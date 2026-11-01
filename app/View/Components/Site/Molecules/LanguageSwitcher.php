<?php

namespace App\View\Components\Site\Molecules;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * EN / FR / NL switcher. Each option points to the *same* page in the
 * other locale (route name + parameters + query string are preserved).
 */
final class LanguageSwitcher extends Component
{
    /** @return list<array{code: string, label: string, native: string, url: string, active: bool}> */
    public function locales(): array
    {
        $current = app()->getLocale();

        return collect(config('locales.supported', []))
            ->map(fn (array $meta, string $code): array => [
                'code' => $code,
                'label' => strtoupper($code),
                'native' => $meta['native'] ?? strtoupper($code),
                'url' => $this->urlFor($code),
                'active' => $code === $current,
            ])
            ->values()
            ->all();
    }

    public function currentLabel(): string
    {
        return strtoupper(app()->getLocale());
    }

    private function urlFor(string $locale): string
    {
        $route = request()->route();
        $parameters = $route?->parameters() ?? [];

        $url = $route?->getName() && array_key_exists('locale', $parameters)
            ? route($route->getName(), array_merge($parameters, ['locale' => $locale]))
            : route('site.home', ['locale' => $locale]);

        $query = request()->getQueryString();

        return $query ? $url.'?'.$query : $url;
    }

    public function render(): View
    {
        return view('components.site.molecules.language-switcher');
    }
}
