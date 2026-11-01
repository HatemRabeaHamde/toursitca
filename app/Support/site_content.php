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
