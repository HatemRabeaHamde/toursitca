<?php

namespace App\View\Components\Site;

use Illuminate\View\Component;
use Illuminate\View\View;

final class AdminRecord extends Component
{
    public string $url;

    public bool $persisted;

    public function __construct(public string $kind, public ?int $recordId = null, public string $label = '')
    {
        $definition = config('site_content_records.'.$kind) ?? abort(404);
        $this->persisted = ($recordId ?? 0) > 0;
        $this->url = $this->persisted ? (isset($definition['edit']) ? route($definition['edit'], [$definition['parameter'] => $recordId, 'content_frame' => 1]) : route('admin.site-content.record', ['kind' => $kind, 'record' => $recordId, 'content_frame' => 1])) : route($definition['index'], ['content_frame' => 1]);
    }

    public function shouldRender(): bool
    {
        return request()->boolean('content_preview') && request()->boolean('content_edit') && (auth()->user()?->hasRole('admin') ?? false);
    }

    public function render(): View
    {
        return view('components.site.admin-record');
    }
}
