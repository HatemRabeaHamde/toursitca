<?php

namespace App\Domain\Experience\Services;

use App\Domain\Experience\Models\ExperienceChangeRequest;
use App\Domain\Experience\ViewModels\ExperienceReviewViewModel;
use Illuminate\Support\Str;

final class ExperienceReviewPageService
{
    public function __construct(private readonly ExperienceDraftService $drafts) {}

    public function build(?ExperienceChangeRequest $review = null, string $status = 'pending'): ExperienceReviewViewModel
    {
        if ($review === null) {
            return new ExperienceReviewViewModel(reviews: ExperienceChangeRequest::query()->with('experience.agency')->when($status === 'pending', fn ($query) => $query->where('status', 'pending'))->latest('id')->paginate(15)->withQueryString());
        }
        $review->load('experience.agency');
        $live = $this->drafts->snapshot($review->experience);
        $sections = [];
        foreach (['attributes', 'itinerary', 'options', 'media'] as $section) {
            $before = $this->flatten($live[$section]);
            $after = $this->flatten($review->payload[$section]);
            $rows = [];
            foreach (array_unique([...array_keys($before), ...array_keys($after)]) as $key) {
                $rows[] = ['label' => Str::headline(str_replace('.', ' / ', $key)), 'before' => $before[$key] ?? '—', 'after' => $after[$key] ?? '—', 'changed' => ($before[$key] ?? null) !== ($after[$key] ?? null)];
            }
            $sections[] = [
                'label' => __('experience_review.sections.'.$section),
                'rows' => $rows,
                'before' => json_encode($live[$section], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'after' => json_encode($review->payload[$section], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'changed' => $live[$section] !== $review->payload[$section],
            ];
        }

        return new ExperienceReviewViewModel(review: $review, sections: $sections);
    }

    private function flatten(array $values, string $prefix = ''): array
    {
        $rows = [];
        foreach ($values as $key => $value) {
            if (in_array($key, ['id', 'experience_id', 'experience_option_id', 'created_at', 'updated_at'], true)) {
                continue;
            }
            $label = $prefix === '' ? (string) $key : $prefix.'.'.$key;
            if (is_array($value)) {
                $rows += $this->flatten($value, $label);
            } else {
                $rows[$label] = is_bool($value) ? ($value ? __('experience_review.yes') : __('experience_review.no')) : (string) ($value ?? '—');
            }
        }

        return $rows;
    }
}
