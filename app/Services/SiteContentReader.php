<?php

namespace App\Services;

use App\Models\SiteContent;
use App\ViewModels\SiteContentPage;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Database\Schema\Builder;

final class SiteContentReader
{
    public function __construct(private SiteContentRegistry $registry, private Repository $cache, private Builder $schema, private SiteContentSanitizer $sanitizer) {}

    public function read(string $page, string $locale, bool $preview = false): SiteContentPage
    {
        $load = fn () => SiteContent::where('page', $page)->get()->mapWithKeys(fn ($row) => [$row->section.'.'.$row->key => [
            'value' => $row->getTranslations('value'), 'draft' => $row->getTranslations('draft_value'),
            'has_draft' => $row->getRawOriginal('draft_value') !== null,
        ]])->all();
        $rows = $this->schema->hasTable('site_contents') ? ($preview ? $load() : $this->cache->remember('site-content:'.$page, 3600, $load)) : [];
        $values = $this->registry->defaults($page, $locale);
        foreach ($rows as $key => $row) {
            $translations = $preview && $row['has_draft'] ? $row['draft'] : $row['value'];
            $values[$key] = $translations[$locale] ?? $translations['en'] ?? $values[$key] ?? '';
        }

        $types = [];
        foreach ($this->registry->fields($page) as $key => $field) {
            $types[$key] = $field['type'];
            if ($field['type'] === 'richtext') {
                $values[$key] = $this->sanitizer->clean($values[$key]);
            }
        }

        return new SiteContentPage($page, $values, $preview, $types);
    }
}
