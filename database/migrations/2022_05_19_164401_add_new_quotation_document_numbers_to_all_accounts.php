<?php

use App\Models\DocumentNumber;
use App\Models\Team;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewQuotationDocumentNumbersToAllAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Team::all()
            ->each(static function (Team $team) {
                DocumentNumber::create([
                    'company_id'        => $team->id,
                    'name'              => 'Default',
                    'type'              => DocumentNumber::TYPE_QUOTATION,
                    'period'            => DocumentNumber::PERIOD_YEARLY,
                    'format'            => 'PRRRRCCCC',
                    'is_default'        => true,
                    'last_generated_at' => null,
                    'counter'           => 0,
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
        DocumentNumber::where('type', DocumentNumber::TYPE_CREDIT_NOTE)->delete();
    }
}
