<?php

namespace App\Actions\SiteContent;

use App\Models\SiteContent;
use App\Services\SiteContentRegistry;
use Illuminate\View\View;

final class ShowSiteContentAction
{
    public function __construct(private SiteContentRegistry $registry) {}

    public function execute(?string $page = null): View
    {
        if ($page === null) {
            return view('admin.site-content.index', ['pages' => $this->registry->pages()]);
        }
        $definition = $this->registry->page($page);
        $rows = SiteContent::with(['revisions' => fn ($q) => $q->latest('id')->limit(20)])->where('page', $page)->get()->keyBy(fn ($r) => $r->section.'.'.$r->key);
        $fields = [];
        $defaults = collect(['en', 'fr', 'nl'])->mapWithKeys(fn ($locale) => [$locale => $this->registry->defaults($page, $locale)])->all();
        foreach ($this->registry->fields($page) as $key => $field) {
            $row = $rows->get($key);
            $translations = $row ? ($row->getRawOriginal('draft_value') !== null ? $row->getTranslations('draft_value') : $row->getTranslations('value')) : [];
            $fields[$key] = [...$field, 'key' => $key, 'encoded' => str_replace('.', '__', $key), 'translations' => $translations, 'defaults' => array_map(fn ($values) => $values[$key], $defaults), 'revisions' => $row?->revisions ?? collect()];
        }

        return view('admin.site-content.edit', compact('page', 'definition', 'fields'));
    }
}
