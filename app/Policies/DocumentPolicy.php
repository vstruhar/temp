<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentPolicy
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
        return $user->hasCurrentTeamPermission('documents:invoice:list');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User     $user
     * @param  \App\Models\Document $document
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Document $document)
    {
        return $user->current_team_id === $document->company_id && $user->hasCurrentTeamPermission('documents:invoice:show');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->currentTeam !== null && $user->hasCurrentTeamPermission('documents:invoice:create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User     $user
     * @param  \App\Models\Document $document
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Document $document)
    {
        return $user->current_team_id === $document->company_id && $user->hasCurrentTeamPermission('documents:invoice:edit');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User    $user
     * @param \App\Models\Document $document
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Document $document)
    {
        return $user->current_team_id === $document->company_id && $user->hasCurrentTeamPermission('documents:invoice:delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User    $user
     * @param \App\Models\Document $document
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Document $document)
    {
        return $user->current_team_id === $document->company_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param \App\Models\Document  $document
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Document $document)
    {
        return false;
    }

    /**
     * Determine whether the user can send the model.
     *
     * @param  \App\Models\User     $user
     * @param  \App\Models\Document $document
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function send(User $user, Document $document)
    {
        return $user->current_team_id === $document->company_id && $user->hasCurrentTeamPermission('documents:invoice:send');
    }

    /**
     * Determine whether the user can download the model.
     *
     * @param  \App\Models\User     $user
     * @param  \App\Models\Document $document
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function download(User $user, Document $document)
    {
        return $user->current_team_id === $document->company_id && $user->hasCurrentTeamPermission('documents:invoice:download');
    }

    /**
     * Determine whether the user can download the model.
     *
     * @param  \App\Models\User     $user
     * @param  \App\Models\Document $document
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function print(User $user, Document $document)
    {
        return $user->current_team_id === $document->company_id && $user->hasCurrentTeamPermission('documents:invoice:print');
    }

    /**
     * Determine whether the user can download the model.
     *
     * @param  \App\Models\User     $user
     * @param  \App\Models\Document $document
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function duplicate(User $user, Document $document)
    {
        $companyIds = $user->allTeams()->pluck('id');

        return collect($companyIds)->contains($document->company_id) && $user->hasCurrentTeamPermission('documents:invoice:duplicate');
    }

    /**
     * Determine whether the user can restore invoice revision.
     *
     * @param \App\Models\User      $user
     * @param  \App\Models\Document $document
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restoreRevision(User $user, Document $document)
    {
        return $user->current_team_id === $document->company_id && $user->hasCurrentTeamPermission('documents:revision:restore');
    }

    /**
     * Determine whether the user can issue real invoice.
     *
     * @param \App\Models\User      $user
     * @param  \App\Models\Document $document
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function issueRealInvoice(User $user, Document $document)
    {
        return $user->current_team_id === $document->company_id && $user->hasCurrentTeamPermission('documents:issue:real_invoice');
    }

    /**
     * Determine whether the user can issue credit note.
     *
     * @param \App\Models\User      $user
     * @param  \App\Models\Document $document
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function issueCreditNote(User $user, Document $document)
    {
        return $user->current_team_id === $document->company_id && $user->hasCurrentTeamPermission('documents:credit_note:create');
    }

    /**
     * Determine whether the user can approve quotation.
     *
     * @param \App\Models\User      $user
     * @param  \App\Models\Document $document
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function approveQuotation(User $user, Document $document)
    {
        return $user->current_team_id === $document->company_id && $user->hasCurrentTeamPermission('documents:quotation:approve');
    }

    /**
     * Determine whether the user can reject quotation.
     *
     * @param \App\Models\User      $user
     * @param  \App\Models\Document $document
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function rejectQuotation(User $user, Document $document)
    {
        return $user->current_team_id === $document->company_id && $user->hasCurrentTeamPermission('documents:quotation:reject');
    }
}
