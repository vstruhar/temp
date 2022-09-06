<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_items', function (Blueprint $table) {

            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->integer('price_with_tax')->nullable();
            $table->integer('price')->nullable();
            $table->integer('tax')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('unit')->nullable();
            $table->integer('discount_amount')->nullable();
            $table->integer('discount_percent')->nullable();
            $table->timestamps();

            $table->index(["invoice_id"], 'fk_price_invoices1_idx');

            $table->foreign('invoice_id', 'fk_price_invoices1_idx')
                ->references('id')->on('invoices')
                ->onDelete('no action')
                ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_items');
    }
}
