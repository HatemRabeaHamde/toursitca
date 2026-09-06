<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Landing\Models\Destination;
use App\Domain\Landing\Models\ExperienceCategory;
use App\Domain\Landing\Models\LandingFaq;
use App\Domain\Landing\Models\Landmark;
use App\Domain\Landing\Models\Testimonial;
use App\Domain\Landing\Models\TravelReel;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DestinationRequest;
use App\Http\Requests\Admin\LandingCategoryRequest;
use App\Http\Requests\Admin\LandingFaqRequest;
use App\Http\Requests\Admin\LandmarkRequest;
use App\Http\Requests\Admin\TestimonialRequest;
use App\Http\Requests\Admin\TravelReelRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingContentController extends Controller
{
    public function categories(Request $request): JsonResponse|View
    {
        if ($request->expectsJson()) {
            return response()->json(['data' => ExperienceCategory::query()->sorted()->paginate(20)]);
        }

        return view('admin.landing.categories');
    }

    public function storeCategory(LandingCategoryRequest $request): JsonResponse
    {
        $category = ExperienceCategory::query()->create($this->withDefaults($request->validated()));

        return response()->json(['data' => $category], 201);
    }

    public function updateCategory(LandingCategoryRequest $request, ExperienceCategory $category): JsonResponse
    {
        $category->update($this->withDefaults($request->validated()));

        return response()->json(['data' => $category->refresh()]);
    }

    public function destroyCategory(ExperienceCategory $category): JsonResponse
    {
        $category->delete();

        return response()->json(status: 204);
    }

    public function destinations(Request $request): JsonResponse|View
    {
        if ($request->expectsJson()) {
            return response()->json(['data' => Destination::query()->sorted()->paginate(20)]);
        }

        return view('admin.landing.destinations');
    }

    public function storeDestination(DestinationRequest $request): JsonResponse
    {
        $destination = Destination::query()->create($this->withDefaults($request->validated()));

        return response()->json(['data' => $destination], 201);
    }

    public function updateDestination(DestinationRequest $request, Destination $destination): JsonResponse
    {
        $destination->update($this->withDefaults($request->validated()));

        return response()->json(['data' => $destination->refresh()]);
    }

    public function destroyDestination(Destination $destination): JsonResponse
    {
        $destination->delete();

        return response()->json(status: 204);
    }

    public function landmarks(Request $request): JsonResponse|View
    {
        if ($request->expectsJson()) {
            return response()->json(['data' => Landmark::query()->sorted()->paginate(20)]);
        }

        return view('admin.landing.landmarks');
    }

    public function storeLandmark(LandmarkRequest $request): JsonResponse
    {
        $landmark = Landmark::query()->create($this->withDefaults($request->validated()));

        return response()->json(['data' => $landmark], 201);
    }

    public function updateLandmark(LandmarkRequest $request, Landmark $landmark): JsonResponse
    {
        $landmark->update($this->withDefaults($request->validated()));

        return response()->json(['data' => $landmark->refresh()]);
    }

    public function destroyLandmark(Landmark $landmark): JsonResponse
    {
        $landmark->delete();

        return response()->json(status: 204);
    }

    public function faqs(Request $request): JsonResponse|View
    {
        if ($request->expectsJson()) {
            return response()->json(['data' => LandingFaq::query()->sorted()->paginate(20)]);
        }

        return view('admin.landing.faqs');
    }

    public function storeFaq(LandingFaqRequest $request): JsonResponse
    {
        $faq = LandingFaq::query()->create($this->withDefaults($request->validated()));

        return response()->json(['data' => $faq], 201);
    }

    public function updateFaq(LandingFaqRequest $request, LandingFaq $faq): JsonResponse
    {
        $faq->update($this->withDefaults($request->validated()));

        return response()->json(['data' => $faq->refresh()]);
    }

    public function destroyFaq(LandingFaq $faq): JsonResponse
    {
        $faq->delete();

        return response()->json(status: 204);
    }

    public function reels(Request $request): JsonResponse|View
    {
        if ($request->expectsJson()) {
            return response()->json(['data' => TravelReel::query()->sorted()->paginate(20)]);
        }

        return view('admin.landing.reels');
    }

    public function storeReel(TravelReelRequest $request): JsonResponse
    {
        $reel = TravelReel::query()->create($this->withDefaults($request->validated()));

        return response()->json(['data' => $reel], 201);
    }

    public function updateReel(TravelReelRequest $request, TravelReel $reel): JsonResponse
    {
        $reel->update($this->withDefaults($request->validated()));

        return response()->json(['data' => $reel->refresh()]);
    }

    public function destroyReel(TravelReel $reel): JsonResponse
    {
        $reel->delete();

        return response()->json(status: 204);
    }

    public function testimonials(Request $request): JsonResponse|View
    {
        if ($request->expectsJson()) {
            return response()->json(['data' => Testimonial::query()->sorted()->paginate(20)]);
        }

        return view('admin.landing.testimonials');
    }

    public function storeTestimonial(TestimonialRequest $request): JsonResponse
    {
        $testimonial = Testimonial::query()->create($this->withDefaults($request->validated()));

        return response()->json(['data' => $testimonial], 201);
    }

    public function updateTestimonial(TestimonialRequest $request, Testimonial $testimonial): JsonResponse
    {
        $testimonial->update($this->withDefaults($request->validated()));

        return response()->json(['data' => $testimonial->refresh()]);
    }

    public function destroyTestimonial(Testimonial $testimonial): JsonResponse
    {
        $testimonial->delete();

        return response()->json(status: 204);
    }

    private function withDefaults(array $payload): array
    {
        $payload['sort_order'] = $payload['sort_order'] ?? 0;
        $payload['is_active'] = $payload['is_active'] ?? true;

        return $payload;
    }
}
