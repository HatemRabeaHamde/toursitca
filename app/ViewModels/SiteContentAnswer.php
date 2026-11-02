<?php

namespace App\ViewModels;

final readonly class SiteContentAnswer
{
    public string $summary;

    public string $question;

    public string $reviewer;

    public string $reviewedOn;

    public array $sources;

    public function __construct(SiteContentPage $content)
    {
        $this->summary = $content->get('aeo.summary');
        $this->question = $content->get('aeo.question');
        $this->reviewer = $content->get('aeo.reviewer');
        $this->reviewedOn = $content->get('aeo.reviewed_on');
        $sources = [];
        foreach ([1, 2] as $index) {
            $url = $content->get('aeo.source_'.$index.'_url');
            if ($url) {
                $sources[] = ['url' => $url, 'label' => $content->get('aeo.source_'.$index.'_label') ?: $url];
            }
        }
        $this->sources = $sources;
    }
}
