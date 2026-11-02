<?php

namespace App\Actions\SiteContent;

use Illuminate\View\View;

final class EditSiteContentRecordAction
{
    public function execute(string $kind, int $record): View
    {
        $definition = config('site_content_records.'.$kind) ?? abort(404);
        abort_unless(isset($definition['view']), 404);
        $model = $definition['model']::findOrFail($record);

        return view($definition['view'], ['canvasRecord' => $model->toArray()]);
    }
}
