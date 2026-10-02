<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetSiteLocale
{
    public function handle(
        Request $request,
        Closure $next,
        string $locale = 'en'
    ): Response {
        app()->setLocale($locale === 'tr' ? 'tr' : 'en');

        return $next($request);
    }
}
