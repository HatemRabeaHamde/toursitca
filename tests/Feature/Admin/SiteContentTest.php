<?php

namespace Tests\Feature\Admin;

use App\Models\SiteContent;
use App\Models\User;
use App\Services\SiteContentReader;
use App\Services\SiteContentSanitizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SiteContentTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        Role::findOrCreate('admin', 'web');
        $user = User::factory()->create();
        $user->assignRole('admin');

        return $user;
    }

    private function save(array $fields, string $mode = 'draft'): TestResponse
    {
        return $this->postJson('/admin/site-content/home', compact('fields', 'mode'));
    }

    public function test_defaults_and_published_locale_fallback(): void
    {
        $this->get('/en')->assertOk()->assertSee('Experience it.');
        $this->actingAs($this->admin());
        $this->save(['hero__title_line_1' => ['text' => ['en' => 'English heading', 'fr' => 'Titre français']]], 'publish')->assertRedirect();
        $this->get('/fr')->assertOk()->assertSee('Titre français');
        $this->assertNull(SiteContent::firstOrFail()->getRawOriginal('draft_value'));
        $this->get('/en?content_preview=1')->assertSee('English heading');
        $this->get('/admin/site-content/home')->assertSee('English heading');
        $this->get('/nl')->assertOk()->assertSee('English heading');
        $this->save(['hero__title_line_1' => ['text' => ['en' => 'Replacement']]], 'publish')->assertRedirect();
        $this->get('/fr')->assertSee('Replacement')->assertDontSee('Titre français');
    }

    public function test_draft_preview_is_admin_only(): void
    {
        $this->actingAs($this->admin());
        $this->save(['hero__title_line_1' => ['text' => ['en' => 'Draft heading']]])->assertRedirect();
        $this->get('/en')->assertDontSee('Draft heading');
        $this->get('/en?content_preview=1')->assertOk()->assertSee('Draft heading')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        auth()->logout();
        $this->get('/en?content_preview=1')->assertForbidden();
    }

    public function test_non_admin_cannot_access_any_editor_route(): void
    {
        $this->actingAs(User::factory()->create());
        foreach (['/admin/site-content', '/admin/site-content/home'] as $url) {
            $this->get($url)->assertForbidden();
        }
        $this->save([])->assertForbidden();
        $this->post('/admin/site-content/home/revisions/1')->assertForbidden();
    }

    public function test_validation_and_revision_restore_and_cache(): void
    {
        $this->actingAs($this->admin());
        $this->save(['unknown' => ['text' => ['en' => 'bad']]])->assertUnprocessable();
        $this->save(['hero__title_line_1' => ['text' => ['en' => str_repeat('x', 1001)]]])->assertUnprocessable();
        $this->save(['hero__image' => ['upload' => UploadedFile::fake()->create('bad.svg', 1, 'image/svg+xml')]])->assertUnprocessable();
        $this->save(['hero__title_line_1' => ['text' => ['en' => 'First']]], 'publish');
        $this->get('/en')->assertSee('First');
        $this->assertTrue(Cache::has('site-content:home'));
        $this->save(['hero__title_line_1' => ['text' => ['en' => 'Second']]], 'publish');
        $this->assertFalse(Cache::has('site-content:home'));
        $row = SiteContent::firstOrFail();
        $revision = $row->revisions()->get()->first(fn ($r) => ($r->value['en'] ?? '') === 'First');
        $this->post('/admin/site-content/home/revisions/'.$revision->id)->assertRedirect();
        $this->get('/en?content_preview=1')->assertSee('First');
        $this->get('/en')->assertSee('Second');
        $this->get('/admin/site-content/home')->assertOk()->assertSee('Revision history');
    }

    public function test_sanitizer_strips_active_content_and_attributes(): void
    {
        $html = app(SiteContentSanitizer::class)->clean('<script>alert(1)</script><strong onclick="bad()">Safe</strong><a href="javascript:bad()">link</a><img src=x onerror=bad()>');
        $this->assertSame('<strong>Safe</strong><a>link</a>', $html);
    }

    public function test_image_media_alt_and_draft_publish_isolation(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());
        $this->save(['hero__image' => ['upload' => UploadedFile::fake()->image('photo.jpg'), 'alt' => ['en' => 'Sunset', 'fr' => 'Soir']]])->assertRedirect();
        $row = SiteContent::firstOrFail();
        $this->assertCount(1, $row->media);
        $this->assertSame('Soir', $row->getTranslations('draft_value')['fr']['alt']);
        $reader = app(SiteContentReader::class);
        $this->assertSame('Sunset', $reader->read('home', 'en', true)->get('hero.image')['alt']);
        $this->assertSame('Marrakech at dusk', $reader->read('home', 'en')->get('hero.image')['alt']);
        $this->assertGreaterThan(0, $row->revisions()->count());
    }

    public function test_rich_text_publishes_sanitized_and_reset_returns_defaults(): void
    {
        $this->actingAs($this->admin());
        $this->save(['hero__description' => ['text' => ['en' => '<strong>Safe copy</strong><script>attack()</script>']]], 'publish')->assertRedirect();
        $this->get('/en')->assertSee('<strong>Safe copy</strong>', false)->assertDontSee('attack()');
        $this->save(['hero__description' => ['reset' => true]], 'publish')->assertRedirect();
        $this->get('/en')->assertDontSee('Safe copy')->assertSee('Real places');
        $this->save(['hero__film_url' => ['text' => ['en' => 'javascript:alert(1)']]])->assertUnprocessable();
    }

    public function test_seo_and_nested_defaults_and_foreign_revision_scope(): void
    {
        $this->actingAs($this->admin());
        $this->save(['seo__title' => ['text' => ['en' => 'Home SEO']], 'people__trust__verified__title' => ['text' => ['en' => 'Managed trust label']]], 'publish')->assertRedirect();
        $this->get('/en')->assertSee('<title>Home SEO</title>', false)->assertSee('Managed trust label');
        $this->post('/admin/site-content/unknown/revisions/1')->assertNotFound();
        $this->get('/admin/site-content/unknown')->assertNotFound();
        $this->save(['hero__title_line_1' => ['text' => ['ar' => 'invalid locale']]])->assertUnprocessable();
    }

    public function test_search_identity_and_answer_fields_publish_safely(): void
    {
        $this->actingAs($this->admin());
        $this->save([
            'seo__robots' => ['text' => ['en' => 'noindex, follow']],
            'seo__social_title' => ['text' => ['en' => 'Shared headline']],
            'geo__name' => ['text' => ['en' => 'Tourstica operator']],
            'geo__instagram' => ['text' => ['en' => 'https://instagram.com/tourstica']],
            'aeo__summary' => ['text' => ['en' => 'Browse experiences offered by local agencies.']],
        ], 'draft')->assertRedirect();
        $this->get('/en')->assertDontSee('Shared headline');
        $this->get('/en?content_preview=1')->assertSee('Shared headline')->assertSee('noindex, nofollow');
        $this->save([], 'publish')->assertRedirect();
        $response = $this->get('/en')->assertOk()->assertSee('noindex, follow')->assertSee('Shared headline')->assertSee('Tourstica operator')->assertSee('Browse experiences offered by local agencies.');
        $response->assertSee('rel="canonical"', false)->assertSee('hreflang="nl"', false)->assertSee('hreflang="x-default"', false)->assertSee('sameAs')->assertSee('WebPage');
        $this->save(['seo__robots' => ['text' => ['en' => 'invalid']]])->assertUnprocessable();
        $this->save(['geo__url' => ['text' => ['en' => '/internal']]])->assertUnprocessable();
    }

    public function test_visual_canvas_is_only_present_in_admin_preview(): void
    {
        $this->get('/en?content_edit=1')->assertDontSee('window.siteContentCanvas', false);
        $this->get('/en?content_preview=1&content_edit=1')->assertForbidden();
        $this->actingAs($this->admin());
        $this->get('/admin/site-content/home')->assertOk()->assertSee('Editable homepage preview')->assertSee('visual-editor.js');
        $this->get('/en?content_preview=1&content_edit=1')->assertOk()->assertSee('window.siteContentCanvas', false)->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_landing_header_footer_and_links_are_editable_without_affecting_other_pages(): void
    {
        $this->actingAs($this->admin());
        $this->save([
            'nav__experiences' => ['text' => ['en' => 'Browse our tours']],
            'footer__tagline' => ['text' => ['en' => 'A managed footer message']],
            'footer__link_privacy' => ['text' => ['en' => 'https://example.com/privacy']],
            'hero__link_url' => ['text' => ['en' => '/en/experiences?sort=price_asc']],
        ], 'publish')->assertRedirect();
        $this->get('/en')->assertSee('Browse our tours')->assertSee('A managed footer message')->assertSee('https://example.com/privacy')->assertSee('/en/experiences?sort=price_asc', false);
        $this->get('/en/experiences')->assertOk()->assertDontSee('A managed footer message')->assertDontSee('Browse our tours');
    }

    public function test_each_hero_slide_has_an_independent_image_override_and_reset(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());
        $this->save([
            'hero__slide_2_image' => ['upload' => UploadedFile::fake()->image('slide-two.jpg'), 'alt' => ['en' => 'Second slide photo']],
            'hero__slide_2_label' => ['text' => ['en' => 'Landing destination']],
            'hero__slide_2_url' => ['text' => ['en' => '/en/experiences']],
        ])->assertRedirect();
        $row = SiteContent::where('key', 'slide_2_image')->firstOrFail();
        $url = $row->getTranslations('draft_value')['en']['url'];
        $this->get('/en')->assertDontSee('Second slide photo');
        $this->get('/en?content_preview=1')->assertSee('Second slide photo')->assertSee($url, false)->assertSee('Landing destination');
        $this->save([], 'publish')->assertRedirect();
        $this->get('/en')->assertSee('Second slide photo')->assertSee('data-content-key="hero.slide_2_image"', false);
        $this->save(['hero__slide_2_image' => ['reset' => true]], 'publish')->assertRedirect();
        $this->get('/en')->assertDontSee($url, false);
        $this->assertDatabaseCount('media', 1);
    }

    public function test_admin_workspace_exposes_search_shortcuts_and_mobile_navigation(): void
    {
        $this->actingAs($this->admin())->get('/admin/site-content')->assertOk()->assertSee('Open page editor')->assertSee('admin-workspace.css')->assertSee('admin-sidebar')->assertSee('Publisher identity');
        $this->get('/admin/site-content/home?field=hero.slide_3_image')->assertOk()->assertSee('Slide 3 image')->assertSee('Footer background artwork')->assertSee('Logo light');
    }
}
