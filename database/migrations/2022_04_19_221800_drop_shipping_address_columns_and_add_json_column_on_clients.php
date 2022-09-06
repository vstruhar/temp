<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropShippingAddressColumnsAndAddJsonColumnOnClients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('shipping_address_name');
            $table->dropColumn('shipping_address_postal_code');
            $table->dropColumn('shipping_address_city');
            $table->dropColumn('shipping_address_address');
            $table->dropColumn('shipping_address_phone');
            $table->dropColumn('shipping_address_country');

            $table->jsonb('shipping_address')->nullable()->after('swift');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('shipping_address_name')->nullable()->after('country');
            $table->string('shipping_address_postal_code')->nullable()->after('country');
            $table->string('shipping_address_city')->nullable()->after('country');
            $table->string('shipping_address_address')->nullable()->after('country');
            $table->string('shipping_address_phone')->nullable()->after('country');
            $table->string('shipping_address_country')->nullable()->after('country');

            $table->dropColumn('shipping_address');
        });
    }
}
