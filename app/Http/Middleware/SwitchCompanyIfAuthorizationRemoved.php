<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;

class SwitchCompanyIfAuthorizationRemoved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
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
        return $next($request);
    }
}
