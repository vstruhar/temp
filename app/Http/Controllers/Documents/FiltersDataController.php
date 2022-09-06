<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Models\DocumentNumber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FiltersDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'document_type' => ['nullable', 'in:invoice,proforma-invoice,credit-note,quotation'],
        ]);

        return response()->json([
            'documentNumbersDropdownItems' => DocumentNumber::dropdownSelect($request->document_type),
        ]);
    }
}
