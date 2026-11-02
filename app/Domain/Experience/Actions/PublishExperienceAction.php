<?php

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Services\ExperienceReviewService;
use App\Models\User;
use Illuminate\Contracts\Auth\Factory;

final class PublishExperienceAction
{
    public function __construct(private readonly Factory $auth, private readonly ExperienceReviewService $reviews) {}

    public function execute(Experience $experience): Experience
    {
        $actor = $this->auth->guard()->user();
        abort_unless($actor instanceof User && $actor->can('publish', $experience), 403);
        $pending = $experience->changeRequests()->where('status', 'pending')->latest('id')->first();
        $pending ??= $this->reviews->submit($experience, $actor);

        return $this->reviews->decide($pending, $actor, 'approved', null);
    }
}
