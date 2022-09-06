<?php

namespace Tests\Feature\Http\Controllers\Api\Documents;

use App\Models\CompanyApiKey;
use App\Models\CompanyDetails;
use App\Models\DocumentNumber;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class InvoiceControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_return_bad_request_when_api_key_is_not_used()
    {
        $this->postJson(route('api.documents.invoice.create'), ['api_key' => null])
             ->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    /** @test */
    public function it_should_return_bad_request_when_invalid_api_key_is_used()
    {
        $company = Team::factory()->create();

        CompanyApiKey::factory()->create(['company_id' => $company->id]);

        $this->postJson(route('api.documents.invoice.create'), ['api_key' => 'abcd-1234-invalid-api-key'])
             ->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    /** @test */
    public function it_should_create_a_new_invoice_for_company()
    {
        $company = Team::factory()->create();

        $companyDetails = CompanyDetails::factory()->create(['company_id' => $company->id]);
        $companyApiKey  = CompanyApiKey::factory()->create(['company_id' => $company]);
        $invoiceNumber  = DocumentNumber::factory()->create(['company_id' => $company]);

        $data = [
            'api_key' => $companyApiKey->key,
            'client'  => [
                'name'        => "JKL - STEEL",
                'address'     => "Továrenská 2",
                'postal_code' => "953 01",
                'city'        => "Zlaté Moravce",
                'country'     => "SK",

                'organization_id' => "1111",   // IČO
                'tax'             => "2222",   // DIČ
                'value_added_tax' => "SK3333", // IČ DPH

                'email' => 'client@mail.com',
                'phone' => '123123',
                'fax'   => '321321',

                'number_account' => '1111111111',
                'bank_code'      => '2222222222',
                'iban'           => '3333333333',
                'swift'          => '4444444444',

                'shipping_address' => [
                    'name'        => 'Client Name',
                    'postal_code' => '789',
                    'city'        => 'City',
                    'address'     => 'Addr',
                    'phone'       => '112233',
                    'country'     => 'Slovakia',
                ],

                'contacts' => [
                    ['name' => 'Client contact name', 'email' => 'contact@mail.com', 'phone' => '123456'],
                ],
            ],
            'invoice' => [
                'invoice_number_id' => $invoiceNumber->id,
                'name'              => null, // generate if not set "Faktúra 220004"
                'date_created'      => '2022-04-27',
                'date_added'        => '2022-04-27',
                'billing_date'      => '2022-05-11',
                'payment_method'    => 'bank_transfer', // bank_transfer, cash, card, cash_on_delivery, mutual_credit, custom
                'invoice_currency'  => 'EUR', // EUR, CZK, USD, GBP, HUF, PLN, CHF, RUB, CNY, SEK
                'order_number'      => '0001',
                'variable_symbol'   => null, // generate if not set '220004'
                'constant_symbol'   => '123',
                'specific_symbol'   => '321',
                'lang_code'         => 'sk', // sk, en, de, it, hr, rs
                'generate_qr_code'  => true,
                'with_logo'         => true,
                'note'              => 'Test note',
                'note_before_items' => 'Test note 2',
                'items'             => [
                    [
                        'name'            => 'Test item 1',
                        'description'     => 'My item description',
                        'unit'            => 'ks',
                        'tax'             => 20,
                        'quantity'        => 2,
                        'price'           => 100,
                        'discount_amount' => 10,
                        //'discount_percent' => 0,
                    ],
                ],
                'issued_by'         => 'Test',
                'issued_by_email'   => 'test@mail.com',
                'issued_by_phone'   => '456789',
            ],
        ];

        $response = $this->postJson(route('api.documents.invoice.create'), $data);

        $response->assertCreated()
                 ->assertJson([
                     'company' => [
                         'name'                   => $companyDetails->name,
                         'type'                   => $companyDetails->type,
                         'country'                => $companyDetails->country,
                         'address'                => $companyDetails->address,
                         'postal_code'            => $companyDetails->postal_code,
                         'city'                   => $companyDetails->city,
                         'register'               => $companyDetails->register,
                         'organization_id'        => $companyDetails->organization_id, // IČO
                         'tax'                    => $companyDetails->tax,             // DIČ
                         'value_added_tax'        => $companyDetails->value_added_tax, // IČ DPH
                         'tax_profile'            => $companyDetails->tax_profile,
                         'default_tax_percentage' => $companyDetails->default_tax_percentage,
                     ],
                     'client'  => [
                         'name'        => $data['client']['name'],
                         'address'     => $data['client']['address'],
                         'postal_code' => $data['client']['postal_code'],
                         'city'        => $data['client']['city'],
                         'country'     => $data['client']['country'],

                         'organization_id' => $data['client']['organization_id'], // IČO
                         'tax'             => $data['client']['tax'],             // DIČ
                         'value_added_tax' => $data['client']['value_added_tax'], // IČ DPH
                     ],
                     'invoice' => [
                         'number'            => now()->year . '0001',
                         'name'              => 'Faktúra: ' . now()->year . '0001',
                         'date_created'      => $data['invoice']['date_created'],
                         'date_added'        => $data['invoice']['date_added'],
                         'billing_date'      => $data['invoice']['billing_date'],
                         'payment_method'    => $data['invoice']['payment_method'],
                         'invoice_currency'  => $data['invoice']['invoice_currency'],
                         'order_number'      => $data['invoice']['order_number'],
                         'variable_symbol'   => now()->year . '0001',
                         'constant_symbol'   => $data['invoice']['constant_symbol'],
                         'specific_symbol'   => $data['invoice']['specific_symbol'],
                         'lang_code'         => $data['invoice']['lang_code'],
                         'generate_qr_code'  => $data['invoice']['generate_qr_code'],
                         'with_logo'         => $data['invoice']['with_logo'],
                         'note'              => $data['invoice']['note'],
                         'note_before_items' => $data['invoice']['note_before_items'],
                         'items'             => [
                             [
                                 'name'             => $data['invoice']['items'][0]['name'],
                                 'description'      => $data['invoice']['items'][0]['description'],
                                 'unit'             => $data['invoice']['items'][0]['unit'],
                                 "tax"              => $data['invoice']['items'][0]['tax'],
                                 'quantity'         => $data['invoice']['items'][0]['quantity'],
                                 'price'            => $data['invoice']['items'][0]['price'],
                                 'discount_amount'  => $data['invoice']['items'][0]['discount_amount'],
                                 'discount_percent' => 5,
                             ],
                         ],
                         'issued_by'         => $data['invoice']['issued_by'],
                         'issued_by_email'   => $data['invoice']['issued_by_email'],
                         'issued_by_phone'   => $data['invoice']['issued_by_phone'],
                     ],
                 ]);

        $this->assertDatabaseCount('documents', 1);
        $this->assertDatabaseCount('document_items', 1);
        $this->assertDatabaseCount('clients', 1);

        $this->assertTrue(Str::contains($response->json('invoice.pdf_url'), 'http://ponfys-faktury.test/documents/pdf/'));
    }
}
