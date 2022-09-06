<?php

use App\Models\Document;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetCancelledStatusToInvoicesWithCreditNote extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('documents')
          ->whereNotNull('credit_note_id')
          ->update(['status' => Document::STATUS_CANCELLED]);
    }
}
