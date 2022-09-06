<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentItemAutocompleteController extends Controller
{
    /**
     * @param string                   $type
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggestions(string $type, Request $request): JsonResponse
    {
        $request->validate(['name' => 'required|min:3']);

        $items = DocumentItem::query()
                             ->search($request->name)
                             ->whereHas('document', function (Builder $query) {
                                 $query->where('company_id', auth()->user()->currentTeam->id)
                                       ->whereIn('type', [Document::TYPE_INVOICE, Document::TYPE_PROFORMA_INVOICE]);
                             })
                             ->select(['name', 'price', 'unit'])
                             ->groupByRaw('name, price, unit')
                             ->limit(10)
                             ->get();

        return response()->json($items);
    }
}
