<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->index('name');
            $table->index('number');
            $table->index('amount');
            $table->index('amount_with_tax');
            $table->index('order_number');
            $table->index('variable_symbol');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex('name');
            $table->dropIndex('number');
            $table->dropIndex('amount');
            $table->dropIndex('amount_with_tax');
            $table->dropIndex('order_number');
            $table->dropIndex('variable_symbol');
        });
    }
}
