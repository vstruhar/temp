<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentDownloadController extends Controller
{
    /**
     * Download invoice pdf
     *
     * @param String               $type
     * @param \App\Models\Document $document
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function download(String $type, Document $document): StreamedResponse
    {
        $this->authorize('download', $document);

        $headers = [
            'Content-Description' => 'File Transfer',
            'Content-Type'        => 'application/pdf',
        ];

        return Storage::drive('invoices')->download($document->getPdfLocation(), null, $headers);
    }

    /**
     * @param String               $type
     * @param \App\Models\Document $document
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function pdf(String $type, Document $document)
    {
        $this->authorize('print', $document);

        $filepath = Storage::drive('invoices')->path($document->getPdfLocation());

        if (!File::exists($filepath)) {
            abort(404);
        }

        return response()->file($filepath, ['Cache-Control' => 'no-cache, must-revalidate']);
    }

    /**
     * Download the latest invoice pdf
     */
    public function downloadLatest(String $type): StreamedResponse
    {
        $document = Document::where('company_id', '=', auth()->user()->currentTeam->id)
                           ->with('company')
                           ->latest()
                           ->first();

        $this->authorize('download', $document);

        $headers = [
            'Content-Description' => 'File Transfer',
            'Content-Type'        => 'application/pdf',
        ];

        return Storage::drive('invoices')->download($document->getPdfLocation(), null, $headers);
    }
}
