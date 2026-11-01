<?php

namespace App\Services;

use App\Events\SiteContentChanged;
use App\Models\SiteContent;
use App\Models\SiteContentRevision;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\DatabaseManager;

final class SiteContentWriter
{
    public function __construct(private SiteContentRegistry $registry, private SiteContentSanitizer $sanitizer, private DatabaseManager $db, private Repository $cache, private Dispatcher $events) {}

    public function save(string $page, array $data, int $userId): void
    {
        $this->db->transaction(function () use ($page, $data, $userId): void {
            foreach ($data['fields'] ?? [] as $encoded => $input) {
                $path = str_replace('__', '.', $encoded);
                [$section,$key] = explode('.', $path, 2);
                $field = $this->registry->fields($page)[$path];
                $row = SiteContent::firstOrCreate(['page' => $page, 'section' => $section, 'key' => $key], ['type' => $field['type']]);
                $row = SiteContent::whereKey($row->id)->lockForUpdate()->firstOrFail();
                $value = $this->value($row, $field, $input);
                $this->revision($row, $userId);
                $row->setTranslations('draft_value', []);
                $row->setTranslations('draft_value', $value);
                $row->updated_by = $userId;
                $row->save();
            }
            if ($data['mode'] === 'publish') {
                foreach (SiteContent::where('page', $page)->lockForUpdate()->get() as $row) {
                    if ($row->getRawOriginal('draft_value') === null) {
                        continue;
                    }
                    $this->revision($row, $userId);
                    $row->setTranslations('value', []);
                    $row->setTranslations('value', $row->getTranslations('draft_value'));
                    $row->setRawAttributes(array_replace($row->getAttributes(), ['draft_value' => null]));
                    $row->updated_by = $userId;
                    $row->save();
                }
            }
        });
        $this->cache->forget('site-content:'.$page);
        $this->events->dispatch(new SiteContentChanged($page, $userId, $data['mode']));
    }

    public function restore(string $page, int $revisionId, int $userId): void
    {
        $this->db->transaction(function () use ($page, $revisionId, $userId): void {
            $revision = SiteContentRevision::findOrFail($revisionId);
            $row = SiteContent::where('page', $page)->whereKey($revision->site_content_id)->lockForUpdate()->firstOrFail();
            $this->revision($row, $userId);
            $row->setTranslations('draft_value', []);
            $row->setTranslations('draft_value', $revision->value ?? []);
            $row->updated_by = $userId;
            $row->save();
        });
        $this->cache->forget('site-content:'.$page);
        $this->events->dispatch(new SiteContentChanged($page, $userId, 'restore'));
    }

    private function revision(SiteContent $row, int $userId): void
    {
        $row->revisions()->create(['value' => $row->getRawOriginal('draft_value') !== null ? $row->getTranslations('draft_value') : $row->getTranslations('value'), 'changed_by' => $userId]);
    }

    private function value(SiteContent $row, array $field, array $input): array
    {
        if (! empty($input['reset'])) {
            return [];
        }
        if ($field['type'] !== 'image') {
            return collect($input['text'] ?? [])->filter(fn ($v) => $v !== null && $v !== '')->map(fn ($v) => $field['type'] === 'richtext' ? $this->sanitizer->clean($v) : $v)->all();
        }
        if (! empty($input['remove'])) {
            return ['en' => ['url' => '', 'alt' => '']];
        }
        $value = $row->getRawOriginal('draft_value') !== null ? $row->getTranslations('draft_value') : $row->getTranslations('value');
        $url = $value['en']['url'] ?? $this->registry->defaults($row->page, 'en')[$row->section.'.'.$row->key]['url'];
        if (isset($input['upload'])) {
            $media = $row->addMedia($input['upload'])->toMediaCollection('image');
            $url = $media->getUrl('webp');
        }
        foreach (['en', 'fr', 'nl'] as $locale) {
            $value[$locale] = ['url' => $url, 'alt' => ($input['alt'][$locale] ?? '') ?: ($input['alt']['en'] ?? '')];
        }

        return $value;
    }
}
