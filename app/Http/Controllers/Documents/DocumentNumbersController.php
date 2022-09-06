<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Models\DocumentNumber;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DocumentNumbersController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param string                   $type
     * @param \App\Models\Team         $company
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, string $type, Team $company): JsonResponse
    {
        $date = Carbon::parse($request->date);

        return response()->json([
            'items' => DocumentNumber::dropdownSelect(
                Str::singular($type),
                ['default', 'next', 'period'],
                $company->id,
                $date
            ),
        ]);
    }
}
