<?php

namespace App\Http\Requests\Site;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AvailabilityQuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booking_type' => ['required', Rule::in(['group', 'private'])],
            'participants_count' => ['nullable', 'integer', 'min:1', 'max:'.config('booking.max_participants')],
            'participants' => ['nullable', 'array:adult,child'],
            'participants.*' => ['integer', 'min:0', 'max:'.config('booking.max_participants')],
            'child_ages' => ['nullable', 'array', 'max:20'],
            'child_ages.*' => ['required', 'integer', 'min:0', 'max:17'],
            'option_id' => ['nullable', 'integer', 'exists:experience_options,id'],
            'availability_id' => ['nullable', 'integer', 'exists:availabilities,id'],
            'language' => ['nullable', 'string', 'max:12'],
        ];
    }

    /**
     * @return array<string, int>
     */
    public function participants(): array
    {
        $participants = collect($this->input('participants', []))
            ->map(fn ($count): int => (int) $count)
            ->filter(fn (int $count): bool => $count > 0)
            ->all();

        if ($participants !== []) {
            return $participants;
        }

        return [
            'adult' => $this->integer('participants_count', 1),
        ];
    }
}
