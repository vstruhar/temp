<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Document;

class AddWithLogoAndBankAccountsColumnsToInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->boolean('with_logo')->nullable()->after('generate_qr_code');
            $table->json('bank_accounts')->nullable()->after('company_details');
        });

        DB::table('invoices')
          ->get()
          ->each(static function (Document $invoice) {
              $invoice->update([
                  'with_logo'     => $invoice->company->companyDetails->logo !== null,
                  'bank_accounts' => $invoice->company->bankAccounts,
              ]);
          });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('with_logo');
            $table->dropColumn('bank_accounts');
        });
    }
}
