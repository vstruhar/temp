<?php

use App\Models\CompanyApiKey;
use App\Models\Team;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GenerateCompanyApiKeyForEachCompany extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Team::all()
            ->each(function (Team $team) {
                do {
                    $newKey = Str::random(46);
                } while (CompanyApiKey::where('key', $newKey)->exists());

                $team->companyApiKey()->create(['key' => $newKey]);
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        CompanyApiKey::truncate();
    }
}
