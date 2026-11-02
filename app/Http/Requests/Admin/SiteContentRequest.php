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
        return new SiteContentUpdateData((string) $this->route('page'), $this->validated('mode'), $this->validated('fields', []), $this->user()->id);
    }

    public function authorize(): bool
    {
        return $this->user()?->can('manage', SiteContent::class) ?? false;
    }

    public function rules(SiteContentRegistry $registry): array
    {
        $fields = $registry->fields((string) $this->route('page', 'home'));
        $rules = ['mode' => ['required', Rule::in(['draft', 'publish'])], 'fields' => ['sometimes', 'array:'.implode(',', array_map(fn ($k) => str_replace('.', '__', $k), array_keys($fields)))]];
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
