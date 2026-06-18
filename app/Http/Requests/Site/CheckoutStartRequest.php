<?php

namespace App\Http\Requests\Site;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutStartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'experience_id' => ['required', 'integer', 'exists:experiences,id'],
            'option_id' => ['nullable', 'integer', 'exists:experience_options,id'],
            'availability_id' => ['required', 'integer', 'exists:availabilities,id'],
            'booking_type' => ['required', Rule::in(['group', 'private'])],
            'participants' => ['nullable', 'array'],
            'participants.*' => ['integer', 'min:0', 'max:'.config('booking.max_participants')],
            'participants_count' => ['nullable', 'integer', 'min:1', 'max:'.config('booking.max_participants')],
            'language' => ['nullable', 'string', 'max:12'],
            'payment_method' => ['nullable', Rule::in(['manual', 'pay_later'])],
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
