<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Laravel\Jetstream\Contracts\AddsTeamMembers;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\TeamInvitation;

class CompanyInvitationController extends Controller
{
    /**
     * Accept a team invitation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Jetstream\TeamInvitation  $invitation
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function accept(Request $request, TeamInvitation $invitation)
    {
        app(AddsTeamMembers::class)->add(
            $invitation->team->owner,
            $invitation->team,
            $invitation->email,
            $invitation->role
        );

        // add all invitations
        $newTeamMember = Jetstream::findUserByEmailOrFail($invitation->email);

        $rows = DB::table('team_invitations')
                  ->where('email', $invitation->email)
                  ->where('id', '!=', $invitation->id)
                  ->whereBetween('created_at', [$invitation->created_at->subMinute(), $invitation->created_at->addMinute()])
                  ->get();

        $rows->each(function ($row) use ($newTeamMember, $invitation) {
            Team::find($row->team_id)
                ->users()
                ->attach(
                    $newTeamMember, ['role' => $invitation->role]
                );
        });

        DB::table('team_invitations')->whereIn('id', $rows->pluck('id'))->delete();

        $invitation->delete();

        return redirect(config('fortify.home'))->banner(__('You have been granted access to new company'));
    }

    /**
     * Cancel the given team invitation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Jetstream\TeamInvitation  $invitation
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, TeamInvitation $invitation)
    {
        if (!Gate::forUser($request->user())->check('removeTeamMember', $invitation->team)) {
            throw new AuthorizationException;
        }

        $invitation->delete();

        return back(303);
    }
}
