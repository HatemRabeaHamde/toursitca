<?php

namespace App\Http\Requests\Site;

use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class BookingStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'availability_id' => ['required', 'integer', 'exists:availabilities,id'],
            'booking_type' => ['required', 'string', 'in:group,private'],
            'participants_count' => ['required', 'integer', 'min:1', 'max:'.config('booking.max_participants')],
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_email' => ['required', 'email:rfc', 'max:255'],
            'guest_phone' => ['nullable', 'string', 'max:50'],
            'special_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $experience = $this->route('experience');
            if (! $experience instanceof Experience || $experience->status !== 'published') {
                $validator->errors()->add('experience', __('ui.messages.experience_unavailable'));

                return;
            }

            $availability = Availability::query()->find($this->integer('availability_id'));
            if (! $availability || $availability->experience_id !== $experience->id) {
                $validator->errors()->add('availability_id', __('ui.messages.invalid_booking_slot'));

                return;
            }

            if (! $availability->is_active || $availability->date->lt(today())) {
                $validator->errors()->add('availability_id', __('ui.messages.booking_slot_unavailable'));
            }

            $participantsCount = $this->integer('participants_count');
            if ($participantsCount > $experience->max_group_size) {
                $validator->errors()->add('participants_count', __('ui.messages.participants_exceed_experience'));
            }

            if ($this->string('booking_type')->toString() === 'private') {
                if ($availability->booked_seats > 0 || $availability->held_seats > 0) {
                    $validator->errors()->add('availability_id', __('ui.messages.booking_slot_unavailable'));
                }

                return;
            }

            if ($participantsCount > $availability->availableSeats()) {
                $validator->errors()->add('participants_count', __('ui.messages.not_enough_seats'));
            }
        });
    }
}
