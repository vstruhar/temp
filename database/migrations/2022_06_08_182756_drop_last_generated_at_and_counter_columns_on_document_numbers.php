<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropLastGeneratedAtAndCounterColumnsOnDocumentNumbers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_numbers', function (Blueprint $table) {
            $table->renameColumn('last_generated_at', 'last_generated_at_DELETED');
            $table->renameColumn('counter', 'counter_DELETED');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('document_numbers', function (Blueprint $table) {
            $table->renameColumn('last_generated_at_DELETED', 'last_generated_at');
            $table->renameColumn('counter_DELETED', 'counter');
        });
    }
}
