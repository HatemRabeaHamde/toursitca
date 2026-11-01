<?php

namespace App\Http\Controllers\Site;

use App\Domain\Review\Services\ReviewDirectoryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\ReviewDirectoryRequest;
use App\Http\Responses\ReviewDirectoryResponse;

class TravellerReviewsController extends Controller
{
    public function index(ReviewDirectoryRequest $request, ReviewDirectoryService $service): ReviewDirectoryResponse
    {
        return new ReviewDirectoryResponse($request->toFilters(), $service);
    }
}
