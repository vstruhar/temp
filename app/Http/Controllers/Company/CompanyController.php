<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Services\ValidateVatNumber;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Laravel\Jetstream\Actions\ValidateTeamDeletion;
use Laravel\Jetstream\Contracts\CreatesTeams;
use Laravel\Jetstream\Contracts\DeletesTeams;
use Laravel\Jetstream\Contracts\UpdatesTeamNames;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\RedirectsActions;

class CompanyController extends Controller
{
    use RedirectsActions;

    /**
     * Show the team management screen.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $teamId
     *
     * @return \Inertia\Response
     */
    public function show(Request $request, $teamId)
    {
        $team = Jetstream::newTeamModel()->findOrFail($teamId);

        Gate::authorize('view', $team);

        $ownerCompanies = Team::where('user_id', auth()->id())
                              ->active()
                              ->orderBy('name')
                              ->select('id', 'name')
                              ->get();

        return Jetstream::inertia()->render($request, 'Teams/Show', [
            'team'                 => $team->load('owner', 'users', 'teamInvitations'),
            'ownerCompanies'       => $ownerCompanies,
            'availableRoles'       => array_values(Jetstream::$roles),
            'availablePermissions' => Jetstream::$permissions,
            'defaultPermissions'   => Jetstream::$defaultPermissions,
            'permissions'          => [
                'canAddTeamMembers'    => Gate::check('addTeamMember', $team),
                'canDeleteTeam'        => Gate::check('delete', $team),
                'canRemoveTeamMembers' => Gate::check('removeTeamMember', $team),
                'canUpdateTeam'        => Gate::check('update', $team),
            ],
        ]);
    }

    /**
     * Show the team creation screen.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Inertia\Response
     */
    public function create(Request $request)
    {
        Gate::authorize('create', Jetstream::newTeamModel());

        return Inertia::render('Teams/Create');
    }

    /**
     * Create a new team.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $creator = app(CreatesTeams::class);

        $creator->create($request->user(), $request->all());

        return $this->redirectPath($creator);
    }

    /**
     * Update the given team's name.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $teamId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $teamId)
    {
        $team = Jetstream::newTeamModel()->findOrFail($teamId);

        app(UpdatesTeamNames::class)->update($request->user(), $team, $request->all());

        return back(303);
    }

    /**
     * Delete the given team.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $teamId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $teamId)
    {
        $team = Jetstream::newTeamModel()->findOrFail($teamId);

        app(ValidateTeamDeletion::class)->validate($request->user(), $team);

        $deleter = app(DeletesTeams::class);

        $deleter->delete($team);

        return $this->redirectPath($deleter);
    }

    /**
     * @param \Illuminate\Http\Request        $request
     * @param \App\Services\ValidateVatNumber $validateVatNumber
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateVat(Request $request, ValidateVatNumber $validateVatNumber): JsonResponse
    {
        try {
            $result = $validateVatNumber->handle($request->vat);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed']);
        }

        return response()->json(['status' => $result ? 'valid' : 'invalid']);
    }
}
