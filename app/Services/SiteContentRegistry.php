<?php

namespace App\Services;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Translation\Translator;

final class SiteContentRegistry
{
    public function __construct(private Repository $config, private Translator $translator) {}

    public function pages(): array
    {
        return $this->config->get('site_content.pages', []);
    }

    public function page(string $page): array
    {
        return $this->pages()[$page] ?? abort(404);
    }

    public function fields(string $page): array
    {
        $result = [];
        foreach ($this->page($page)['sections'] as $section => $definition) {
            foreach ($definition['fields'] as $key => $field) {
                $result[$section.'.'.$key] = $field;
            }
        }

        return $result;
    }

    public function defaults(string $page, string $locale): array
    {
        $result = [];
        foreach ($this->fields($page) as $key => $field) {
            $value = isset($field['lang']) ? $this->translator->get($field['lang'], [], $locale) : $this->config->get($field['config'], '');
            $result[$key] = $field['type'] === 'image' ? ['url' => asset($value), 'alt' => ! empty($field['alt_lang']) ? $this->translator->get($field['alt_lang'], [], $locale) : ''] : $value;
        }

        return $result;
    }
}
