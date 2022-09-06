<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;
use Laravel\Jetstream\TeamInvitation as TeamInvitationModel;

class CompanyInvitation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The team invitation instance.
     *
     * @var \Laravel\Jetstream\TeamInvitation
     */
    public $invitation;

    /**
     * @var string
     */
    public $companyNames;

    /**
     * Create a new message instance.
     *
     * @param  \Laravel\Jetstream\TeamInvitation  $invitation
     * @param  \Illuminate\Support\Collection  $companyNames
     */
    public function __construct(TeamInvitationModel $invitation, Collection $companyNames)
    {
        $this->invitation   = $invitation;

        $lastCompanyName = $companyNames->pop();

        $this->companyNames = $companyNames->count() === 0
            ? $lastCompanyName
            : $companyNames->implode(', ') .' a '. $lastCompanyName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.company-invitation', [
            'acceptUrl' => URL::signedRoute('company-invitations.accept', ['invitation' => $this->invitation]),
        ])->subject(__('You were invited to join a company'));
    }
}
