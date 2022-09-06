<?php

use App\Http\Controllers\AdminLoginAsUserController;
use App\Http\Controllers\CheckAfterLoginController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\Company\CompanyAutocompleteController;
use App\Http\Controllers\Company\CompanyClientsController;
use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\Company\CompanyInvitationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Documents\DocumentDownloadController;
use App\Http\Controllers\Documents\DocumentItemAutocompleteController;
use App\Http\Controllers\Documents\DocumentNumbersController;
use App\Http\Controllers\Documents\DocumentPaymentsController;
use App\Http\Controllers\Documents\DocumentsController;
use App\Http\Controllers\Documents\DocumentSendController;
use App\Http\Controllers\Documents\DuplicateDocumentController;
use App\Http\Controllers\Documents\FiltersDataController;
use App\Http\Controllers\Documents\Invoice\IssueCreditNoteController;
use App\Http\Controllers\Documents\ProformaInvoice\IssueRealInvoiceController;
use App\Http\Controllers\Documents\Quotation\QuotationStatusController;
use App\Http\Controllers\GlobalDocumentsController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\RefreshCsrfTokenController;
use App\Http\Controllers\Settings\BankAccountSettingsController;
use App\Http\Controllers\Settings\CompanyApiKeyController;
use App\Http\Controllers\Settings\ContactAndInvoiceSettingsController;
use App\Http\Controllers\Settings\DefaultSettingsController;
use App\Http\Controllers\Settings\DocumentNumberController;
use App\Http\Controllers\Settings\InvoiceSettingsController;
use App\Http\Controllers\Tools\ExportController;
use App\Http\Controllers\Tools\PrintController;
use App\Http\Controllers\User\PasswordController;
use App\Http\Controllers\User\UserCompanyController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

//Route::get('/', [HomeController::class, 'index']);

Route::redirect('/', '/login');
Route::get('login/user/{id}', [AdminLoginAsUserController::class, 'quick']); // works only on local env
Route::get('login/{secret}', [AdminLoginAsUserController::class, 'login']);
Route::get('logout', [AuthenticatedSessionController::class, 'destroy']);

