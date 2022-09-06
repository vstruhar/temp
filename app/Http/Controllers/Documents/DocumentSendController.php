<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendInvoiceRequest;
use App\Mail\SendInvoice;
use App\Models\Client;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class DocumentSendController extends Controller
{
    /**
     * @param String               $type
     * @param \App\Models\Document $document
     *
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function form(String $type, Document $document): Response
    {
        $this->authorize('view', $document);

        /** @var \App\Models\User $sender */
        $sender = auth()->user();

        $bankAccountNumber = optional($document->company->bankAccountDefault)->number_account;

        if (!$bankAccountNumber) {
            $bankAccountNumber = $document->company->bankAccounts
                ? $document->company->bankAccounts()->latest()->first()
                : '';
        }

        $companyName  = $sender->getCompanyName();
        $emailSubject = $companyName . ' ' . $document->getTypeTranslated() . ": {$document->number}";

        $document->load('client');

        $component = Str::of($type)->camel()->ucfirst();

        return Inertia::render("Documents/{$component}/Send", compact('document', 'sender', 'bankAccountNumber', 'emailSubject'));
    }

    /**
     * @param String                                $type
     * @param \App\Models\Document                  $document
     * @param \App\Http\Requests\SendInvoiceRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function send(String $type, Document $document, SendInvoiceRequest $request): RedirectResponse
    {
        $this->authorize('send', $document);

        $data = $request->all();

        $data['number']      = $document->number;
        $data['companyName'] = auth()->user()->getCompanyNameInSnakeCase();
        $data['senderEmail'] = $document->company->user->email;

        // update client email if he doesn't have it
        $currentClient = Client::find($document->client['id']);

        if ($currentClient && empty($currentClient->email)) {
            $currentClient->update(['email' => $request->email]);
        }

        $pdfInvoicePath = storage_path()."/app/invoices/pdf/{$document->company->id}/{$document->id}.pdf";

        Mail::to($data['email'])->send(new SendInvoice($data, $pdfInvoicePath));

        return redirect()->route('documents', $type)->banner(__('Email was sent successfully'));
    }
}
