<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $role   = $request->string('role')->toString();

        $users = User::query()
            ->withCount('bookings')
            ->when($search !== '', fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            }))
            ->when($role !== '', fn ($q) => $q->role($role))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search', 'role'));
    }

    public function show(User $user): View
    {
        $user->load(['bookings.experience', 'agency']);

        return view('admin.users.show', compact('user'));
    }

    public function ban(User $user): RedirectResponse
    {
        $user->forceFill(['status' => 'banned'])->save();

        return redirect()->back()->with('success', 'User banned.');
    }

    public function restore(User $user): RedirectResponse
    {
        $user->forceFill(['status' => 'active'])->save();

        return redirect()->back()->with('success', 'User restored.');
    }
}
