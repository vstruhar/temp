<?php

namespace Tests\Browser;

use App\Models\CompanyDetails;
use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\ClientPicker;
use Tests\Browser\Components\InvoiceItemsTable;
use Tests\Browser\Pages\CreateProformaInvoice;
use Tests\Browser\Pages\EditProformaInvoice;
use Tests\DuskTestCase;

class ProformaInvoiceTest extends DuskTestCase
{
    /** @test */
    public function it_should_be_able_to_create_new_proforma_invoice_with_correct_prices_when_populating_price_first()
    {
        $companyDetails = CompanyDetails::first();

        $companyDetails->update(['tax_profile' => 0]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::first())
                    ->visit(new CreateProformaInvoice)
                    ->within(new ClientPicker, function ($browser) {
                        $browser->selectFirstClient();
                    })
                    ->within(new InvoiceItemsTable, function ($browser) {
                        $browser->populateFirstRow('First Item', 2, 100, null, null);
                    })
                    ->pause(100)
                    ->assertAttribute('@first-row-subtotal', 'modelvalue', '200')
                    ->assertSeeIn('@summary', '200,00')
                    ->press('ULOŽIŤ')
                    ->waitForText('Zálohová faktúra bola úspešne vytvorená');

            // Check edit page data
            $browser->visit(new EditProformaInvoice(Document::latest()->first()))
                    ->waitForText('Edit zálohovej faktúry:')
                    ->assertInputValue('@first-row-name', 'First Item')
                    ->assertInputValue('@first-row-quantity', 2)
                    ->assertAttribute('@first-row-price', 'modelvalue', '100')
                    ->assertAttribute('@first-row-subtotal', 'modelvalue', '200')
                    ->assertSeeIn('@summary', '200,00');

            // Change items on edit page
            $browser
                ->within(new InvoiceItemsTable, function ($browser) {
                    $browser->populateFirstRow('First Item (edited)', 3, 50, null, null);
                })
                ->pause(100)
                ->assertInputValue('@first-row-name', 'First Item (edited)')
                ->assertInputValue('@first-row-quantity', 3)
                ->assertAttribute('@first-row-price', 'modelvalue', '50')
                ->assertAttribute('@first-row-subtotal', 'modelvalue', '150')
                ->assertSeeIn('@summary', '150,00')
                ->press('ULOŽIŤ')
                ->waitForText('Zálohová faktúra bola úspešne aktualizovaná');
        });

        $companyDetails->update(['tax_profile' => 1]);
    }

    /** @test */
    public function it_should_be_able_to_create_new_proforma_invoice_with_correct_prices_when_populating_subtotal_first()
    {
        $companyDetails = CompanyDetails::first();

        $companyDetails->update(['tax_profile' => 0]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::first())
                    ->visit(new CreateProformaInvoice)
                    ->within(new ClientPicker, function ($browser) {
                        $browser->selectFirstClient();
                    })
                    ->within(new InvoiceItemsTable, function ($browser) {
                        $browser->populateFirstRow('First Item', 2, null, null, 200);
                    })
                    ->pause(100)
                    ->assertAttribute('@first-row-price', 'modelvalue', '100')
                    ->assertSeeIn('@summary', '200,00')
                    ->press('ULOŽIŤ')
                    ->waitForText('Zálohová faktúra bola úspešne vytvorená');

            // Check edit page data
            $browser->visit(new EditProformaInvoice(Document::latest()->first()))
                    ->waitForText('Edit zálohovej faktúry:')
                    ->assertInputValue('@first-row-name', 'First Item')
                    ->assertInputValue('@first-row-quantity', 2)
                    ->assertAttribute('@first-row-price', 'modelvalue', '100')
                    ->assertAttribute('@first-row-subtotal', 'modelvalue', '200')
                    ->assertSeeIn('@summary', '200,00');

            // Change items on edit page
            $browser
                ->within(new InvoiceItemsTable, function ($browser) {
                    $browser->populateFirstRow('First Item (edited)', 3, null, null, 150);
                })
                ->pause(100)
                ->assertInputValue('@first-row-name', 'First Item (edited)')
                ->assertInputValue('@first-row-quantity', 3)
                ->assertAttribute('@first-row-price', 'modelvalue', '50')
                ->assertAttribute('@first-row-subtotal', 'modelvalue', '150')
                ->assertSeeIn('@summary', '150,00')
                ->press('ULOŽIŤ')
                ->waitForText('Zálohová faktúra bola úspešne aktualizovaná');
        });

        $companyDetails->update(['tax_profile' => 1]);
    }

    /** @test */
    public function it_should_be_able_to_apply_discount_amount_to_proforma_invoice_item_when_company_does_not_have_tax()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::first())
                    ->visit(new CreateProformaInvoice)
                    ->within(new ClientPicker, function ($browser) {
                        $browser->selectFirstClient();
                    })
                    ->within(new InvoiceItemsTable, function ($browser) {
                        $browser->populateFirstRow('First Item', 2, 100, null, null)
                                ->addDiscountToFirstRow(10, null);
                    })
                    ->pause(100)
                    ->assertAttribute('@first-row-subtotal', 'modelvalue', '190')
                    ->assertAttribute('@first-extra-row-percent-discount', 'modelvalue', '5')
                    ->assertAttribute('@first-extra-row-amount-discount', 'modelvalue', '10')
                    ->assertSeeIn('@first-extra-row-discount', 'Zľava: 10,00')
                    ->assertSeeIn('@summary', '190,00')
                    ->press('ULOŽIŤ')
                    ->waitForText('Zálohová faktúra bola úspešne vytvorená');

            // Check edit page data
            $browser->visit(new EditProformaInvoice(Document::latest()->first()))
                    ->waitForText('Edit zálohovej faktúry:')
                    ->assertInputValue('@first-row-name', 'First Item')
                    ->assertInputValue('@first-row-quantity', 2)
                    ->assertAttribute('@first-row-price', 'modelvalue', '100')
                    ->assertAttribute('@first-row-subtotal', 'modelvalue', '190')
                    ->assertAttribute('@first-extra-row-percent-discount', 'modelvalue', '5')
                    ->assertAttribute('@first-extra-row-amount-discount', 'modelvalue', '10')
                    ->assertSeeIn('@summary', '190,00');

            // Change items on edit page
            $browser
                ->within(new InvoiceItemsTable, function ($browser) {
                    $browser->populateFirstRow('First Item (edited)', 3, 50, null, null)
                            ->addDiscountToFirstRow(5, null, false);
                })
                ->pause(100)
                ->assertInputValue('@first-row-name', 'First Item (edited)')
                ->assertInputValue('@first-row-quantity', 3)
                ->assertAttribute('@first-row-price', 'modelvalue', '50')
                ->assertAttribute('@first-row-subtotal', 'modelvalue', '145')
                ->assertSeeIn('@summary', '145,00')
                ->press('ULOŽIŤ')
                ->waitForText('Zálohová faktúra bola úspešne aktualizovaná');
        });
    }

    /** @test */
    public function it_should_be_able_to_apply_discount_percent_to_proforma_invoice_item_when_company_does_not_have_tax()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::first())
                    ->visit(new CreateProformaInvoice)
                    ->within(new ClientPicker, function ($browser) {
                        $browser->selectFirstClient();
                    })
                    ->within(new InvoiceItemsTable, function ($browser) {
                        $browser->populateFirstRow('First Item', 2, 100, null, null)
                                ->addDiscountToFirstRow(null, 10);
                    })
                    ->pause(100)
                    ->assertAttribute('@first-row-subtotal', 'modelvalue', '180')
                    ->assertAttribute('@first-extra-row-percent-discount', 'modelvalue', '10')
                    ->assertAttribute('@first-extra-row-amount-discount', 'modelvalue', '20')
                    ->assertSeeIn('@first-extra-row-discount', 'Zľava: 20,00')
                    ->assertSeeIn('@summary', '180,00')
                    ->press('ULOŽIŤ')
                    ->waitForText('Zálohová faktúra bola úspešne vytvorená');

            // Check edit page data
            $browser->visit(new EditProformaInvoice(Document::latest()->first()))
                    ->waitForText('Edit zálohovej faktúry:')
                    ->assertInputValue('@first-row-name', 'First Item')
                    ->assertInputValue('@first-row-quantity', 2)
                    ->assertAttribute('@first-row-price', 'modelvalue', '100')
                    ->assertAttribute('@first-row-subtotal', 'modelvalue', '180')
                    ->assertAttribute('@first-extra-row-percent-discount', 'modelvalue', '10')
                    ->assertAttribute('@first-extra-row-amount-discount', 'modelvalue', '20')
                    ->assertSeeIn('@first-extra-row-discount', 'Zľava: 20,00')
                    ->assertSeeIn('@summary', '180,00');

            // Change items on edit page
            $browser
                ->within(new InvoiceItemsTable, function ($browser) {
                    $browser->populateFirstRow('First Item (edited)', 3, 50, null, null)
                            ->addDiscountToFirstRow(null, 5, false);
                })
                ->pause(100)
                ->assertInputValue('@first-row-name', 'First Item (edited)')
                ->assertInputValue('@first-row-quantity', 3)
                ->assertAttribute('@first-row-price', 'modelvalue', '50')
                ->assertAttribute('@first-row-subtotal', 'modelvalue', '142.5')
                ->assertAttribute('@first-extra-row-percent-discount', 'modelvalue', '5')
                ->assertAttribute('@first-extra-row-amount-discount', 'modelvalue', '7.5')
                ->assertSeeIn('@first-extra-row-discount', 'Zľava: 7,50')
                ->assertSeeIn('@summary', '142,50')
                ->press('ULOŽIŤ')
                ->waitForText('Zálohová faktúra bola úspešne aktualizovaná');
        });
    }
}
