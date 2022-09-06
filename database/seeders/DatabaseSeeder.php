<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\Client;
use App\Models\CompanyDetails;
use App\Models\DocumentNumber;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->withPersonalTeam()->create([
            'name'  => 'John Doe',
            'email' => 'user@mail.com',
        ]);

        $team = Team::first();

        CompanyDetails::create([
            'company_id'      => $team->id,
            'name'            => 'Test',
            'type'            => 'ltd',
            'country'         => 'SK',
            'address'         => 'Test Address',
            'postal_code'     => '123',
            'city'            => 'Bratislava',
            'phone'           => '+421 123123',
            'register'        => 'Obchodný register Okresného súdu Bratislava I',
            'organization_id' => '36517674',
            'tax'             => '2022201555',
            'value_added_tax' => 'SK2022201555',
            'tax_profile'     => 1,
            'invoice_color'   => '#336699',
        ]);

        $invoiceNumberData = [
            'company_id' => $team->id,
            'period'     => DocumentNumber::PERIOD_YEARLY,
            'is_default' => true,
        ];

        DocumentNumber::create(
            array_merge($invoiceNumberData, [
                'name'   => 'Default',
                'type'   => DocumentNumber::TYPE_INVOICE,
                'format' => 'RRRRCCCC',
            ])
        );

        DocumentNumber::create(
            array_merge($invoiceNumberData, [
                'name'   => 'Default',
                'type'   => DocumentNumber::TYPE_PROFORMA_INVOICE,
                'format' => 'ZRRRRCCCC',
            ])
        );

        DocumentNumber::create(
            array_merge($invoiceNumberData, [
                'name'   => 'Default',
                'type'   => DocumentNumber::TYPE_CREDIT_NOTE,
                'format' => 'DRRRRCCCC',
            ])
        );

        DocumentNumber::create(
            array_merge($invoiceNumberData, [
                'name'   => 'Default',
                'type'   => DocumentNumber::TYPE_QUOTATION,
                'format' => '#CRRRRCCCC',
            ])
        );

        BankAccount::create([
            'company_id'        => $team->id,
            'name'              => 'Default',
            'currency'          => 'EUR',
            'number_account'    => '123123',
            'bank_code'         => 'DEF1',
            'iban'              => '123123123123123',
            'swift'             => '123123',
            'default'           => true,
            'show_on_documents' => true,
        ]);

        Client::create([
            'company_id'      => $team->id,
            'name'            => 'Prvy klient',
            'address'         => 'Test Address',
            'organization_id' => '123123',
            'postal_code'     => '123',
            'email'           => 'client@mail.com',
            'tax'             => '123123',
            'phone'           => '+421 123123',
            'city'            => 'Bratislava',
            'value_added_tax' => '123123',
            'country'         => 'SK',
            'number_account'  => '123123',
            'bank_code'       => 'ADS',
            'iban'            => '123123123123',
            'swift'           => '123',
        ]);
    }
}
