<?php

namespace App\Http\Requests\Admin;

use App\Domain\Booking\Actions\UpdateBookingPaymentStatusAction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookingPaymentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') === true;
    }

    public function rules(): array
    {
        return [
            'payment_status' => ['required', Rule::in(UpdateBookingPaymentStatusAction::STATUSES)],
        ];
    }
}
