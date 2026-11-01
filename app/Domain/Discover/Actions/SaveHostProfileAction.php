<?php

namespace App\Domain\Discover\Actions;

use App\Domain\Discover\DTOs\HostProfileData;
use App\Domain\Discover\Models\Host;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Creates or updates the profile of a host that belongs to an agency.
 * A new profile starts hidden and unreviewed; changing the licence of a
 * verified host takes the badge away until an admin checks it again.
 */
final class SaveHostProfileAction
{
    public function execute(?Host $host, int $agencyId, HostProfileData $data): Host
    {
        $host ??= new Host([
            'agency_id' => $agencyId,
            'slug' => $this->uniqueSlug($data->name),
            'is_active' => false,
            'is_verified' => false,
        ]);

        $licenceChanged = $host->exists && trim((string) $host->licence_number) !== trim((string) $data->licenceNumber);

        $host->fill([
            'name' => $data->name,
            'city' => $data->city,
            'destination_id' => $data->destinationId,
            'interest_id' => $data->interestId,
            'languages' => $data->languages ?: null,
            'partner_since' => $data->partnerSince,
            'licence_number' => $data->licenceNumber,
        ]);

        foreach (['role' => $data->role, 'bio' => $data->bio, 'quote' => $data->quote, 'story' => $data->story, 'skills' => $data->skills] as $field => $values) {
            $host->setTranslations($field, $values);
        }

        if (! $host->exists || $licenceChanged) {
            $host->is_verified = false;
            $host->reviewed_at = null;
        }

        $this->storeImage($host, 'photo_url', $data->photo);
        $this->storeImage($host, 'cover_url', $data->cover);
        $host->save();

        return $host;
    }

    private function storeImage(Host $host, string $column, ?UploadedFile $file): void
    {
        if ($file !== null) {
            $host->{$column} = 'storage/'.$file->store('hosts', 'public');
        }
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'host';
        $slug = $base;

        for ($i = 2; Host::withTrashed()->where('slug', $slug)->exists(); $i++) {
            $slug = $base.'-'.$i;
        }

        return $slug;
    }
}
