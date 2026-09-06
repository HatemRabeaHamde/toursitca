<?php

namespace App\Http\Controllers\Site;

use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Services\ExperienceQuoteService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\AvailabilityQuoteRequest;
use Illuminate\Http\JsonResponse;

class AvailabilityController extends Controller
{
    public function quote(AvailabilityQuoteRequest $request, string $locale, Experience $experience, ExperienceQuoteService $quoteService): JsonResponse
    {
        abort_unless($experience->status === 'published', 404);

        return response()->json([
            'data' => $quoteService->quote(
                experience: $experience,
                bookingType: $request->string('booking_type')->toString(),
                participants: $request->participants(),
                optionId: $request->filled('option_id') ? $request->integer('option_id') : null,
                availabilityId: $request->filled('availability_id') ? $request->integer('availability_id') : null,
                languageCode: $request->filled('language') ? $request->string('language')->toString() : null,
            ),
        ]);
    }
}
