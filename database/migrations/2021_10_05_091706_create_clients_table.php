<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable();
            $table->string('name')->nullable();
            $table->string('address')->nullable();
            $table->string('organization_id')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('email')->nullable();
            $table->string('tax')->nullable();
            $table->string('phone')->nullable();
            $table->string('city')->nullable();
            $table->string('value_added_tax')->nullable();
            $table->string('fax')->nullable();
            $table->string('country')->nullable();
            $table->string('shipping_address_name')->nullable();
            $table->string('shipping_address_postal_code')->nullable();
            $table->string('shipping_address_city')->nullable();
            $table->string('shipping_address_address')->nullable();
            $table->string('shipping_address_phone')->nullable();
            $table->string('shipping_address_country')->nullable();
            $table->string('number_account')->nullable();
            $table->string('bank_code')->nullable();
            $table->string('iban')->nullable();
            $table->string('swift')->nullable();
            $table->text('contacts')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
