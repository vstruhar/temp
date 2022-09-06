<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('teams');
            $table->foreignId('client_id')->nullable();
            $table->string('name')->nullable();
            $table->string('number');
            $table->integer('amount')->nullable();
            $table->integer('amount_with_tax')->nullable();
            $table->integer('discount')->nullable();
            $table->dateTime('date_created');
            $table->dateTime('date_added')->nullable();
            $table->dateTime('billing_date')->nullable();
            $table->string('variable_symbol')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('constant_symbol')->nullable();
            $table->string('order_number')->nullable();
            $table->string('invoice_currency')->nullable();
            $table->text('note_before_items')->nullable();
            $table->text('note')->nullable();
            $table->string('invoice_issued')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->json('client')->nullable();
            $table->boolean('taxable')->default(1);
            $table->timestamps();

            $table->index(["client_id"], 'fk_invoice_clients1_idx');

            $table->index(["company_id"], 'fk_bills_users1_idx');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
