<?php

use App\Models\DocumentItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCalculatedFromSubtotalColumnToDocumentItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_items', function (Blueprint $table) {
            $table->boolean('calculated_from_subtotal')->nullable()->after('description');
        });

        DB::table('document_items')->update(['calculated_from_subtotal' => true]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('document_items', function (Blueprint $table) {
            $table->dropColumn('calculated_from_subtotal');
        });
    }
}
