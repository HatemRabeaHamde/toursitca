<?php

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\Models\Experience;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use InvalidArgumentException;

final class AttachExperienceMediaAction
{
    /**
     * @param  array<int, UploadedFile>  $images
     */
    public function execute(
        Experience $experience,
        array $images = [],
        ?UploadedFile $videoFile = null,
        ?string $videoUrl = null,
    ): void {
        $this->guardMediaLimits($experience, $images, $videoFile, $videoUrl);

        $sortOrder = (int) $experience->media()->max('sort_order');

        foreach ($images as $image) {
            $path = $this->moveUploadedFile($image, config('media.public_paths.experience_images'));
            $sortOrder++;

            $experience->media()->create([
                'type' => 'image',
                'source_type' => 'upload',
                'path' => $path,
                'sort_order' => $sortOrder,
            ]);

            if ($experience->thumbnail === null) {
                $experience->forceFill(['thumbnail' => $path])->save();
            }
        }

        if ($videoFile !== null) {
            $sortOrder++;
            $experience->media()->create([
                'type' => 'video',
                'source_type' => 'upload',
                'path' => $this->moveUploadedFile($videoFile, config('media.public_paths.experience_videos')),
                'sort_order' => $sortOrder,
            ]);
        }

        if ($videoUrl !== null) {
            $sortOrder++;
            $experience->media()->create([
                'type' => 'video',
                'source_type' => 'url',
                'path' => $videoUrl,
                'sort_order' => $sortOrder,
            ]);
        }
    }

    /**
     * @param  array<int, UploadedFile>  $images
     */
    private function guardMediaLimits(
        Experience $experience,
        array $images,
        ?UploadedFile $videoFile,
        ?string $videoUrl,
    ): void {
        $imageCount = $experience->images()->count() + count($images);
        if ($imageCount > (int) config('media.max_per_experience')) {
            throw new InvalidArgumentException('Experience image limit exceeded.');
        }

        $newVideosCount = ($videoFile === null ? 0 : 1) + ($videoUrl === null ? 0 : 1);
        $videoCount = $experience->videos()->count() + $newVideosCount;
        if ($videoCount > (int) config('media.max_videos_per_experience')) {
            throw new InvalidArgumentException('Experience video limit exceeded.');
        }
    }

    private function moveUploadedFile(UploadedFile $file, string $directory): string
    {
        File::ensureDirectoryExists(public_path($directory));

        $filename = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
        $file->move(public_path($directory), $filename);

        return $directory.'/'.$filename;
    }
}
