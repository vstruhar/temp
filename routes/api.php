<?php

use App\Http\Controllers\Api\Documents\DocumentNumbersController;
use App\Http\Controllers\Api\Documents\DownloadDocumentController;
use App\Http\Controllers\Api\Documents\InvoiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['prefix' => 'documents', 'middleware' => ['locale:en']], function () {
    Route::post('invoice', [InvoiceController::class, 'store'])
         ->name('api.documents.invoice.create');

    Route::get('numbers/{type}', [DocumentNumbersController::class, 'index'])
         ->where('type', 'invoice|proforma-invoice|credit-note')
         ->name('api.documents.numbers');

    Route::get('pdf/{path}', [DownloadDocumentController::class, 'download'])
         ->where('path', '(.*)')
         ->name('api.documents.pdf.download');
});
