<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param null                     $locale
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $locale = null)
    {
        if ($locale) {
            app()->setLocale($locale);
        } elseif (auth()->check()) {
            app()->setLocale(
                session('locale', auth()->user()->language)
            );
        }

        return $next($request);
    }
}