Route::group(['middleware' => ['auth:sanctum', 'active-users-only', 'verified', 'version-check']], function () {
    Route::get('csrf-token', RefreshCsrfTokenController::class);
    Route::get('language/{locale}', [LanguageController::class, 'setLocale'])->name('language.set_locale');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('user/route/check', [CheckAfterLoginController::class, 'check'])->name('user.route.check');
    Route::get('user/password/set', [PasswordController::class, 'form'])->name('user.password.form');
    Route::put('user/password/set', [PasswordController::class, 'update'])->name('user.password.update');
    Route::get('user/company/select', [UserCompanyController::class, 'select'])->name('user.company.select');

    Route::get('clients', [ClientController::class, 'index'])->name('clients');
    Route::get('clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::post('clients/create', [ClientController::class, 'store'])->name('clients.store');
    Route::get('clients/{client}', [ClientController::class, 'show'])->name('clients.show');
    Route::get('clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::put('clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('clients/{client}', [ClientController::class, 'destroy'])->name('clients.delete');

    Route::get('company/{companyId}/clients/dropdown-items', [CompanyClientsController::class, 'dropdownItems'])->name('company.clients.dropdown_items');

    Route::redirect('/documents', '/documents/invoices');

    Route::get('documents/search/global', [GlobalDocumentsController::class, 'search'])->name('documents.global.search');
    Route::get('documents/global/open/{id}', [GlobalDocumentsController::class, 'open'])->name('documents.global.open');

    Route::group(['prefix' => 'documents/{type}', 'where' => ['type' => 'invoices|proforma-invoices|credit-notes|quotations']], function () {
        Route::get('/', [DocumentsController::class, 'index'])->name('documents');
        Route::get('create', [DocumentsController::class, 'create'])->name('documents.create');
        Route::post('store', [DocumentsController::class, 'store'])->name('documents.store');
        Route::get('{document}', [DocumentsController::class, 'show'])->name('documents.show');
        Route::post('{document}/duplicate', [DuplicateDocumentController::class, 'store'])->name('documents.duplicate');
        Route::get('{document}/edit', [DocumentsController::class, 'edit'])->name('documents.edit');
        Route::put('{document}/update', [DocumentsController::class, 'update'])->name('documents.update');
        Route::put('{document}/revision/{revision}/restore', [DocumentsController::class, 'restoreRevision'])->name('documents.revision.restore');
        Route::delete('{document}/delete', [DocumentsController::class, 'destroy'])->name('documents.delete');
        Route::post('{document}/payment', [DocumentPaymentsController::class, 'store'])->name('documents.payments.store');
        Route::delete('{document}/payment/{payment}/delete', [DocumentPaymentsController::class, 'destroy'])->name('documents.payments.delete');
        Route::get('company/{company}/document-numbers', [DocumentNumbersController::class, 'index'])->name('documents.company.document_numbers');

        Route::get('{document}/download', [DocumentDownloadController::class, 'download'])->name('documents.download');
        Route::get('{document}/pdf', [DocumentDownloadController::class, 'pdf'])->name('documents.pdf');
        Route::get('download/latest', [DocumentDownloadController::class, 'downloadLatest'])->name('documents.download_latest');

        Route::get('{document}/send', [DocumentSendController::class, 'form'])->name('documents.send_form');
        Route::post('{document}/send', [DocumentSendController::class, 'send'])->name('documents.send');
        Route::get('items/autocomplete', [DocumentItemAutocompleteController::class, 'suggestions'])->name('documents.items.autocomplete');
    });

    Route::group(['prefix' => 'documents'], function () {
        Route::get('invoices/{document}/issue/real-invoice', [IssueRealInvoiceController::class, 'create'])->name('documents.issue.real_invoice.create');
        Route::post('invoices/issue/real-invoice', [IssueRealInvoiceController::class, 'store'])->name('documents.issue.real_invoice.store');

        Route::get('invoices/{document}/issue/credit-note', [IssueCreditNoteController::class, 'create'])->name('documents.issue.credit_note.create');
        Route::post('invoices/issue/credit-note', [IssueCreditNoteController::class, 'store'])->name('documents.issue.credit_note.store');

        Route::put('quotations/{document}/approve', [QuotationStatusController::class, 'approve'])->name('documents.quotations.approve');
        Route::put('quotations/{document}/reject', [QuotationStatusController::class, 'reject'])->name('documents.quotations.reject');

        Route::get('filters/data', [FiltersDataController::class, 'index'])->name('filters.data');
    });

    Route::group(['prefix' => 'tools'], function () {
        Route::redirect('print', 'print/invoices')->name('tools.print.index');
        Route::get('print/invoices', [PrintController::class, 'invoices'])->name('tools.print.invoices');
        Route::get('print/invoices/merged', [PrintController::class, 'getMergedInvoices'])->name('tools.print.invoices.merged');
        Route::get('print/invoices/download', [PrintController::class, 'downloadMergedInvoices'])->name('tools.print.invoices.download');

        Route::get('export', [ExportController::class, 'index'])->name('tools.export.index');
        Route::get('export/{service}', [ExportController::class, 'export'])->name('tools.export.file');
    });

    Route::group(['prefix' => 'settings'], function () {
        Route::redirect('/', '/settings/contact-and-invoice');
        Route::get('contact-and-invoice', [ContactAndInvoiceSettingsController::class, 'index'])->name('settings.contact_and_invoice');
        Route::post('contact-and-invoice', [ContactAndInvoiceSettingsController::class, 'save'])->name('settings.contact_and_invoice.save');
        Route::put('contact-and-invoice/tax', [ContactAndInvoiceSettingsController::class, 'updateTaxSettings'])->name('settings.contact_and_invoice.tax_settings.update');

        Route::get('bank-account', [BankAccountSettingsController::class, 'index'])->name('settings.bank_accounts');
        Route::post('bank-account', [BankAccountSettingsController::class, 'store'])->name('settings.bank_account.store');
        Route::delete('bank-account/{bankAccount}', [BankAccountSettingsController::class, 'destroy'])->name('settings.bank_account.delete');

        Route::get('logo-and-signature', [InvoiceSettingsController::class, 'index'])->name('settings.invoice');
        Route::post('logo-and-signature', [InvoiceSettingsController::class, 'store'])->name('settings.invoice.store');
        Route::delete('logo-and-signature/{type}', [InvoiceSettingsController::class, 'destroy'])->name('settings.invoice.delete');

        Route::get('document-number', [DocumentNumberController::class, 'index'])->name('settings.document_number');
        Route::post('document-number', [DocumentNumberController::class, 'store'])->name('settings.document_number.store');
        Route::put('document-number/{invoiceNumber}', [DocumentNumberController::class, 'update'])->name('settings.document_number.update');

        Route::get('default-settings', [DefaultSettingsController::class, 'index'])->name('settings.default_settings');
        Route::put('default-settings', [DefaultSettingsController::class, 'update'])->name('settings.default_settings.update');

        Route::get('company-api-key', [CompanyApiKeyController::class, 'index'])->name('settings.company_api_key');
        Route::put('company-api-key/reset', [CompanyApiKeyController::class, 'reset'])->name('settings.company_api_key.reset');
    });

    Route::get('company/create', [CompanyController::class, 'create'])->name('company.create');
    Route::post('company', [CompanyController::class, 'store'])->name('company.store');
    Route::get('company/autocomplete', [CompanyAutocompleteController::class, 'suggestions'])->name('company.autocomplete');
    Route::get('company/autocomplete/details', [CompanyAutocompleteController::class, 'companyDetails'])->name('company.autocomplete.details');
    Route::get('company/{team}', [CompanyController::class, 'show'])->name('company.show');
    Route::put('company/{team}', [CompanyController::class, 'update'])->name('company.update');
    Route::delete('company/{team}', [CompanyController::class, 'destroy'])->name('company.destroy');
    Route::get('company/validate/vat', [CompanyController::class, 'validateVat'])->name('company.validate.vat');

    Route::get('company-invitations/{invitation}', [CompanyInvitationController::class, 'accept'])->middleware(['signed'])->name('company-invitations.accept');
    Route::delete('company-invitations/{invitation}', [CompanyInvitationController::class, 'destroy'])->name('company-invitations.destroy');
});
