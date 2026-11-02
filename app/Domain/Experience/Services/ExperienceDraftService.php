<?php

namespace App\Domain\Experience\Services;

use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Models\ExperienceChangeRequest;
use App\Domain\Experience\Models\ExperienceItineraryItem;
use App\Domain\Experience\Models\ExperienceMedia;
use App\Domain\Experience\Models\ExperienceOption;
use App\Domain\Experience\Models\ExperienceOptionLanguage;
use App\Domain\Experience\Models\ExperienceOptionPrice;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

final class ExperienceDraftService
{
    public function __construct(private readonly DatabaseManager $database) {}

    public function snapshot(Experience $experience): array
    {
        $experience->load(['itineraryItems', 'options.prices', 'options.languages', 'media']);

        return [
            'attributes' => Arr::except($experience->attributesToArray(), ['id', 'created_by', 'created_at', 'updated_at', 'deleted_at', 'status', 'review_status', 'published_version', 'reviewed_by', 'reviewed_at', 'rating_avg', 'reviews_count']),
            'itinerary' => $experience->itineraryItems->toArray(),
            'media' => $experience->media->toArray(),
            'options' => $experience->options->map(fn (ExperienceOption $option): array => [
                'id' => $option->id,
                'attributes' => Arr::only($option->attributesToArray(), $option->getFillable()),
                'prices' => $option->prices->toArray(),
                'languages' => $option->languages->toArray(),
            ])->all(),
        ];
    }

    public function mutate(Experience $experience, callable $change): ExperienceChangeRequest
    {
        return $this->database->transaction(function () use ($experience, $change): ExperienceChangeRequest {
            $locked = Experience::query()->lockForUpdate()->findOrFail($experience->id);
            $draft = $locked->changeRequests()->latest('id')->first();
            if ($draft?->status === 'pending') {
                throw ValidationException::withMessages(['review' => __('experience_review.withdraw_first')]);
            }
            if (! $draft || $draft->status !== 'draft') {
                $draft = new ExperienceChangeRequest([
                    'experience_id' => $locked->id,
                    'base_version' => $locked->published_version,
                    'payload' => $draft && in_array($draft->status, ['rejected', 'changes_requested'], true) ? $draft->payload : $this->snapshot($locked),
                ]);
            }
            $draft->payload = $change($draft->payload);
            $draft->save();
            $locked->forceFill(['review_status' => 'draft'])->save();

            return $draft;
        });
    }

    /** Project the private draft into the existing forms, without writing public rows. */
    public function forEditing(Experience $experience): Experience
    {
        $draft = $experience->changeRequests()->latest('id')->first();
        if (! $draft || $draft->status === 'approved') {
            return $experience->load(['media', 'itineraryItems', 'options.prices', 'options.languages']);
        }

        return $this->project($experience, $draft->payload);
    }

    public function project(Experience $experience, array $payload): Experience
    {
        $copy = clone $experience;
        $copy->fill($payload['attributes']);
        $copy->setRelation('media', collect($payload['media'])->map(fn (array $row) => new ExperienceMedia($row)));
        $copy->setRelation('itineraryItems', collect($payload['itinerary'])->map(fn (array $row) => new ExperienceItineraryItem($row)));
        $copy->setRelation('options', collect($payload['options'])->map(function (array $row): ExperienceOption {
            $option = new ExperienceOption($row['attributes']);
            $option->id = $row['id'];
            $option->setRelation('prices', collect($row['prices'])->map(fn (array $price) => new ExperienceOptionPrice($price)));
            $option->setRelation('languages', collect($row['languages'])->map(fn (array $language) => new ExperienceOptionLanguage($language)));

            return $option;
        }));

        return $copy;
    }

    public function stageOption(Experience $experience, array $attributes, array $prices, array $languages, ?ExperienceOption $option): ExperienceOption
    {
        return $this->database->transaction(function () use ($experience, $attributes, $prices, $languages, $option): ExperienceOption {
            Experience::query()->lockForUpdate()->findOrFail($experience->id);
            // A private placeholder gives new draft options a stable, authorized form URL.
            $option ??= $experience->options()->create(array_replace($attributes, ['status' => 'inactive']));
            $this->mutate($experience, function (array $payload) use ($attributes, $prices, $languages, $option): array {
                $payload['options'] = array_values(array_filter($payload['options'], fn (array $row): bool => $row['id'] !== $option->id));
                $payload['options'][] = ['id' => $option->id, 'attributes' => $attributes, 'prices' => $prices, 'languages' => $languages];

                return $payload;
            });

            return $option;
        });
    }
}
