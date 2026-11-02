<?php

namespace App\Domain\Experience\Services;

use App\Domain\Agency\Models\Agency;
use App\Domain\Discover\Models\Host;
use App\Domain\Experience\Events\ExperienceReviewChanged;
use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Models\ExperienceChangeRequest;
use App\Models\User;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

final class ExperienceReviewService
{
    public function __construct(
        private readonly DatabaseManager $database,
        private readonly Dispatcher $events,
        private readonly ExperienceDraftService $drafts,
        private readonly ExperienceItineraryService $itinerary,
    ) {}

    public function submit(Experience $experience, User $actor): ExperienceChangeRequest
    {
        abort_unless($actor->hasRole('admin') || $actor->agency?->id === $experience->agency_id, 403);

        return $this->database->transaction(function () use ($experience, $actor): ExperienceChangeRequest {
            $locked = Experience::query()->lockForUpdate()->findOrFail($experience->id);
            $draft = $locked->changeRequests()->latest('id')->first();
            if ($draft?->status === 'pending') {
                return $draft;
            }
            if ($locked->status === 'published' && (! $draft || $draft->status === 'approved')) {
                throw ValidationException::withMessages(['review' => __('experience_review.no_changes')]);
            }
            $draft = $this->drafts->mutate($locked, fn (array $payload): array => $payload);
            $this->validateContent($draft->payload);
            $draft->forceFill(['status' => 'pending', 'submitted_by' => $actor->id, 'submitted_at' => now()])->save();
            $locked->forceFill(['review_status' => 'pending'])->save();
            $this->database->connection()->afterCommit(fn () => $this->events->dispatch(new ExperienceReviewChanged($draft)));

            return $draft;
        });
    }

    public function decide(ExperienceChangeRequest $request, User $actor, string $decision, ?string $reason): Experience
    {
        abort_unless($actor->hasRole('admin'), 403);

        return $this->database->transaction(function () use ($request, $actor, $decision, $reason): Experience {
            $experience = Experience::query()->lockForUpdate()->findOrFail($request->experience_id);
            $locked = ExperienceChangeRequest::query()->lockForUpdate()->findOrFail($request->id);
            if ($locked->status !== 'pending' || $locked->base_version !== $experience->published_version) {
                throw ValidationException::withMessages(['review' => __('experience_review.stale')]);
            }
            if (! in_array($decision, ['approved', 'rejected', 'changes_requested'], true)) {
                throw ValidationException::withMessages(['decision' => __('validation.in', ['attribute' => 'decision'])]);
            }
            if ($decision !== 'approved' && blank($reason)) {
                throw ValidationException::withMessages(['reason' => __('experience_review.reason_required')]);
            }
            if ($decision === 'approved') {
                $this->apply($experience, $locked->payload);
                $experience->forceFill(['status' => 'published', 'published_version' => $experience->published_version + 1]);
            }
            $experience->forceFill(['review_status' => $decision, 'reviewed_by' => $actor->id, 'reviewed_at' => now()])->save();
            $locked->forceFill(['status' => $decision, 'reason' => $reason, 'reviewed_by' => $actor->id, 'reviewed_at' => now()])->save();
            $this->database->connection()->afterCommit(fn () => $this->events->dispatch(new ExperienceReviewChanged($locked)));

            return $experience->refresh();
        });
    }

    public function withdraw(Experience $experience, User $actor): void
    {
        abort_unless($actor->hasRole('admin') || $actor->agency?->id === $experience->agency_id, 403);
        $this->database->transaction(function () use ($experience): void {
            $locked = Experience::query()->lockForUpdate()->findOrFail($experience->id);
            $draft = $locked->changeRequests()->latest('id')->first();
            if ($draft?->status !== 'pending') {
                throw ValidationException::withMessages(['review' => __('experience_review.stale')]);
            }
            $draft->forceFill(['status' => 'draft', 'submitted_at' => null])->save();
            $locked->forceFill(['review_status' => 'draft'])->save();
            $this->database->connection()->afterCommit(fn () => $this->events->dispatch(new ExperienceReviewChanged($draft)));
        });
    }

    private function validateContent(array $payload): void
    {
        $agency = Agency::query()->find($payload['attributes']['agency_id']);
        if ($agency?->status !== 'active') {
            throw ValidationException::withMessages(['review' => __('ui.messages.booking_slot_unavailable')]);
        }
        if (filled($payload['attributes']['host_id'] ?? null)) {
            $host = Host::query()->find($payload['attributes']['host_id']);
            if (! $host?->is_active || $host->agency_id !== $agency->id) {
                throw ValidationException::withMessages(['review' => __('ui.messages.booking_slot_unavailable')]);
            }
        }

        if (blank(data_get($payload, 'attributes.title.en')) || blank(data_get($payload, 'attributes.description.en'))
            || (blank(data_get($payload, 'attributes.thumbnail')) && ! collect($payload['media'])->contains('type', 'image'))) {
            throw ValidationException::withMessages(['review' => __('experience_review.incomplete')]);
        }
    }

    private function apply(Experience $experience, array $payload): void
    {
        $this->validateContent($payload);
        if ($experience->bookings()->exists() && (int) $payload['attributes']['agency_id'] !== $experience->agency_id) {
            throw ValidationException::withMessages(['review' => __('experience_review.owner_locked')]);
        }
        $experience->fill($payload['attributes']);
        $this->itinerary->sync($experience, $payload['itinerary']);
        $ids = array_column($payload['options'], 'id');
        foreach ($experience->options()->whereNotIn('id', $ids)->get() as $option) {
            if ($option->availabilities()->exists()) {
                throw ValidationException::withMessages(['review' => __('ui.messages.option_has_availability')]);
            }
            $option->delete();
        }
        foreach ($payload['options'] as $row) {
            $option = $experience->options()->findOrFail($row['id']);
            $option->fill(Arr::except($row['attributes'], ['experience_id']))->save();
            $option->prices()->delete();
            $option->languages()->delete();
            foreach ($row['prices'] as $price) {
                $option->prices()->create(Arr::only($price, ['participant_type', 'min_age', 'max_age', 'price', 'original_price', 'currency']));
            }
            foreach ($row['languages'] as $language) {
                $option->languages()->create(Arr::only($language, ['language_code', 'type']));
            }
        }
        foreach ($payload['media'] as $media) {
            if (! isset($media['id'])) {
                $experience->media()->create(Arr::only($media, ['type', 'source_type', 'path', 'sort_order']));
            }
        }
    }
}
