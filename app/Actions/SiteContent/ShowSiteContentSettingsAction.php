<?php

namespace App\Actions\SiteContent;

use App\Models\SiteContent;
use App\Services\SiteContentEditorData;
use App\Services\SiteContentMetadata;
use App\Services\SiteContentReader;
use Illuminate\View\View;

final class ShowSiteContentSettingsAction
{
    public function __construct(private SiteContentEditorData $editor, private SiteContentReader $reader, private SiteContentMetadata $metadata) {}

    public function execute(string $page, string $panel): View
    {
        $settings = config('site_content.panels.'.$panel) ?? abort(404);
        $data = $this->editor->get($page, $settings['sections']);
        $data['fields'] = array_filter($data['fields'], fn ($field) => in_array(explode('.', $field['key'])[0], $settings['sections'], true));
        $data['definition']['sections'] = array_intersect_key($data['definition']['sections'], array_flip($settings['sections']));
        $draft = $this->reader->read($page, 'en', true);
        $data['metadata'] = $this->metadata->build($draft, 'en');
        $data['seo'] = ['title' => $settings['title'].' | Tourstica admin', 'robots' => 'noindex, nofollow'];
        $schemas = collect(['en', 'fr', 'nl'])->mapWithKeys(fn ($locale) => [$locale => $this->metadata->build($this->reader->read($page, $locale, true), $locale)['schema']])->all();
        $data['previewData'] = ['urls' => $data['metadata']['hreflang'], 'schemas' => $schemas, 'fields' => collect($data['fields'])->map(fn ($field) => $field['translations'])->all(), 'defaults' => collect(['en', 'fr', 'nl'])->mapWithKeys(fn ($locale) => [$locale => collect($data['fields'])->map(fn ($field) => $field['defaults'][$locale])->all()])->all()];
        $data['hasDraft'] = SiteContent::where('page', $page)->whereIn('section', $settings['sections'])->whereNotNull('draft_value')->exists();
        $groups = $settings['groups'] ?? [];
        $data['groups'] = collect($groups)->map(fn ($keys) => array_intersect_key($data['fields'], array_flip($keys)))->all();

        $groupedKeys = collect($groups)->flatten()->all();
        $remaining = array_diff_key($data['fields'], array_flip($groupedKeys));
        if ($remaining) {
            $data['groups']['additional'] = $remaining;
        }

        return view('admin.site-content.settings', [...$data, 'panel' => $panel, 'settings' => $settings]);
    }
}
