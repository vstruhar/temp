<?php

use App\Models\DocumentNumber;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameInvoiceTablePrefixesToDocumentPrefix extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('invoice_items', 'document_items');
        Schema::rename('invoice_numbers', 'document_numbers');
        Schema::rename('invoice_payments', 'document_payments');

        Schema::table('document_items', function (Blueprint $table) {
            $table->renameColumn('invoice_id', 'document_id');
        });

        Schema::table('document_payments', function (Blueprint $table) {
            $table->renameColumn('invoice_id', 'document_id');
        });

        DB::table('document_numbers')
          ->where('type', 'advance_invoice')
          ->update(['type' => DocumentNumber::TYPE_PROFORMA_INVOICE]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('document_items', 'invoice_items');
        Schema::rename('document_numbers', 'invoice_numbers');
        Schema::rename('document_payments', 'invoice_payments');

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->renameColumn('document_id', 'invoice_id');
        });

        Schema::table('invoice_payments', function (Blueprint $table) {
            $table->renameColumn('document_id', 'invoice_id');
        });

        DB::table('document_numbers')
          ->where('type', DocumentNumber::TYPE_PROFORMA_INVOICE)
          ->update(['type' => 'advance_invoice']);
    }
}
