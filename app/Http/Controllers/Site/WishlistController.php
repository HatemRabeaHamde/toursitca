<?php

namespace App\Http\Controllers\Site;

use App\Domain\Experience\Models\Experience;
use App\Domain\Wishlist\Models\Wishlist;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class WishlistController extends Controller
{
    public function toggle(Request $request, string $locale, Experience $experience): JsonResponse
    {
        $user = $request->user();

        $existing = Wishlist::where('user_id', $user->id)
            ->where('experience_id', $experience->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $saved = false;
        } else {
            Wishlist::create([
                'user_id' => $user->id,
                'experience_id' => $experience->id,
            ]);
            $saved = true;
        }

        return response()->json(['saved' => $saved]);
    }

    public function ids(Request $request): JsonResponse
    {
        $ids = $request->user()
            ? $request->user()->wishlistedExperienceIds()
            : [];

        return response()->json(['ids' => $ids]);
    }
}
