<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameIssuedByColumnsOnInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', static function (Blueprint $table) {
            $table->renameColumn('invoice_issued', 'issued_by');
            $table->renameColumn('email', 'issued_by_email');
            $table->renameColumn('phone', 'issued_by_phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', static function (Blueprint $table) {
            $table->renameColumn('issued_by', 'invoice_issued');
            $table->renameColumn('issued_by_email', 'email');
            $table->renameColumn('issued_by_phone', 'phone');
        });
    }
}
