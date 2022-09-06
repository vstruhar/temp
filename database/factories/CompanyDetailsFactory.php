<?php

namespace Database\Factories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyDetailsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'company_id'             => Team::factory()->create(),
            'name'                   => $this->faker->name,
            'type'                   => $this->faker->randomElement(['ltd', 'trade']),
            'country'                => 'SK',
            'address'                => $this->faker->address,
            'postal_code'            => $this->faker->postcode,
            'city'                   => $this->faker->city,
            'phone'                  => $this->faker->phoneNumber,
            'fax'                    => $this->faker->phoneNumber,
            'web'                    => $this->faker->domainName,
            'register'               => 'Obchodný register Okresného súdu Bratislava I',
            'organization_id'        => (string) $this->faker->numberBetween(10000, 99999),
            'tax'                    => (string) $this->faker->numberBetween(10000, 99999),
            'value_added_tax'        => (string) $this->faker->numberBetween(10000, 99999),
            'tax_profile'            => 1, // is taxable when greater than 0
            'default_tax_percentage' => 20,
            'logo'                   => null,
            'signature'              => null,
            'invoice_color'          => '#336699',
        ];
    }
}
