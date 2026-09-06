<?php

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Models\ExperienceOption;
use Illuminate\Support\Facades\DB;

final class SaveExperienceOptionAction
{
    public function execute(
        Experience $experience,
        array $optionAttributes,
        array $priceRows,
        array $languageRows,
        ?ExperienceOption $option = null,
    ): ExperienceOption {
        return DB::transaction(function () use ($experience, $optionAttributes, $priceRows, $languageRows, $option): ExperienceOption {
            $option ??= new ExperienceOption(['experience_id' => $experience->id]);
            $option->fill($optionAttributes);
            $option->experience_id = $experience->id;
            $option->save();

            $option->prices()->delete();
            foreach ($priceRows as $row) {
                $option->prices()->create($row);
            }

            $option->languages()->delete();
            foreach ($languageRows as $row) {
                $option->languages()->create($row);
            }

            return $option->refresh()->load(['prices', 'languages']);
        });
    }
}
