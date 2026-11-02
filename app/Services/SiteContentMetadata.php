<?php

namespace App\Services;

use App\ViewModels\SiteContentPage;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Routing\UrlGenerator;

final class SiteContentMetadata
{
    public function __construct(private UrlGenerator $urls, private Repository $config) {}

    public function build(SiteContentPage $content, string $locale): array
    {
        $definition = $this->config->get('site_content.pages.'.$content->page);
        $canonical = $this->urls->route($definition['route'], ['locale' => $locale]);
        $root = $content->get('geo.url') ?: $this->urls->to('/');
        $entity = ['@type' => 'Organization', '@id' => rtrim($root, '/').'#organization', 'name' => $content->get('geo.name'), 'url' => $root, 'logo' => $content->get('geo.logo')['url']];
        if ($content->get('geo.description')) {
            $entity['description'] = $content->get('geo.description');
        }
        $profiles = array_values(array_filter(array_map(fn ($key) => $content->get('geo.'.$key), ['instagram', 'facebook', 'linkedin', 'youtube'])));
        if ($profiles) {
            $entity['sameAs'] = $profiles;
        }
        foreach (['legal_name' => 'legalName', 'alternate_name' => 'alternateName', 'email' => 'email', 'phone' => 'telephone'] as $key => $property) {
            if ($content->get('geo.'.$key)) {
                $entity[$property] = $content->get('geo.'.$key);
            }
        }
        if ($content->get('geo.about_url')) {
            $entity['subjectOf'] = ['@type' => 'WebPage', 'url' => $content->get('geo.about_url')];
        }
        $robots = $content->preview ? 'noindex, nofollow' : $content->get('seo.robots');
        if (! $content->preview) {
            foreach (['max_snippet' => 'max-snippet', 'max_image_preview' => 'max-image-preview', 'max_video_preview' => 'max-video-preview'] as $key => $directive) {
                $value = $content->get('seo.'.$key);
                if ($value !== '' && $value !== null) {
                    $robots .= ', '.$directive.':'.$value;
                }
            }
        }
        $alternates = [];
        foreach (['en', 'fr', 'nl'] as $language) {
            $alternates[$language] = $this->urls->route($definition['route'], ['locale' => $language]);
        }
        $alternates['x-default'] = $alternates['en'];

        $webPage = ['@type' => 'WebPage', '@id' => $canonical.'#webpage', 'url' => $canonical, 'name' => $content->get('seo.title'), 'description' => $content->get('aeo.summary') ?: $content->get('seo.description'), 'inLanguage' => $locale, 'publisher' => ['@id' => $entity['@id']]];
        if ($content->get('aeo.summary')) {
            if ($content->get('aeo.reviewed_on')) {
                $webPage['lastReviewed'] = $content->get('aeo.reviewed_on');
            }
            if ($content->get('aeo.reviewer')) {
                $webPage['reviewedBy'] = ['@type' => $content->get('aeo.reviewer_type') ?: 'Person', 'name' => $content->get('aeo.reviewer')];
            }
            $sources = array_values(array_filter(array_map(fn ($i) => $content->get('aeo.source_'.$i.'_url'), [1, 2])));
            if ($sources) {
                $webPage['citation'] = $sources;
            }
        }

        return [
            'title' => $content->get('seo.title'), 'description' => $content->get('seo.description'),
            'robots' => $robots, 'twitter_card' => $content->get('seo.twitter_card') ?: 'summary_large_image', 'google_verification' => $content->get('seo.google_verification'),
            'canonical' => $canonical, 'hreflang' => $alternates,
            'social_title' => $content->get('seo.social_title') ?: $content->get('seo.title'),
            'social_description' => $content->get('seo.social_description') ?: $content->get('seo.description'),
            'image' => $content->get('seo.social_image')['url'], 'image_alt' => $content->get('seo.social_image')['alt'],
            'schema' => ['@context' => 'https://schema.org', '@graph' => [$entity, $webPage]],
        ];
    }
}
