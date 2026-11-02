<?php

namespace Tests\Feature\Experience;

use App\Domain\Agency\Models\Agency;
use App\Domain\Booking\Models\Booking;
use App\Domain\Checkout\Models\CheckoutSession;
use App\Domain\Discover\Models\Host;
use App\Domain\Experience\Actions\AttachExperienceMediaAction;
use App\Domain\Experience\Actions\SaveExperienceOptionAction;
use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Models\ExperienceChangeRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ExperienceReviewWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_agency_submits_and_only_admin_can_publish(): void
    {
        [$owner, $admin, $experience] = $this->records('draft');
        $this->actingAs($owner)->post(route('agency.experiences.publish', $experience))->assertSessionHasNoErrors();
        $this->assertSame('draft', $experience->fresh()->status);
        $this->assertSame('pending', $experience->fresh()->review_status);
        $review = ExperienceChangeRequest::query()->firstOrFail();
        $this->get(route('site.experiences.show', ['locale' => 'en', 'experience' => $experience->slug]))->assertNotFound();
        $this->patch(route('admin.experience-reviews.update', $review), ['decision' => 'approved'])->assertForbidden();
        $this->actingAs($admin)->get(route('admin.experience-reviews.show', $review))->assertOk()->assertSee('Original trip');
        $this->patch(route('admin.experience-reviews.update', $review), ['decision' => 'approved'])->assertSessionHasNoErrors();
        $this->assertSame('published', $experience->fresh()->status);
        $this->assertSame($admin->id, $experience->fresh()->reviewed_by);
        $this->assertSame('approved', $review->fresh()->status);
    }

    public function test_published_edits_options_and_media_stay_private_until_approval(): void
    {
        [$owner, $admin, $experience] = $this->records();
        $this->actingAs($owner)->patch(route('agency.experiences.update', $experience), $this->payload('Edited trip'))->assertSessionHasNoErrors();
        $this->assertSame('Original trip', $experience->fresh()->getTranslation('title', 'en'));
        $option = app(SaveExperienceOptionAction::class)->execute($experience, [
            'title' => ['en' => 'New package'], 'status' => 'active', 'price_type' => 'per_person',
        ], [['participant_type' => 'adult', 'price' => '250.00', 'currency' => 'MAD']], []);
        app(AttachExperienceMediaAction::class)->execute($experience, [], null, 'https://example.test/qa-video');
        $this->assertSame('inactive', $option->fresh()->status);
        $this->assertSame(0, $option->prices()->count());
        $this->assertSame(0, $experience->videos()->count());
        $this->get(route('agency.experiences.edit', $experience))->assertOk()->assertSee('Edited trip');
        $this->get(route('agency.experiences.options.index', $experience))->assertOk()->assertSee('New package');
        $this->post(route('agency.experiences.review.store', $experience))->assertSessionHasNoErrors();
        $review = $experience->changeRequests()->latest('id')->firstOrFail();
        $this->patch(route('agency.experiences.update', $experience), $this->payload('Unreviewed change'))->assertSessionHasErrors('review');
        $this->actingAs($admin)->patch(route('admin.experience-reviews.update', $review), ['decision' => 'approved'])->assertSessionHasNoErrors();
        $this->assertSame('Edited trip', $experience->fresh()->getTranslation('title', 'en'));
        $this->assertSame('active', $option->fresh()->status);
        $this->assertSame('250.00', $option->prices()->first()->price);
        $this->assertSame(1, $experience->videos()->count());
        $this->get(route('site.experiences.show', ['locale' => 'en', 'experience' => $experience->slug]))->assertOk()->assertSee('Edited trip');
        $this->patch(route('admin.experience-reviews.update', $review), ['decision' => 'approved'])->assertSessionHasErrors('review');
    }

    public function test_rejection_requires_reason_and_resubmission_preserves_history(): void
    {
        [$owner, $admin, $experience] = $this->records('draft');
        $this->actingAs($owner)->post(route('agency.experiences.review.store', $experience));
        $review = ExperienceChangeRequest::query()->firstOrFail();
        $this->actingAs($admin)->patch(route('admin.experience-reviews.update', $review), ['decision' => 'changes_requested'])->assertSessionHasErrors('reason');
        $this->patch(route('admin.experience-reviews.update', $review), ['decision' => 'changes_requested', 'reason' => 'Clarify the meeting point.'])->assertSessionHasNoErrors();
        $this->actingAs($owner)->get(route('agency.experiences.edit', $experience))->assertSee('Clarify the meeting point.');
        $this->patch(route('agency.experiences.update', $experience), $this->payload('Revised trip'))->assertSessionHasNoErrors();
        $this->post(route('agency.experiences.review.store', $experience))->assertSessionHasNoErrors();
        $this->assertSame('changes_requested', $review->fresh()->status);
        $this->assertDatabaseCount('experience_change_requests', 2);
    }

    public function test_other_agency_cannot_submit_or_withdraw_and_pending_must_be_withdrawn_before_editing(): void
    {
        [$owner, $admin, $experience] = $this->records('draft');
        [$other] = $this->records('draft', 'second');
        $this->actingAs($other)->post(route('agency.experiences.review.store', $experience))->assertForbidden();
        $this->delete(route('agency.experiences.review.destroy', $experience))->assertForbidden();
        $this->actingAs($owner)->post(route('agency.experiences.review.store', $experience));
        $this->delete(route('agency.experiences.review.destroy', $experience))->assertSessionHasNoErrors();
        $this->assertSame('draft', $experience->fresh()->review_status);
        $this->patch(route('agency.experiences.update', $experience), $this->payload('Editable again'))->assertSessionHasNoErrors();
    }

    public function test_crafted_status_on_create_cannot_publish_and_incomplete_draft_cannot_submit(): void
    {
        [$owner, , $experience] = $this->records('draft');
        $this->actingAs($owner)->post(route('agency.experiences.store'), $this->payload('New draft') + ['status' => 'published'])->assertSessionHasNoErrors();
        $created = Experience::where('slug', 'new-draft')->firstOrFail();
        $this->assertSame('draft', $created->status);
        $this->post(route('agency.experiences.review.store', $created))->assertSessionHasErrors('review');
        $this->assertSame('draft', $created->fresh()->status);
    }

    public function test_private_draft_preview_uses_the_real_page_without_enabling_checkout(): void
    {
        [$owner, $admin, $experience] = $this->records('draft');
        $this->actingAs($owner)->get(route('agency.experiences.draft-preview', $experience))->assertOk()
            ->assertSee('Original trip')->assertSee('noindex, nofollow')->assertViewHas('page', fn ($page): bool => $page->booking['store']['preview'] === true);
        $this->actingAs(User::factory()->create())->get(route('admin.experiences.draft-preview', $experience))->assertForbidden();
        $this->actingAs($admin)->get(route('admin.experiences.draft-preview', $experience))->assertOk();
        $this->get(route('site.experiences.show', ['locale' => 'en', 'experience' => $experience->slug]))->assertNotFound();
    }

    public function test_full_agency_admin_traveller_booking_chain_and_immutable_selection(): void
    {
        [$owner, $admin, $experience] = $this->records('draft');
        $host = Host::create(['agency_id' => $experience->agency_id, 'name' => 'Original host', 'role' => ['en' => 'Local guide'], 'slug' => 'original-host', 'is_active' => true]);
        $experience->update(['host_id' => $host->id]);
        $option = app(SaveExperienceOptionAction::class)->execute($experience, [
            'title' => ['en' => 'Family package'], 'status' => 'active', 'price_type' => 'per_person', 'cancellation_hours' => 24,
        ], [
            ['participant_type' => 'adult', 'price' => '100.00', 'currency' => 'MAD'],
            ['participant_type' => 'child', 'min_age' => 0, 'max_age' => 12, 'price' => '60.00', 'currency' => 'MAD'],
        ], []);
        $slot = $experience->availabilities()->create(['experience_option_id' => $option->id, 'date' => now()->addWeek()->toDateString(), 'time_slot' => '09:00', 'max_seats' => 6, 'booked_seats' => 0, 'held_seats' => 0, 'is_active' => true]);
        $this->actingAs($owner)->post(route('agency.experiences.review.store', $experience))->assertSessionHasNoErrors();
        $review = $experience->changeRequests()->latest('id')->firstOrFail();
        $this->actingAs($admin)->patch(route('admin.experience-reviews.update', $review), ['decision' => 'approved'])->assertSessionHasNoErrors();
        $client = User::factory()->create();
        $client->assignRole(Role::findOrCreate('user', 'web'));
        $selection = ['experience_id' => $experience->id, 'option_id' => $option->id, 'availability_id' => $slot->id, 'booking_type' => 'group', 'participants' => ['adult' => 2, 'child' => 1], 'child_ages' => [7]];
        $this->actingAs($client)->postJson(route('site.experiences.quote', ['locale' => 'en', 'experience' => $experience->slug]), $selection)->assertOk()->assertJsonPath('data.total', '260.00');
        $this->postJson(route('site.checkout.start', ['locale' => 'en']), $selection)->assertCreated();
        $session = CheckoutSession::query()->firstOrFail();
        $this->get(route('site.checkout.contact', ['locale' => 'en', 'checkoutSession' => $session]))->assertOk()->assertSee('Family package')->assertSee('09:00');
        $contact = ['contact_first_name' => 'Test', 'contact_last_name' => 'Traveller', 'contact_email' => $client->email, 'terms' => '1'];
        $this->post(route('site.checkout.contact.store', ['locale' => 'en', 'checkoutSession' => $session]), $contact)->assertSessionHasNoErrors();
        $booking = Booking::query()->firstOrFail();
        $this->assertSame('confirmed', $booking->status);
        $this->assertSame('260.00', $booking->total_price);
        $this->assertSame(3, $slot->fresh()->booked_seats);
        $this->assertSame(0, $slot->fresh()->held_seats);
        $this->assertDatabaseHas('payouts', ['booking_id' => $booking->id, 'amount' => '221.00']);
        $this->post(route('site.checkout.contact.store', ['locale' => 'en', 'checkoutSession' => $session]), $contact)->assertRedirect(route('site.bookings.confirmation', ['locale' => 'en', 'booking' => $booking]));
        $this->assertDatabaseCount('bookings', 1);
        $this->assertDatabaseCount('payouts', 1);
        $host->update(['name' => 'Changed host']);
        $option->update(['title' => ['en' => 'Changed package'], 'cancellation_hours' => 1]);
        foreach (['en', 'fr', 'nl'] as $locale) {
            $this->get(route('site.bookings.confirmation', ['locale' => $locale, 'booking' => $booking]))->assertOk()->assertSee('Original host');
            $this->get(route('site.dashboard.bookings', ['locale' => $locale]))->assertOk()->assertSee('Original host')->assertSee('Family package');
        }
        $this->actingAs($owner)->get(route('agency.bookings.show', $booking))->assertOk()->assertSee('Family package')->assertSee('Original host');
        $this->actingAs($admin)->get(route('admin.bookings.show', $booking))->assertOk()->assertSee('Family package')->assertSee('Original host');
    }

    private function records(string $status = 'published', string $suffix = 'first'): array
    {
        $owner = User::factory()->create();
        $owner->assignRole(Role::findOrCreate('travel_agency', 'web'));
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('admin', 'web'));
        $agency = Agency::create(['user_id' => $owner->id, 'name' => 'Review Agency '.$suffix, 'slug' => 'review-'.$suffix, 'description' => ['en' => 'Agency'], 'commission_rate' => '15.00', 'status' => 'active']);
        $experience = Experience::create($this->payload('Original trip') + ['agency_id' => $agency->id, 'created_by' => $owner->id, 'status' => $status, 'thumbnail' => 'images/landing-v2/hero-marrakech.jpg']);

        return [$owner, $admin, $experience];
    }

    private function payload(string $title): array
    {
        return ['title' => ['en' => $title], 'description' => ['en' => 'Actual test description'], 'category' => 'cultural', 'difficulty' => 'easy', 'duration_hours' => '3.0', 'max_group_size' => 6, 'price_per_person' => '100.00', 'location_city' => 'Marrakesh'];
    }
}
