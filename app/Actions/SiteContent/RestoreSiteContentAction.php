<?php

namespace App\Actions\SiteContent;

use App\Services\SiteContentWriter;

final class RestoreSiteContentAction
{
    public function __construct(private SiteContentWriter $writer) {}

    public function execute(string $page, int $revision, int $userId): void
    {
        $this->writer->restore($page, $revision, $userId);
    }
}
