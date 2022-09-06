<?php

namespace App\Http\Controllers\Documents\Invoice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\InvoiceStoreRequest;
use App\Models\Client;
use App\Models\DefaultItem;
use App\Models\Document;
use App\Models\DocumentNumber;
use App\Services\CountriesService;
use App\Services\PdfInvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class IssueCreditNoteController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @param \App\Models\Document $document
     *
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(Document $document): \Inertia\Response
    {
        $this->authorize('issueCreditNote', $document);

        abort_if($document->type !== Document::TYPE_INVOICE, Response::HTTP_NOT_ACCEPTABLE);

        /** @var \App\Models\User $user */
        $user           = auth()->user();
        $company        = $user->currentTeam;
        $companyDetails = $company->companyDetails;

        $companyDetailsMissing = !$companyDetails || !$companyDetails->name || $companyDetails->tax_profile === null || !$companyDetails->organization_id;

        $document->load('items');

        return Inertia::render('Documents/CreditNotes/Create', [
                'type'                  => 'credit-notes',
                'document'              => $document,
                'companyDetails'        => $companyDetails,
                'bankAccounts'          => $company->bankAccounts,
                'clients'               => Client::dropdownOptions($document->client_id, ['*']),
                'documentCompanyName'   => $user->getCompanyNameInSnakeCase(),
                'companyDetailsMissing' => $companyDetailsMissing,
                'documentNumberOptions' => DocumentNumber::dropdownSelect(DocumentNumber::TYPE_CREDIT_NOTE, ['default', 'next', 'period'], $company->id),
                'countryDropdownItems'  => CountriesService::dropdownOptions(),
                'userDefaults'          => [
                    'issuedBy' => DefaultItem::getIssuedBy([DefaultItem::TYPE_ISSUED_BY]),
                ],
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \App\Http\Requests\Invoice\InvoiceStoreRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Throwable
     */
    public function store(InvoiceStoreRequest $request): RedirectResponse
    {
        $invoice = Document::findOrFail($request->invoice_id);

        $this->authorize('issueCreditNote', $invoice);

        abort_if($invoice->type !== Document::TYPE_INVOICE, Response::HTTP_NOT_ACCEPTABLE);

        $data = $request->all();

        $companyId = auth()->user()->current_team_id;

        $data['type']            = Document::TYPE_CREDIT_NOTE;
        $data['status']          = Document::STATUS_PAYMENT_RECEIVED;
        $data['bank_accounts']   = data_get($request->billing_data, 'bank_accounts');
        $data['with_logo']       = data_get($request->billing_data, 'with_logo');
        $data['company_details'] = data_get($request->billing_data, 'company_details');
        $data['company_id']      = $companyId;

        DB::transaction(function () use ($companyId, $invoice, $request, $data) {
            $document = Document::create($data);

            $invoice->update([
                'status'         => Document::STATUS_CANCELLED,
                'credit_note_id' => $document->id,
            ]);

            foreach ($data['items'] as $item) {
                $document->items()->create([
                    'description'      => $item['description'],
                    'discount_amount'  => $item['discount_amount'],
                    'discount_percent' => $item['discount_percent'],
                    'name'             => $item['name'],
                    'price'            => $item['price'],
                    'price_with_tax'   => $item['price_with_tax'],
                    'quantity'         => $item['quantity'],
                    'tax'              => $item['tax'],
                    'unit'             => $item['unit'],
                    'image'            => $item['image'],
                ]);
            }

            optional($document->documentNumber)->update(['current_counter' => 0]);

            PdfInvoiceService::generate($document, $companyId);
        });

        return redirect()->route('documents', 'credit-notes')->banner(__('Credit note was created successfully'));
    }
}
