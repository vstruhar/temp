<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\PrintedDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Jurosh\PDFMerge\PDFMerger;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PrintController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function invoices(): Response
    {
        /** @var \App\Models\User $user */
        $user      = auth()->user();
        $companyId = $user->current_team_id;

        $rows = Document::query()
                        ->selectRaw(
                            "COUNT(*) AS count," .
                            "DATE_FORMAT(date_created, '%m.%Y') month_year," .
                            "DATE_FORMAT(date_created, '%M') month," .
                            "DATE_FORMAT(date_created, '%Y') year"
                        )
                        ->where('company_id', $companyId)
                        ->whereIn('type', [Document::TYPE_INVOICE, Document::TYPE_CREDIT_NOTE])
                        ->whereDoesntHave('printedByAuthUser')
                        ->groupBy(['month_year', 'month', 'year'])
                        ->orderBy('month_year', 'desc')
                        ->get();

        return Inertia::render('Tools/Print', ['section' => 'Invoices', 'data' => compact('rows')]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function getMergedInvoices(Request $request): BinaryFileResponse
    {
        /** @var \App\Models\User $user */
        $user      = auth()->user();
        $companyId = $user->current_team_id;

        $documents = Document::query()
                             ->selectRaw("*, DATE_FORMAT(date_created, '%m.%Y') month_year")
                             ->where('company_id', $companyId)
                             ->whereIn('type', [Document::TYPE_INVOICE, Document::TYPE_CREDIT_NOTE])
                             ->whereDoesntHave('printedByAuthUser')
                             ->orderBy('date_created', 'desc')
                             ->orderBy('created_at', 'desc');

        foreach ($request->periods as $period) {
            $documents->orHaving('month_year', $period);
        }

        $pdf = new PDFMerger;
        $printedDocuments = [];

        $documents->each(static function (Document $document) use ($companyId, $user, &$pdf, &$printedDocuments) {
            $filepath = Storage::drive('invoices')->path($document->getPdfLocation());

            $pdf->addPDF($filepath);

            $printedDocuments[] = [
                'user_id'     => $user->id,
                'company_id'  => $companyId,
                'document_id' => $document->id,
            ];
        });

        $tempFilepath = storage_path('app/temp/print_merged/' . auth()->id() . '.pdf');

        $pdf->merge('file', $tempFilepath);

        PrintedDocument::insert($printedDocuments);

        return response()->file($tempFilepath, ['Cache-Control' => 'no-cache, must-revalidate']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadMergedInvoices(): StreamedResponse
    {
        $headers = [
            'Content-Description' => 'File Transfer',
            'Content-Type'        => 'application/pdf',
        ];

        return Storage::drive('temp')->download('print_merged/' . auth()->id() . '.pdf', null, $headers);
    }
}
