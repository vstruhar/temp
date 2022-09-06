<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminAccountAccessRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_account_access_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requested_by_user_id');
            $table->unsignedBigInteger('access_client_id');
            $table->string('secret');
            $table->timestamp('ttl');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_account_access_requests');
    }
}
