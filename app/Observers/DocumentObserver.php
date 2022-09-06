<?php

namespace App\Observers;

use App\Models\Document;
use App\Models\DocumentNumber;
use App\Models\PrintedDocument;
use Illuminate\Support\Str;

class DocumentObserver
{
    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    /**
     * Handle the Document "creating" event.
     *
     * @param  \App\Models\Document  $document
     * @return void
     */
    public function creating(Document $document)
    {
        $document->status = $this->getInitialStatus($document->type);
    }

    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\Document  $document
     * @return void
     */
    public function created(Document $document)
    {
        $document->update([
            'number_counter' => $this->getCounterFromNumber($document),
        ]);

        $document->searchable();
    }

    /**
     * Handle the Document "updated" event.
     *
     * @param  \App\Models\Document  $document
     * @return void
     */
    public function updated(Document $document)
    {
        $hasChangesThatWillAffectPdf = $document->wasChanged(Document::COLUMNS_AFFECTING_PDF);

        if ($hasChangesThatWillAffectPdf && in_array($document->type, [Document::TYPE_INVOICE, Document::TYPE_CREDIT_NOTE], true)) {
            PrintedDocument::where('document_id', $document->id)->delete();
        }
    }

    /**
     * Handle the Document "deleted" event.
     *
     * @param \App\Models\Document $document
     *
     * @return void
     */
    public function deleted(Document $document)
    {
        $document->unsearchable();
    }

    /**
     * Handle the Document "restored" event.
     *
     * @param \App\Models\Document $document
     *
     * @return void
     */
    public function restored(Document $document)
    {
        $document->searchable();
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    private function getInitialStatus(string $type): ?string
    {
        switch ($type) {
            case Document::TYPE_INVOICE:
            case Document::TYPE_PROFORMA_INVOICE: return Document::STATUS_WAITING_FOR_PAYMENT;
            case Document::TYPE_CREDIT_NOTE:      return Document::STATUS_PAYMENT_RECEIVED;
            case Document::TYPE_QUOTATION:        return Document::STATUS_WAITING_FOR_APPROVAL;
            default:                              return null;
        }
    }

    /**
     * @param \App\Models\Document $document
     *
     * @return string
     */
    public function getCounterFromNumber(Document $document): ?string
    {
        $documentNumber = DocumentNumber::withoutGlobalScope('company_invoice_numbers_only')
                                        ->where('id', $document->invoice_number_id)
                                        ->first();

        if ($documentNumber === null) {
            return null;
        }

        $format = '';
        $formatChars = (array) str_split($documentNumber->format);
        $count = count($formatChars);

        for ($i = 0; $i < $count; $i++) {
            if (($formatChars[$i] === '#') && isset($formatChars[$i + 1])) {
                $formatChars[$i + 1] = 'Ø';
            }
            $format .= $formatChars[$i];
        }

        $from = stripos($format, 'C');
        $to = strripos($format, 'C');

        return (int) (string) Str::of($document->number)->substr($from, $to);
    }
}
