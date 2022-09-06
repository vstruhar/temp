<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Document;
use App\Models\DocumentNumber;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'company_id'          => Team::factory()->create(),
            'client_id'           => Client::factory()->create(),
            'type'                => Document::TYPE_INVOICE,
            'invoice_id'          => null,
            'proforma_invoice_id' => null,
            'credit_note_id'      => null,
            'quotation_id'        => null,
            'status'              => Document::STATUS_WAITING_FOR_PAYMENT,
            'name'                => 'Invoice name',
            'number'              => now()->year . '0001',
            'number_counter'      => 1,
            'invoice_number_id'   => DocumentNumber::factory()->create(),
            'amount'              => 10000,
            'amount_with_tax'     => 12000,
            'discount'            => null,
            'date_created'        => now()->toDateTime(),
            'date_added'          => now()->toDateTime(),
            'billing_date'        => now()->addWeek()->toDateTime(),
            'variable_symbol'     => now()->year . '0001',
            'payment_method'      => $this->faker->randomElement(['bank_transfer', 'cash_on_delivery']),
            'constant_symbol'     => null,
            'order_number'        => null,
            'invoice_currency'    => 'EUR',
            'note_before_items'   => null,
            'note'                => null,
            'issued_by'           => $this->faker->name,
            'issued_by_phone'     => $this->faker->phoneNumber,
            'issued_by_email'     => $this->faker->email,
            'client'              => null,
            'company_details'     => null,
            'bank_accounts'       => null,
            'taxable'             => 1,
            'generate_qr_code'    => 0,
            'lang_code'           => 'en',
            'with_logo'           => 0,
            'specific_symbol'     => '',
        ];
    }
}
