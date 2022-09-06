<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentNumber;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExportController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function index(): Response
    {
        abort_if(!auth()->user()->hasCurrentTeamPermission('tools:export:list'), 401);

        $documentNumbers['invoice'] = DocumentNumber::dropdownSelect('invoice');
        $documentNumbers['proforma_invoice'] = DocumentNumber::dropdownSelect('proforma-invoice');

        return Inertia::render('Tools/Export', compact('documentNumbers'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param string                   $service
     *
     * @return \Illuminate\Http\Response|mixed|string
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function export(Request $request, string $service)
    {
        abort_if(!auth()->user()->hasCurrentTeamPermission('tools:export:invoices'), 401);

        $documents = Document::ofType(Document::TYPE_INVOICE)
                            ->whereBetween('date_created', [$request->startDate, $request->endDate])
                            ->when($request->status, fn($query) => $query->where('status', $request->status))
                            ->when($request->documentNumberId !== 'all', fn($query) => $query->where('invoice_number_id', $request->documentNumberId))
                            ->with('items')
                            ->get();

        $xml = view("export.{$service}.{$request->legislation}", ['documents' => $documents])->render();

        return response()->make($xml, 200, ['Cache-Control' => 'no-cache, must-revalidate']);
    }
}
