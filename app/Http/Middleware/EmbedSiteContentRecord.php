<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EmbedSiteContentRecord
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        if (! $request->boolean('content_frame')) {
            return $response;
        }
        $response->headers->set('Cache-Control', 'private, no-store');
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        if ($response->isRedirection()) {
            $location = $response->headers->get('Location');
            $parts = parse_url($location);
            if (isset($parts['path']) && str_starts_with($parts['path'], '/admin/') && (! isset($parts['host']) || $parts['host'] === $request->getHost())) {
                parse_str($parts['query'] ?? '', $query);
                $query['content_frame'] = 1;
                $base = explode('?', $location, 2)[0];
                $response->headers->set('Location', $base.'?'.http_build_query($query));
            }
        }

        return $response;
    }
}
