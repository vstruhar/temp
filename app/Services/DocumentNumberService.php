<?php

namespace App\Services;

use App\Models\DocumentNumber;
use App\Models\Team;
use App\Services\DocumentNumber\DocumentNumberGenerator;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class DocumentNumberService
{
    /**
     * @param \App\Models\DocumentNumber $documentNumber
     * @param \Illuminate\Support\Carbon $date
     *
     * @return string
     */
    public static function getNext(DocumentNumber $documentNumber, Carbon $date): string
    {
        $generator = new DocumentNumberGenerator($documentNumber, $date);

        try {
            return $generator->getNext();
        } catch (Exception $e) {
            Log::error('Failed to generate new invoice number', [
                'message'           => $e->getMessage(),
                'invoice_number_id' => $documentNumber->id,
                'trace'             => $e->getTraceAsString(),
            ]);
            return '';
        }
    }

    /**
     * @param \App\Models\Team $company
     *
     * @return void
     */
    public static function createDefault(Team $company): void
    {
        $documentNumberData = [
            'company_id' => $company->id,
            'period'     => DocumentNumber::PERIOD_YEARLY,
            'is_default' => true,
        ];

        DocumentNumber::create(
            array_merge($documentNumberData, [
                'name'   => 'Default',
                'type'   => DocumentNumber::TYPE_INVOICE,
                'format' => 'RRRRCCCC',
            ])
        );

        DocumentNumber::create(
            array_merge($documentNumberData, [
                'name'   => 'Default',
                'type'   => DocumentNumber::TYPE_PROFORMA_INVOICE,
                'format' => 'ZRRRRCCCC',
            ])
        );

        DocumentNumber::create(
            array_merge($documentNumberData, [
                'name'   => 'Default',
                'type'   => DocumentNumber::TYPE_CREDIT_NOTE,
                'format' => 'DRRRRCCCC',
            ])
        );
    }
}
