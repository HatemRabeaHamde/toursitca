<?php

namespace App\Services;

use App\Models\SiteContent;

final class SiteContentEditorData
{
    public function __construct(private SiteContentRegistry $registry) {}

    public function get(string $page, ?array $sections = null): array
    {
        $definition = $this->registry->page($page);
        $rows = SiteContent::with(['revisions' => fn ($q) => $q->latest('id')->limit(20)])->where('page', $page)->when($sections, fn ($query) => $query->whereIn('section', $sections))->get()->keyBy(fn ($r) => $r->section.'.'.$r->key);
        $fields = [];
        $defaults = collect(['en', 'fr', 'nl'])->mapWithKeys(fn ($locale) => [$locale => $this->registry->defaults($page, $locale)])->all();
        foreach ($this->registry->fields($page) as $key => $field) {
            if ($sections && ! in_array(explode('.', $key)[0], $sections, true)) {
                continue;
            }
            $row = $rows->get($key);
            $translations = $row ? ($row->getRawOriginal('draft_value') !== null ? $row->getTranslations('draft_value') : $row->getTranslations('value')) : [];
            $fields[$key] = [...$field, 'key' => $key, 'encoded' => str_replace('.', '__', $key), 'translations' => $translations, 'defaults' => array_map(fn ($values) => $values[$key], $defaults), 'revisions' => $row?->revisions ?? collect()];
        }

        return compact('page', 'definition', 'fields');
    }
}
