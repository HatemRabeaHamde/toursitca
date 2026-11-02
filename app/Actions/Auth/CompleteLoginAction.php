<?php

namespace App\Actions\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Services\DashboardDestination;

final class CompleteLoginAction
{
    public function __construct(private readonly DashboardDestination $destinations) {}

    public function execute(LoginRequest $request): string
    {
        $request->authenticate();
        $request->session()->regenerate();
        $request->session()->forget('url.intended');
        /** @var User $user */
        $user = $request->user();

        return $this->destinations->forUser($user);
    }
}
