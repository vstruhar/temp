<?php

namespace App\Http\Controllers\Api\Documents;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\DocumentNumber;
use Illuminate\Http\JsonResponse;

class DocumentNumbersController extends BaseApiController
{
    /**
     * @param string $type
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(string $type): JsonResponse
    {
        $documentNumbers = DocumentNumber::withoutGlobalScope('company_invoice_numbers_only')
                                         ->where('company_id', $this->company->id)
                                         ->where('type', $type)
                                         ->orderBy('name')
                                         ->get(['id', 'name']);

        return $this->response($documentNumbers);
    }
}
