<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendInvoice extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    // public $invoiceId;
    public $message;
    public $subject;
    public $pdfInvoicePath;
    public $senderEmail;
    public $companyName;
    public $number;

    /**
     * SendInvoice constructor.
     *
     * @param  array  $data
     * @param  string  $pdfInvoicePath
     */
    public function __construct(array $data, string $pdfInvoicePath)
    {
        // $this->invoiceId      = $data['invoice']['id'] ?? $data['downPayment']['id'];
        $this->message        = $data['message'];
        $this->subject        = $data['name'];
        $this->senderEmail    = $data['senderEmail'];
        $this->companyName    = $data['companyName'];
        $this->number         = $data['number'];
        $this->pdfInvoicePath = $pdfInvoicePath;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.invoice')
                    ->replyTo($this->senderEmail)
                    ->subject($this->subject)
                    ->attach($this->pdfInvoicePath, ['as' => $this->companyName.'_'.$this->number.'.pdf', 'mime' => 'application/pdf']);
    }
}
