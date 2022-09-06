<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddValueColumnToDefaultItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('default_items', function (Blueprint $table) {
            $table->unsignedBigInteger('default_id')->nullable()->change();
            $table->text('value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('default_items', function (Blueprint $table) {
            $table->unsignedBigInteger('default_id')->change();
            $table->dropColumn('value');
        });
    }
}
