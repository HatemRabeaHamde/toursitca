<?php

namespace App\Http\Requests\Experience;

use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\ExperienceOption;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AvailabilityStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $availability = $this->route('availability');
        $availabilityId = $availability instanceof Availability ? $availability->id : null;

        return [
            'experience_id' => ['required', 'integer', 'exists:experiences,id'],
            'experience_option_id' => ['nullable', 'integer', 'exists:experience_options,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time_slot' => [
                'required',
                'date_format:H:i',
                Rule::unique('availabilities', 'time_slot')
                    ->where(function ($query) {
                        $query
                            ->where('experience_id', $this->input('experience_id'))
                            ->where('date', $this->input('date'));

                        return $this->filled('experience_option_id')
                            ? $query->where('experience_option_id', $this->input('experience_option_id'))
                            : $query->whereNull('experience_option_id');
                    })
                    ->ignore($availabilityId),
            ],
            'max_seats' => ['required', 'integer', 'min:1', 'max:'.config('booking.max_participants')],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $availability = $this->route('availability');

            if ($availability instanceof Availability && $this->integer('max_seats') < $availability->booked_seats) {
                $validator->errors()->add('max_seats', __('validation.min.numeric', [
                    'attribute' => __('ui.fields.max_seats'),
                    'min' => $availability->booked_seats,
                ]));
            }

            if ($this->filled('experience_option_id')) {
                $optionBelongsToExperience = ExperienceOption::query()
                    ->whereKey($this->integer('experience_option_id'))
                    ->where('experience_id', $this->integer('experience_id'))
                    ->exists();

                if (! $optionBelongsToExperience) {
                    $validator->errors()->add('experience_option_id', __('ui.messages.invalid_experience_option'));
                }
            }
        });
    }

    public function payload(): array
    {
        return [
            'experience_id' => $this->integer('experience_id'),
            'experience_option_id' => $this->filled('experience_option_id') ? $this->integer('experience_option_id') : null,
            'date' => $this->date('date')->toDateString(),
            'time_slot' => $this->string('time_slot')->toString(),
            'max_seats' => $this->integer('max_seats'),
            'is_active' => $this->boolean('is_active'),
        ];
    }
}
