<?php

namespace App\Policies;

use App\Models\CompanyDetails;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyDetailsPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CompanyDetails  $companyDetails
     *
     * @return bool
     */
    public function view(User $user, CompanyDetails $companyDetails)
    {
        return $user->current_team_id === $companyDetails->company_id && $user->hasCurrentTeamPermission('settings:invoice:show');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->currentTeam !== null && $user->hasCurrentTeamPermission('settings:invoice:edit');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CompanyDetails  $companyDetails
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, CompanyDetails $companyDetails)
    {
        return $user->current_team_id === $companyDetails->company_id && $user->hasCurrentTeamPermission('settings:contact_and_invoice:edit');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CompanyDetails  $companyDetails
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, CompanyDetails $companyDetails)
    {
        return $user->current_team_id === $companyDetails->company_id && $user->hasCurrentTeamPermission('settings:invoice:delete');
    }
}
