<?php

namespace App\Http\Controllers\Documents\ProformaInvoice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\InvoiceStoreRequest;
use App\Models\Client;
use App\Models\Document;
use App\Models\DocumentNumber;
use App\Models\DocumentPayment;
use App\Services\CountriesService;
use App\Services\DocumentNumberService;
use App\Services\PdfInvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class IssueRealInvoiceController extends Controller
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
        $this->authorize('issueRealInvoice', $document);

        abort_if(!in_array($document->type, [Document::TYPE_PROFORMA_INVOICE, Document::TYPE_QUOTATION], true), Response::HTTP_NOT_ACCEPTABLE);

        /** @var \App\Models\User $user */
        $user           = auth()->user();
        $company        = $user->currentTeam;
        $companyDetails = $company->companyDetails;
        $bankAccounts   = $company->bankAccounts;

        $clients               = Client::where('company_id', $company->id)->orderBy('name')->get();
        $documentCompanyName   = $user->getCompanyNameInSnakeCase();
        $isTaxPayer            = optional($companyDetails)->isTaxable();
        $companyDetailsMissing = !$companyDetails || !$companyDetails->name || $companyDetails->tax_profile === null || !$companyDetails->organization_id;
        $documentNumberOptions = DocumentNumber::dropdownSelect('invoice', ['default', 'next']);

        $countryDropdownItems = CountriesService::dropdownOptions();
        $type = 'invoices';
        $issuingRealInvoice = true;

        $document->load('items');

        return Inertia::render('Documents/Invoices/Create', compact(
                'type', 'document', 'companyDetails', 'bankAccounts', 'clients', 'documentCompanyName', 'isTaxPayer',
                'companyDetailsMissing', 'documentNumberOptions', 'countryDropdownItems', 'issuingRealInvoice'
            )
        );
    }

    /**
     * @param \App\Http\Requests\Invoice\InvoiceStoreRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Throwable
     */
    public function store(InvoiceStoreRequest $request): RedirectResponse
    {
        $sourceDocument = Document::findOrFail($request->creating_from_document_id);

        $this->authorize('issueRealInvoice', $sourceDocument);

        abort_if(!in_array($sourceDocument->type, [Document::TYPE_PROFORMA_INVOICE, Document::TYPE_QUOTATION], true), Response::HTTP_NOT_ACCEPTABLE);

        $data = $request->all();

        $companyId = auth()->user()->current_team_id;

        $data['type']            = Document::TYPE_INVOICE;
        $data['bank_accounts']   = data_get($request->billing_data, 'bank_accounts');
        $data['with_logo']       = data_get($request->billing_data, 'with_logo');
        $data['company_details'] = data_get($request->billing_data, 'company_details');
        $data['company_id']      = $companyId;

        $data[Str::replace('-', '_', $sourceDocument->type) . '_id'] = $request->creating_from_document_id;

        DB::transaction(function () use ($request, $data, $sourceDocument, $companyId) {
            $document = Document::create($data);

            $sourceDocument->update(['invoice_id' => $document->id]);

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
                ]);
            }

            // transfer payments
            DocumentPayment::insert(
                DocumentPayment::where('document_id', $request->creating_from_document_id)
                               ->whereNull('deleted_at')
                               ->get()
                               ->map(function ($payment) use ($document) {
                                   return array_merge($payment->toArray(), [
                                       'id'           => null,
                                       'document_id'  => $document->id,
                                       'amount'       => $payment->amount * 100,
                                       'payment_date' => $payment->payment_date->toDateTimeString(),
                                       'updated_at'   => $payment->updated_at->toDateTimeString(),
                                       'created_at'   => $payment->created_at->toDateTimeString(),
                                   ]);
                               })
                               ->toArray()
            );

            $document->updateStatus();

            optional($document->documentNumber)->update(['current_counter' => 0]);

            PdfInvoiceService::generate($document, $companyId);
        });

        return redirect()->route('documents', 'invoices')->banner(__('Invoice was created successfully'));
    }
}
