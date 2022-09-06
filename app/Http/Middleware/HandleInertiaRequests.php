<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use App\Services\TranslationsService;
use Closure;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    public function handle(Request $request, Closure $next)
    {
        $response = parent::handle($request, $next);

        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($user && (!$user->currentTeam || !$user->belongsToTeam($user->currentTeam))) {
            if ($user->personalTeam()) {
                $user->switchTeam(
                    $user->personalTeam()
                );
                return redirect(RouteServiceProvider::HOME);
            }

            if ($user->teams()->count()) {
                $user->switchTeam(
                    $user->teams()->first()
                );
                return redirect(RouteServiceProvider::HOME);
            }

            return redirect('logout');
        }
        return $response;
    }

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return string|null
     */
    public function version(Request $request)
    {
        //return parent::version($request); // default
        return config('app.version');
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return array
     */
    public function share(Request $request)
    {
        $user = auth()->user();

        $this->setLocaleIfNeeded($request);

        return array_merge(parent::share($request), [
            'locale' => [
                'current'   => app()->getLocale(),
                'languages' => TranslationsService::getAvailableLanguageCodes(),
            ],
            'role' => $user ? $user->teamRole($user->currentTeam) : [],
            'admin_impersonating_client' => session('admin_impersonating_client', false),
            'user' => auth()->check()
                ? [
                    'owns_current_company' => $user->ownsCurrentCompany(),
                    'all_teams' => $user->allTeams(),
                  ] + $user->toArray()
                : null,
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    protected function setLocaleIfNeeded(Request $request): void
    {
        if (
            $request->has('lang')
            && app()->getLocale() !== $request->lang
            && in_array($request->lang, TranslationsService::getAvailableLanguageCodes(), true)
        ) {
            app()->setLocale(
                $request->get('lang', app()->getLocale())
            );
        }
    }
}
