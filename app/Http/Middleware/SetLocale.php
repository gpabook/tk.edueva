<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from session, fallback to config default
        $locale = Session::get('locale', config('app.locale'));

        // Check if the locale is in our available_locales array
        if (array_key_exists($locale, config('app.available_locales', []))) {
            App::setLocale($locale);
        } else {
            // If not valid, use the default locale
            App::setLocale(config('app.locale'));
        }

        return $next($request);
    }
}