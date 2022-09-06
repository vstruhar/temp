<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRevisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('revisionable_type');
            $table->unsignedBigInteger('revisionable_id');
            $table->json('data');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('restored_at')->nullable();

            $table->index('user_id');
            $table->index('revisionable_type');
            $table->index('revisionable_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('revisions');
    }
}
