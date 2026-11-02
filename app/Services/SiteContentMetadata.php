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
        $profiles = array_values(array_filter(array_map(fn ($key) => $content->get('geo.'.$key), ['instagram', 'facebook', 'linkedin'])));
        if ($profiles) {
            $entity['sameAs'] = $profiles;
        }
        $alternates = [];
        foreach (['en', 'fr', 'nl'] as $language) {
            $alternates[$language] = $this->urls->route($definition['route'], ['locale' => $language]);
        }
        $alternates['x-default'] = $alternates['en'];

        return [
            'title' => $content->get('seo.title'), 'description' => $content->get('seo.description'),
            'robots' => $content->preview ? 'noindex, nofollow' : $content->get('seo.robots'),
            'canonical' => $canonical, 'hreflang' => $alternates,
            'social_title' => $content->get('seo.social_title') ?: $content->get('seo.title'),
            'social_description' => $content->get('seo.social_description') ?: $content->get('seo.description'),
            'image' => $content->get('seo.social_image')['url'], 'image_alt' => $content->get('seo.social_image')['alt'],
            'schema' => ['@context' => 'https://schema.org', '@graph' => [$entity, [
                '@type' => 'WebPage', '@id' => $canonical.'#webpage', 'url' => $canonical, 'name' => $content->get('seo.title'),
                'description' => $content->get('aeo.summary') ?: $content->get('seo.description'), 'inLanguage' => $locale,
                'publisher' => ['@id' => $entity['@id']],
            ]]],
        ];
    }
}
