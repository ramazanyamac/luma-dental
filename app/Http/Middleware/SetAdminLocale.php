<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetAdminLocale
{
    public function handle(
        Request $request,
        Closure $next,
        string $locale = 'en'
    ): Response {
        if ($locale === 'en' && $request->isMethod('GET')) {
            // Explicitly switch back to English.
            if ($request->query('lang') === 'en') {
                return redirect()
                    ->to($request->fullUrlWithoutQuery(['lang']))
                    ->withCookie(cookie('site_locale', 'en', 60 * 24 * 365));
            }

            // Restore the saved Turkish preference.
            if ($request->cookie('site_locale') === 'tr') {
                return redirect()->route(
                    'tr.' . $request->route()->getName(),
                    array_merge(
                        $request->query(),
                        $request->route()->parameters()
                    )
                );
            }
        }

        app()->setLocale($locale);

        $response = $next($request);

        $response->headers->setCookie(
            cookie('site_locale', $locale, 60 * 24 * 365)
        );

        return $response;
    }
}
