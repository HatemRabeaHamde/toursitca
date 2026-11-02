<?php

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\Models\Experience;
use Illuminate\Support\Facades\DB;

final class UnpublishExperienceAction
{
    public function execute(Experience $experience): Experience
    {
        return DB::transaction(function () use ($experience): Experience {
            $locked = Experience::query()->lockForUpdate()->findOrFail($experience->id);
            $draft = $locked->changeRequests()->where('status', 'pending')->latest('id')->first();
            if ($draft) {
                $draft->forceFill(['status' => 'draft', 'submitted_at' => null])->save();
                $locked->review_status = 'draft';
            }
            $locked->forceFill(['status' => 'unpublished'])->save();

            return $locked->refresh();
        });
    }
}
