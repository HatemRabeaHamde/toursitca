<?php

namespace App\Http\Requests\Admin;

use App\DTOs\SiteContentUpdateData;
use App\Models\SiteContent;
use App\Services\SiteContentRegistry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class SiteContentRequest extends FormRequest
{
    public function toData(): SiteContentUpdateData
    {
        return new SiteContentUpdateData((string) $this->route('page'), $this->validated('mode'), $this->validated('fields', []), $this->user()->id, $this->sections());
    }

    private function sections(): ?array
    {
        if ($panel = $this->route('panel')) {
            return (config('site_content.panels.'.$panel) ?? abort(404))['sections'];
        }
        if ($this->input('context') === 'canvas') {
            $sections = array_keys(config('site_content.pages.'.$this->route('page').'.sections', []));
            $settings = collect(config('site_content.panels'))->pluck('sections')->flatten()->all();

            return array_values(array_diff($sections, $settings));
        }

        return null;
    }

    public function successMessage(): string
    {
        $panel = $this->route('panel');
        if (! $panel) {
            return $this->input('mode') === 'publish' ? 'Content published.' : 'Draft saved. The public site has not changed.';
        }

        return __('content.'.($this->input('mode') === 'publish' ? 'section_published' : 'section_draft_saved'), ['section' => config('site_content.panels.'.$panel.'.title')]);
    }

    public function authorize(): bool
    {
        return $this->user()?->can('manage', SiteContent::class) ?? false;
    }

    public function rules(SiteContentRegistry $registry): array
    {
        $fields = $registry->fields((string) $this->route('page', 'home'));
        if ($sections = $this->sections()) {
            $fields = array_filter($fields, fn ($field, $key) => in_array(explode('.', $key)[0], $sections, true), ARRAY_FILTER_USE_BOTH);
        }
        $rules = ['context' => ['sometimes', Rule::in(['canvas'])], 'mode' => ['required', Rule::in(['draft', 'publish'])], 'fields' => ['sometimes', 'array:'.implode(',', array_map(fn ($k) => str_replace('.', '__', $k), array_keys($fields)))]];
        foreach ($fields as $key => $field) {
            $base = 'fields.'.str_replace('.', '__', $key);
            $rules[$base] = ['sometimes', 'array:text,upload,alt,remove,reset'];
            $rules[$base.'.reset'] = ['sometimes', 'boolean'];
            $rules[$base.'.remove'] = ['sometimes', 'boolean'];
            if ($field['type'] === 'image') {
                $rules[$base.'.upload'] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:'.$field['max']];
                $rules[$base.'.alt'] = ['sometimes', 'array:en,fr,nl'];
                foreach (['en', 'fr', 'nl'] as $locale) {
                    $rules[$base.'.alt.'.$locale] = ['nullable', 'string', 'max:250'];
                }
                $rules[$base.'.text'] = ['prohibited'];
            } else {
                $rules[$base.'.upload'] = ['prohibited'];
                $rules[$base.'.alt'] = ['prohibited'];
                $rules[$base.'.text'] = ['sometimes', 'array:en,fr,nl'];
                foreach (['en', 'fr', 'nl'] as $locale) {
                    $rules[$base.'.text.'.$locale] = ['nullable', 'string', 'max:'.$field['max']];
                    $rules[$base.'.text.'.$locale] = array_merge($rules[$base.'.text.'.$locale], $field['validation'] ?? []);
                    if (isset($field['allowed'])) {
                        $rules[$base.'.text.'.$locale][] = Rule::in($field['allowed']);
                    }
                    if (! empty($field['absolute'])) {
                        $rules[$base.'.text.'.$locale][] = 'url:http,https';
                    }
                    if ($field['type'] === 'link') {
                        $rules[$base.'.text.'.$locale][] = 'regex:~^(https?://|/[^/]|#[a-zA-Z0-9_-])~';
                    }
                }
            }
        }

        return $rules;
    }
}
