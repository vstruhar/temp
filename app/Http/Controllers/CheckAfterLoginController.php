<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CheckAfterLoginController extends Controller
{
    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function check(): RedirectResponse
    {
        $user = Auth::user();

        $lastLogin = $user->last_login_at;

        $user->update(['last_login_at' => now()]);

        if ($user->registered_from === 'admin' && $lastLogin === null) {
            return redirect()->route('user.password.form');
        }

        if ($user->allTeams()->count() > 1) {
            return redirect()->route('user.company.select');
        }

        return redirect()->route('documents', 'invoices');
    }
}
