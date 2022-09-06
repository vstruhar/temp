<?php

use App\Models\Document;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AddNumberCounterToDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->unsignedBigInteger('number_counter')->nullable()->after('number');
        });

        Document::orderBy('id')
                ->with('documentNumber')
                ->chunk(200, static function ($documents) {
                    $documents->each(function (Document $document) {
                        if ($document->documentNumber !== null) {
                            $from = stripos($document->documentNumber->format, 'C');
                            $to   = strripos($document->documentNumber->format, 'C');

                            $counter = (string)Str::of($document->number)->substr($from, $to);

                            // numbers only
                            $counter = preg_replace("/[^0-9]/", "", $counter);

                            $document->update(['number_counter' => empty($counter) ? null : (int) $counter]);
                        }
                    });
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
            $table->dropColumn('number_counter');
        });
    }
}
