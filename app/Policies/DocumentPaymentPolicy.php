<?php

namespace App\Policies;

use App\Models\DocumentPayment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentPaymentPolicy
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
        return $user->hasCurrentTeamPermission('documents:payments:list');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->currentTeam !== null && $user->hasCurrentTeamPermission('documents:payments:create');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User            $user
     * @param  \App\Models\DocumentPayment $payment
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, DocumentPayment $payment)
    {
        return $user->current_team_id === $payment->document->company_id && $user->hasCurrentTeamPermission('documents:payments:delete');
    }
}
