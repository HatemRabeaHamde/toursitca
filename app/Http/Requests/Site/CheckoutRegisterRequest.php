<?php

namespace App\Http\Requests\Site;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class CheckoutRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'preferred_lang' => ['required', 'string', 'in:'.implode(',', array_keys(config('locales.supported')))],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }
}
