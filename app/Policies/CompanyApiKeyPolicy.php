<?php

namespace App\Policies;

use App\Models\CompanyApiKey ;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyApiKeyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->hasCurrentTeamPermission('settings:company_api_key:show');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CompanyApiKey  $companyApiKey
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, CompanyApiKey $companyApiKey)
    {
        return $user->current_team_id === $companyApiKey->company_id && $user->hasCurrentTeamPermission('settings:company_api_key:show');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CompanyApiKey  $companyApiKey
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, CompanyApiKey $companyApiKey)
    {
        return $user->current_team_id === $companyApiKey->company_id && $user->hasCurrentTeamPermission('settings:company_api_key:edit');
    }
}
