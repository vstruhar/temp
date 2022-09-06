<?php

namespace App\Policies;

use App\Models\DocumentNumber;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentNumberPolicy
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
        return $user->hasCurrentTeamPermission('settings:document_numbers:show');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User           $user
     * @param  \App\Models\DocumentNumber $invoiceNumber
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, DocumentNumber $invoiceNumber)
    {
        return $user->current_team_id === $invoiceNumber->company_id && $user->hasCurrentTeamPermission('settings:document_numbers:show');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->currentTeam !== null && $user->hasCurrentTeamPermission('settings:document_numbers:create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User           $user
     * @param  \App\Models\DocumentNumber $invoiceNumber
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, DocumentNumber $invoiceNumber)
    {
        return $user->current_team_id === $invoiceNumber->company_id && $user->hasCurrentTeamPermission('settings:document_numbers:edit');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User          $user
     * @param \App\Models\DocumentNumber $invoiceNumber
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, DocumentNumber $invoiceNumber)
    {
        return $user->current_team_id === $invoiceNumber->company_id && $user->hasCurrentTeamPermission('settings:document_numbers:delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User          $user
     * @param \App\Models\DocumentNumber $invoiceNumber
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, DocumentNumber $invoiceNumber)
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param \App\Models\DocumentNumber  $invoiceNumber
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, DocumentNumber $invoiceNumber)
    {
        //
    }
}
