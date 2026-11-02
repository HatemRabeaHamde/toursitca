<?php

// Reads only the page snapshot prepared before Blade renders. Never performs queries.
function site_content(string $key, array $replace = []): mixed
{
    [$page,$path] = explode('.', $key, 2);
    $snapshot = request()->attributes->get('site_content');
    if (! $snapshot || $snapshot->page !== $page) {
        return '';
    }

    $value = array_key_exists($path, $snapshot->values) ? $snapshot->get($path) : $snapshot->group($path);
    foreach ($replace as $name => $replacement) {
        if (is_string($value)) {
            $value = str_replace(':'.$name, (string) $replacement, $value);
        }
    }

    return $value;
}

// Shared chrome keeps its normal translations on all other pages.
function landing_text(string $key, array $replace = []): string
{
    if (request()->attributes->get('site_content')?->page === 'home') {
        return (string) site_content('home.'.substr($key, 5), $replace);
    }

    return (string) __($key, $replace);
}
function landing_link(string $key, string $fallback): string
{
    return request()->attributes->get('site_content')?->page === 'home' ? (site_content('home.'.$key) ?: $fallback) : $fallback;
}
function landing_artwork(string $key, string $fallback): string
{
    return request()->attributes->get('site_content')?->page === 'home' ? site_content('home.artwork.'.str_replace('.', '_', $key))['url'] : asset($fallback);
}

function landing_choice(string $key, int $count): string
{
    if (request()->attributes->get('site_content')?->page !== 'home') {
        return trans_choice($key, $count, ['count' => $count]);
    }
    $line = (string) site_content('home.'.substr($key, 11));

    return str_replace(':count', (string) $count, app('translator')->getSelector()->choose($line, $count, app()->getLocale()));
}
