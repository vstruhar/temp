<?php

namespace Database\Factories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'company_id'       => Team::factory()->create(),
            'name'             => $this->faker->words(3, true),
            'address'          => $this->faker->address,
            'organization_id'  => '',
            'postal_code'      => '',
            'email'            => $this->faker->email,
            'tax'              => '',
            'phone'            => $this->faker->phoneNumber,
            'city'             => $this->faker->city,
            'value_added_tax'  => '',
            'fax'              => '',
            'country'          => 'SK',
            'number_account'   => '',
            'bank_code'        => '',
            'iban'             => '',
            'swift'            => '',
            'shipping_address' => '',
            'contacts'         => '{}',
        ];
    }
}
