<?php

namespace App\Actions\SiteContent;

use App\DTOs\SiteContentUpdateData;
use App\Services\SiteContentWriter;

final class SaveSiteContentAction
{
    public function __construct(private SiteContentWriter $writer) {}

    public function execute(SiteContentUpdateData $data): void
    {
        $this->writer->save($data->page, ['mode' => $data->mode, 'fields' => $data->fields], $data->userId);
    }
}
