<?php

namespace App\Http\Controllers\Documents\Quotation;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;

class QuotationStatusController extends Controller
{
    /**
     * @param \App\Models\Document $document
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Document $document): RedirectResponse
    {
        abort_if($document->type !== Document::TYPE_QUOTATION, 403);

        $this->authorize('approveQuotation', $document);

        $document->update(['status' => Document::STATUS_APPROVED]);

        return redirect()->route('documents.show', ['type' => 'quotations', 'document' => $document->id]);
    }

    /**
     * @param \App\Models\Document $document
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Document $document): RedirectResponse
    {
        abort_if($document->type !== Document::TYPE_QUOTATION, 403);

        $this->authorize('rejectQuotation', $document);

        $document->update(['status' => Document::STATUS_REJECTED]);

        return redirect()->route('documents.show', ['type' => 'quotations', 'document' => $document->id]);
    }
}
