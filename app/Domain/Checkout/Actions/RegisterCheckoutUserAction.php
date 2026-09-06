<?php

namespace App\Domain\Checkout\Actions;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

final class RegisterCheckoutUserAction
{
    public function execute(array $payload): User
    {
        $user = User::query()->create([
            'name' => $payload['name'],
            'email' => $payload['email'],
            'preferred_lang' => $payload['preferred_lang'],
            'password' => Hash::make($payload['password']),
        ]);

        $user->assignRole(Role::findOrCreate('user', 'web'));

        event(new Registered($user));

        return $user;
    }
}
