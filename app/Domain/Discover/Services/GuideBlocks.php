<?php

namespace App\Domain\Discover\Services;

use App\Domain\Discover\Models\GuideBlock;
use Illuminate\Database\Eloquent\Model;

/** Loads a guide's blocks in one query, grouped by type. */
final class GuideBlocks
{
    /** @return array<string, list<array{title: ?string, body: ?string, image: ?string}>> */
    public function for(Model $guide): array
    {
        $locale = app()->getLocale();

        return GuideBlock::query()
            ->whereMorphedTo('guideable', $guide)
            ->active()
            ->sorted()
            ->get()
            ->groupBy('type')
            ->map(fn ($blocks) => $blocks->map(fn (GuideBlock $block): array => [
                'title' => $block->getTranslation('title', $locale, true) ?: null,
                'body' => $block->getTranslation('body', $locale, true) ?: null,
                'image' => $block->image_url,
            ])->values()->all())
            ->all();
    }
}
