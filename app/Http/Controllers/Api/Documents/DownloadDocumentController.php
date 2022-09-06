<?php

namespace App\Http\Controllers\Api\Documents;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadDocumentController extends Controller
{
    /**
     * @param string $path
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download(string $path): StreamedResponse
    {
        $headers = ['Content-Type' => 'application/pdf'];

        return Storage::disk('invoices')->download($path, null, $headers);
    }
}
