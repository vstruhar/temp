<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function index()
    {
        /* @var \App\Models\User $user */
        $user = auth()->user();

        abort_if(!$user->hasCurrentTeamPermission('dashboard:show'), 401);

        $companies = Team::whereIn('id', $user->allTeams()->pluck('id'))
                         ->with(['companyDetails' => function ($query) {
                             $query->withoutGlobalScope('company_details_only');
                         }])
                         ->orderBy('name')
                         ->get();

        return Inertia::render('Dashboard', compact('companies'));
    }
}
