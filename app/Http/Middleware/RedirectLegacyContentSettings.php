<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class RedirectLegacyContentSettings
{
    public function handle(Request $request, Closure $next): Response
    {
        $field = $request->query('field');
        if (is_string($field)) {
            $section = explode('.', $field)[0];
            foreach (config('site_content.panels') as $panel => $definition) {
                if (in_array($section, $definition['sections'], true)) {
                    return redirect()->route('admin.site-content.settings', ['page' => $request->route('page'), 'panel' => $panel]);
                }
            }
        }

        return $next($request);
    }
}
