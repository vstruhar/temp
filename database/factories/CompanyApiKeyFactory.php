<?php

namespace Database\Factories;

use App\Models\CompanyApiKey;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CompanyApiKeyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        do {
            $newKey = Str::random(46);
        } while (CompanyApiKey::where('key', $newKey)->exists());

        return [
            'company_id' => Team::factory()->create(),
            'key'        => $newKey,
        ];
    }
}
