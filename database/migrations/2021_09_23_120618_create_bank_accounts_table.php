<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('teams');
            $table->string('name');
            $table->string('currency', 120)->nullable();
            $table->string('number_account')->nullable();
            $table->string('bank_code', 120)->nullable();
            $table->string('iban', 120)->nullable();
            $table->string('swift', 120)->nullable();
            $table->boolean('default')->default(false);

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
        Schema::dropIfExists('bank_accounts');
    }
}
