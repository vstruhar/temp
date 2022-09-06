<?php

use App\Models\BankAccount;
use App\Models\DefaultItem;
use App\Models\DocumentNumber;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDefaultItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('default_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('company_id')->constrained('teams');
            $table->string('type');
            $table->unsignedBigInteger('default_id');
        });

        DB::table('team_user')
          ->get()
          ->each(function ($row) {
              $bankAccountId = BankAccount::where('company_id', $row->team_id)->where('default', true)->value('id');

              if ($bankAccountId) {
                  DB::table('default_items')->insert([
                      'user_id'    => $row->user_id,
                      'company_id' => $row->team_id,
                      'type'       => DefaultItem::TYPE_BANK_ACCOUNT,
                      'default_id' => $bankAccountId,
                  ]);
              }

              $rows = DocumentNumber::where('company_id', $row->team_id)
                                    ->where('is_default', true)
                                    ->get('id')
                                    ->map(static function ($item) use ($row) {
                                        return [
                                            'user_id'    => $row->user_id,
                                            'company_id' => $row->team_id,
                                            'type'       => DefaultItem::TYPE_DOCUMENT_NUMBER,
                                            'default_id' => $item->id,
                                        ];
                                    })
                                    ->toArray();

              DB::table('default_items')->insert($rows);
          });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('default_items');
    }
}
