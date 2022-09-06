<?php

namespace App\Providers;

use App\Actions\Jetstream\AddTeamMember;
use App\Actions\Jetstream\CreateTeam;
use App\Actions\Jetstream\DeleteTeam;
use App\Actions\Jetstream\DeleteUser;
use App\Actions\Jetstream\InviteTeamMember;
use App\Actions\Jetstream\RemoveTeamMember;
use App\Actions\Jetstream\UpdateTeamName;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Jetstream\Jetstream;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configurePermissions();

        Jetstream::createTeamsUsing(CreateTeam::class);
        Jetstream::updateTeamNamesUsing(UpdateTeamName::class);
        Jetstream::addTeamMembersUsing(AddTeamMember::class);
        Jetstream::inviteTeamMembersUsing(InviteTeamMember::class);
        Jetstream::removeTeamMembersUsing(RemoveTeamMember::class);
        Jetstream::deleteTeamsUsing(DeleteTeam::class);
        Jetstream::deleteUsersUsing(DeleteUser::class);

        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            if ($user && $user->active && Hash::check($request->password, $user->password)) {
                if ($user->belongsToTeam($user->currentTeam)) {
                    return $user;
                }

                $userHasAccessToCurrentTeam = $user->currentTeam && DB::table('team_user')
                                                                      ->where('user_id', $user->id)
                                                                      ->where('team_id', $user->currentTeam->id)
                                                                      ->exists();

                if (!$userHasAccessToCurrentTeam) {
                    $row = DB::table('team_user')
                             ->where('user_id', $user->id)
                             ->when($user->currentTeam, function ($query) use ($user) {
                                 $query->where('team_id', '<>', $user->currentTeam->id);
                             })
                             ->first();

                    if ($row) {
                        $team = Team::find($row->team_id);

                        $user->switchTeam($team);
                    } elseif ($user->allTeams()->count()) {
                        $user->switchTeam(
                            $user->allTeams()->first()
                        );
                    } else {
                        return null;
                    }
                }

                return $user;
            }
        });
    }

    /**
     * Configure the roles and permissions that are available within the application.
     *
     * @return void
     */
    protected function configurePermissions()
    {
        Jetstream::defaultApiTokenPermissions(['read']);


        Jetstream::role('admin', __('Administrator'), [
            // DOCUMENTS
            'documents:revision:restore', 'documents:issue:real_invoice',
            // INVOICES
            'documents:invoice:list', 'documents:invoice:show', 'documents:invoice:edit', 'documents:invoice:create', 'documents:invoice:delete',
            'documents:invoice:duplicate',
            'documents:invoice:send', 'documents:invoice:download', 'documents:invoice:print',
            // PROFORMA INVOICES
            'documents:proforma_invoice:list', 'documents:proforma_invoice:show', 'documents:proforma_invoice:edit', 'documents:proforma_invoice:create',
            'documents:proforma_invoice:delete', 'documents:proforma_invoice:duplicate',
            'documents:proforma_invoice:send', 'documents:proforma_invoice:download', 'documents:proforma_invoice:print',
            // CREDIT NOTE
            'documents:credit_note:list', 'documents:credit_note:show', 'documents:credit_note:edit', 'documents:credit_note:create',
            'documents:credit_note:delete',
            'documents:credit_note:send', 'documents:credit_note:download', 'documents:credit_note:print',
            // QUOTATION
            'documents:quotation:list', 'documents:quotation:show', 'documents:quotation:edit', 'documents:quotation:create',
            'documents:quotation:delete',
            'documents:quotation:send', 'documents:quotation:download', 'documents:quotation:print',
            'documents:quotation:approve', 'documents:quotation:reject',
            // PAYMENTS
            'documents:payments:list', 'documents:payments:create', 'documents:payments:delete', 'documents:payments:created_by_user',
            // CLIENT
            'client:list', 'client:show', 'client:edit', 'client:create', 'client:delete',
            // TOOLS
            'tools:export:list', 'tools:export:invoices',
            // SETTINGS
            'settings:show',
            'settings:contact_and_invoice:show', 'settings:contact_and_invoice:edit',
            'settings:bank_accounts:show', 'settings:bank_accounts:create', 'settings:bank_accounts:edit', 'settings:bank_accounts:delete',
            'settings:invoice:show', 'settings:invoice:edit', 'settings:invoice:delete',
            'settings:document_numbers:show', 'settings:document_numbers:create', 'settings:document_numbers:edit',
            'settings:company_api_key:show', 'settings:company_api_key:edit',
            // DASHBOARD
            'dashboard:show',
        ])->description(__('Administrator users can perform any action'));


        Jetstream::role('invoicer', __('Invoicer'), [
            // DOCUMENTS
            'documents:revision:restore', 'documents:issue:real_invoice',
            // INVOICES
            'documents:invoice:list', 'documents:invoice:show', 'documents:invoice:edit', 'documents:invoice:create', 'documents:invoice:delete',
            'documents:invoice:duplicate',
            'documents:invoice:send', 'documents:invoice:download', 'documents:invoice:print',
            // PROFORMA INVOICES
            'documents:proforma_invoice:list', 'documents:proforma_invoice:show', 'documents:proforma_invoice:edit', 'documents:proforma_invoice:create',
            'documents:proforma_invoice:duplicate',
            'documents:proforma_invoice:send', 'documents:proforma_invoice:download', 'documents:proforma_invoice:print',
            // CREDIT NOTE
            'documents:credit_note:list', 'documents:credit_note:show', 'documents:credit_note:edit', 'documents:credit_note:create',
            'documents:credit_note:send', 'documents:credit_note:download', 'documents:credit_note:print',
            // QUOTATION
            'documents:quotation:list', 'documents:quotation:show', 'documents:quotation:edit', 'documents:quotation:create',
            'documents:quotation:send', 'documents:quotation:download', 'documents:quotation:print',
            'documents:quotation:approve', 'documents:quotation:reject',
            // PAYMENTS
            'documents:payments:list', 'documents:payments:create',
            // CLIENT
            'client:list', 'client:show', 'client:edit', 'client:create', 'client:delete',
            // TOOLS
            'tools:export:list', 'tools:export:invoices',
            // SETTINGS
            'settings:show',
            'settings:contact_and_invoice:show',
            'settings:bank_accounts:show',
            'settings:invoice:show',
            'settings:document_numbers:show', 'settings:document_numbers:create', 'settings:document_numbers:edit',
            // DASHBOARD
            'dashboard:show',
        ])->description(__('Invoicers have the ability to read, create and update'));


        Jetstream::role('accountant', __('Accountant'), [
            // DOCUMENTS
            // INVOICES
            'documents:invoice:list', 'documents:invoice:show', 'documents:invoice:download', 'documents:invoice:print', 'documents:invoice:delete',
            // PROFORMA INVOICES
            'documents:proforma_invoice:list', 'documents:proforma_invoice:show', 'documents:proforma_invoice:download', 'documents:proforma_invoice:print',
            // CREDIT NOTE
            'documents:credit_note:list', 'documents:credit_note:show', 'documents:credit_note:download', 'documents:credit_note:print',
            // QUOTATION
            'documents:quotation:list', 'documents:quotation:show', 'documents:quotation:download', 'documents:quotation:print',
            // PAYMENTS
            'documents:payments:list',
            // CLIENT
            'client:list', 'client:show',
            // TOOLS
            'tools:export:list', 'tools:export:invoices',
            // DASHBOARD
            'dashboard:show',
        ])->description(__('Accountants have only the ability to read'));
    }
}
