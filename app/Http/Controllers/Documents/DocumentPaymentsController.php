<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\InvoicePaymentRequest;
use App\Models\Document;
use App\Models\DocumentPayment;
use Illuminate\Http\Response;

class DocumentPaymentsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param String                                           $type
     * @param \App\Models\Document                             $document
     * @param \App\Http\Requests\Invoice\InvoicePaymentRequest $request
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(String $type, Document $document, InvoicePaymentRequest $request)
    {
        $this->authorize('create', DocumentPayment::class);

        abort_if($document->status === Document::STATUS_PAYMENT_RECEIVED, Response::HTTP_NOT_ACCEPTABLE);

        $document->payments()
                 ->create([
                    'created_by_user_id' => auth()->id(),
                    'payment_method'     => $request->payment_method,
                    'amount'             => $request->amount,
                    'payment_date'       => $request->payment_date,
                    'comment'            => $request->comment,
                ]);

        $document->updateStatus();

        return redirect()->back()->banner(__('Payment was added successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param String                      $type
     * @param \App\Models\Document        $document
     * @param \App\Models\DocumentPayment $payment
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(String $type, Document $document, DocumentPayment $payment)
    {
        $this->authorize('delete', $payment);

        $payment->delete();
        $document->updateStatus();

        return redirect()->route('documents.show', [$type, $document])->banner(__('Payment was deleted successfully'));
    }
}
