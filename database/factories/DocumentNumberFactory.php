<?php

namespace Database\Factories;

use App\Models\DocumentNumber;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentNumberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'company_id'      => Team::factory()->create(),
            'name'            => 'Default',
            'type'            => DocumentNumber::TYPE_INVOICE,
            'period'          => DocumentNumber::PERIOD_YEARLY,
            'format'          => 'RRRRCCCC',
            'is_default'      => true,
            'current_counter' => 0,
            'created_at'      => now(),
        ];
    }
}
