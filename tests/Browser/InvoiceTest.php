<?php

namespace Tests\Browser;

use App\Models\CompanyDetails;
use App\Models\Document;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\ClientPicker;
use Tests\Browser\Components\InvoiceItemsTable;
use Tests\Browser\Pages\CreateInvoice;
use Tests\Browser\Pages\EditInvoice;
use Tests\DuskTestCase;

class InvoiceTest extends DuskTestCase
{
    /** @test */
    public function it_should_be_able_to_create_new_invoice_with_correct_tax_prices_when_populating_price_first()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::first())
                    ->visit(new CreateInvoice)
                    ->within(new ClientPicker, function ($browser) {
                        $browser->selectFirstClient();
                    })
                    ->within(new InvoiceItemsTable, function ($browser) {
                        $browser->populateFirstRow('First Item', 2, 100, 20, null);
                    })
                    ->pause(100)
                    ->assertAttribute('@first-row-subtotal-with-tax', 'modelvalue', '240')
                    ->assertSeeIn('@summary', '200,00')
                    ->assertSeeIn('@summary', '40,00')
                    ->assertSeeIn('@summary', '240,00')
                    ->press('ULOŽIŤ')
                    ->waitForText('Faktúra bola úspešne vytvorená');

            // Check edit page data
            $browser->visit(new EditInvoice(Document::latest()->first()))
                    ->waitForText('Edit faktúry:')
                    ->assertInputValue('@first-row-name', 'First Item')
                    ->assertInputValue('@first-row-quantity', 2)
                    ->assertAttribute('@first-row-price', 'modelvalue', '100')
                    ->assertAttribute('@first-row-tax', 'modelvalue', '20')
                    ->assertAttribute('@first-row-subtotal-with-tax', 'modelvalue', '240')
                    ->assertSeeIn('@summary', '200,00')
                    ->assertSeeIn('@summary', '40,00')
                    ->assertSeeIn('@summary', '240,00');

            // Change items on edit page
            $browser
                ->within(new InvoiceItemsTable, function ($browser) {
                    $browser->populateFirstRow('First Item (edited)', 3, 50, 10, null);
                })
                ->pause(100)
                ->assertInputValue('@first-row-name', 'First Item (edited)')
                ->assertInputValue('@first-row-quantity', 3)
                ->assertAttribute('@first-row-price', 'modelvalue', '50')
                ->assertAttribute('@first-row-tax', 'modelvalue', '10')
                ->assertAttribute('@first-row-subtotal-with-tax', 'modelvalue', '165')
                ->assertSeeIn('@summary', '150,00')
                ->assertSeeIn('@summary', '15,00')
                ->assertSeeIn('@summary', '165,00')
                ->press('ULOŽIŤ')
                ->waitForText('Faktúra bola úspešne aktualizovaná');
        });
    }

    /** @test */
    public function it_should_be_able_to_create_new_invoice_with_correct_tax_prices_when_populating_subtotal_first()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::first())
                    ->visit(new CreateInvoice)
                    ->within(new ClientPicker, function ($browser) {
                        $browser->selectFirstClient();
                    })
                    ->within(new InvoiceItemsTable, function ($browser) {
                        $browser->populateFirstRow('First Item', 2, null, 20, 240);
                    })
                    ->pause(100)
                    ->assertAttribute('@first-row-subtotal-with-tax', 'modelvalue', '240')
                    ->assertSeeIn('@summary', '200,00')
                    ->assertSeeIn('@summary', '40,00')
                    ->assertSeeIn('@summary', '240,00')
                    ->press('ULOŽIŤ')
                    ->waitForText('Faktúra bola úspešne vytvorená');

            // Check edit page data
            $browser->visit(new EditInvoice(Document::latest()->first()))
                    ->waitForText('Edit faktúry:')
                    ->assertInputValue('@first-row-name', 'First Item')
                    ->assertInputValue('@first-row-quantity', 2)
                    ->assertAttribute('@first-row-price', 'modelvalue', '100')
                    ->assertAttribute('@first-row-tax', 'modelvalue', '20')
                    ->assertAttribute('@first-row-subtotal-with-tax', 'modelvalue', '240')
                    ->assertSeeIn('@summary', '200,00')
                    ->assertSeeIn('@summary', '40,00')
                    ->assertSeeIn('@summary', '240,00');

            // Change items on edit page
            $browser
                ->within(new InvoiceItemsTable, function ($browser) {
                    $browser->populateFirstRow('First Item (edited)', 3, null, 10, 165);
                })
                ->pause(100)
                ->assertInputValue('@first-row-name', 'First Item (edited)')
                ->assertInputValue('@first-row-quantity', 3)
                ->assertAttribute('@first-row-price', 'modelvalue', '50')
                ->assertAttribute('@first-row-tax', 'modelvalue', '10')
                ->assertAttribute('@first-row-subtotal-with-tax', 'modelvalue', '165')
                ->assertSeeIn('@summary', '150,00')
                ->assertSeeIn('@summary', '15,00')
                ->assertSeeIn('@summary', '165,00')
                ->press('ULOŽIŤ')
                ->waitForText('Faktúra bola úspešne aktualizovaná');
        });
    }

    /** @test */
    public function it_should_be_able_to_create_new_invoice_with_correct_prices_when_populating_price_first()
    {
        $companyDetails = CompanyDetails::first();

        $companyDetails->update(['tax_profile' => 0]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::first())
                    ->visit(new CreateInvoice)
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
                    ->waitForText('Faktúra bola úspešne vytvorená');

            // Check edit page data
            $browser->visit(new EditInvoice(Document::latest()->first()))
                    ->waitForText('Edit faktúry:')
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
                ->waitForText('Faktúra bola úspešne aktualizovaná');
        });

        $companyDetails->update(['tax_profile' => 1]);
    }

    /** @test */
    public function it_should_be_able_to_create_new_invoice_with_correct_prices_when_populating_subtotal_first()
    {
        $companyDetails = CompanyDetails::first();

        $companyDetails->update(['tax_profile' => 0]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::first())
                    ->visit(new CreateInvoice)
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
                    ->waitForText('Faktúra bola úspešne vytvorená');

            // Check edit page data
            $browser->visit(new EditInvoice(Document::latest()->first()))
                    ->waitForText('Edit faktúry:')
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
                ->waitForText('Faktúra bola úspešne aktualizovaná');
        });

        $companyDetails->update(['tax_profile' => 1]);
    }

    /** @test */
    public function it_should_be_able_to_apply_discount_amount_to_invoice_item_when_company_has_tax()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::first())
                    ->visit(new CreateInvoice)
                    ->within(new ClientPicker, function ($browser) {
                        $browser->selectFirstClient();
                    })
                    ->within(new InvoiceItemsTable, function ($browser) {
                        $browser->populateFirstRow('First Item', 2, 100, 20, null)
                                ->addDiscountToFirstRow(10, null);
                    })
                    ->pause(100)
                    ->assertAttribute('@first-row-subtotal-with-tax', 'modelvalue', '228')
                    ->assertAttribute('@first-extra-row-percent-discount', 'modelvalue', '5')
                    ->assertAttribute('@first-extra-row-amount-discount', 'modelvalue', '10')
                    ->assertSeeIn('@first-extra-row-discount', 'Zľava: 10,00')
                    ->assertSeeIn('@summary', '190,00')
                    ->assertSeeIn('@summary', '38,00')
                    ->assertSeeIn('@summary', '228,00')
                    ->press('ULOŽIŤ')
                    ->waitForText('Faktúra bola úspešne vytvorená');

            // Check edit page data
            $browser->visit(new EditInvoice(Document::latest()->first()))
                    ->waitForText('Edit faktúry:')
                    ->assertInputValue('@first-row-name', 'First Item')
                    ->assertInputValue('@first-row-quantity', 2)
                    ->assertAttribute('@first-row-price', 'modelvalue', '100')
                    ->assertAttribute('@first-row-tax', 'modelvalue', '20')
                    ->assertAttribute('@first-row-subtotal-with-tax', 'modelvalue', '228')
                    ->assertAttribute('@first-extra-row-percent-discount', 'modelvalue', '5')
                    ->assertAttribute('@first-extra-row-amount-discount', 'modelvalue', '10')
                    ->assertSeeIn('@summary', '190,00')
                    ->assertSeeIn('@summary', '38,00')
                    ->assertSeeIn('@summary', '228,00');

            // Change items on edit page
            $browser
                ->within(new InvoiceItemsTable, function ($browser) {
                    $browser->populateFirstRow('First Item (edited)', 3, 50, 10, null)
                            ->addDiscountToFirstRow(5, null, false);
                })
                ->pause(100)
                ->assertInputValue('@first-row-name', 'First Item (edited)')
                ->assertInputValue('@first-row-quantity', 3)
                ->assertAttribute('@first-row-price', 'modelvalue', '50')
                ->assertAttribute('@first-row-tax', 'modelvalue', '10')
                ->assertAttribute('@first-row-subtotal-with-tax', 'modelvalue', '159.5')
                ->assertSeeIn('@summary', '145,00')
                ->assertSeeIn('@summary', '14,50')
                ->assertSeeIn('@summary', '159,50')
                ->press('ULOŽIŤ')
                ->waitForText('Faktúra bola úspešne aktualizovaná');
        });
    }

    /** @test */
    public function it_should_be_able_to_apply_discount_percent_to_invoice_item_when_company_has_tax()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::first())
                    ->visit(new CreateInvoice)
                    ->within(new ClientPicker, function ($browser) {
                        $browser->selectFirstClient();
                    })
                    ->within(new InvoiceItemsTable, function ($browser) {
                        $browser->populateFirstRow('First Item', 2, 100, 20, null)
                                ->addDiscountToFirstRow(null, 10);
                    })
                    ->pause(100)
                    ->assertAttribute('@first-row-subtotal-with-tax', 'modelvalue', '216')
                    ->assertAttribute('@first-extra-row-percent-discount', 'modelvalue', '10')
                    ->assertAttribute('@first-extra-row-amount-discount', 'modelvalue', '20')
                    ->assertSeeIn('@first-extra-row-discount', 'Zľava: 20,00')
                    ->assertSeeIn('@summary', '180,00')
                    ->assertSeeIn('@summary', '36,00')
                    ->assertSeeIn('@summary', '216,00')
                    ->press('ULOŽIŤ')
                    ->waitForText('Faktúra bola úspešne vytvorená');

            // Check edit page data
            $browser->visit(new EditInvoice(Document::latest()->first()))
                    ->waitForText('Edit faktúry:')
                    ->assertInputValue('@first-row-name', 'First Item')
                    ->assertInputValue('@first-row-quantity', 2)
                    ->assertAttribute('@first-row-price', 'modelvalue', '100')
                    ->assertAttribute('@first-row-tax', 'modelvalue', '20')
                    ->assertAttribute('@first-row-subtotal-with-tax', 'modelvalue', '216')
                    ->assertAttribute('@first-extra-row-percent-discount', 'modelvalue', '10')
                    ->assertAttribute('@first-extra-row-amount-discount', 'modelvalue', '20')
                    ->assertSeeIn('@summary', '180,00')
                    ->assertSeeIn('@summary', '36,00')
                    ->assertSeeIn('@summary', '216,00');

            // Change items on edit page
            $browser
                ->within(new InvoiceItemsTable, function ($browser) {
                    $browser->populateFirstRow('First Item (edited)', 3, 50, 10, null)
                            ->addDiscountToFirstRow(null, 5, false);
                })
                ->pause(100)
                ->assertInputValue('@first-row-name', 'First Item (edited)')
                ->assertInputValue('@first-row-quantity', 3)
                ->assertAttribute('@first-row-price', 'modelvalue', '50')
                ->assertAttribute('@first-row-tax', 'modelvalue', '10')
                ->assertAttribute('@first-row-subtotal-with-tax', 'modelvalue', '156.75')
                ->assertAttribute('@first-extra-row-percent-discount', 'modelvalue', '5')
                ->assertAttribute('@first-extra-row-amount-discount', 'modelvalue', '7.5')
                ->assertSeeIn('@summary', '142,50')
                ->assertSeeIn('@summary', '14,25')
                ->assertSeeIn('@summary', '156,75')
                ->press('ULOŽIŤ')
                ->waitForText('Faktúra bola úspešne aktualizovaná');
        });
    }

    /** @test */
    public function it_should_be_able_to_apply_discount_amount_to_invoice_item_when_company_does_not_have_tax()
    {
        $companyDetails = CompanyDetails::first();

        $companyDetails->update(['tax_profile' => 0]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::first())
                    ->visit(new CreateInvoice)
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
                    ->waitForText('Faktúra bola úspešne vytvorená');

            // Check edit page data
            $browser->visit(new EditInvoice(Document::latest()->first()))
                    ->waitForText('Edit faktúry:')
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
                ->waitForText('Faktúra bola úspešne aktualizovaná');
        });

        $companyDetails->update(['tax_profile' => 1]);
    }

    /** @test */
    public function it_should_be_able_to_apply_discount_percent_to_invoice_item_when_company_does_not_have_tax()
    {
        $companyDetails = CompanyDetails::first();

        $companyDetails->update(['tax_profile' => 0]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::first())
                    ->visit(new CreateInvoice)
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
                    ->waitForText('Faktúra bola úspešne vytvorená');

            // Check edit page data
            $browser->visit(new EditInvoice(Document::latest()->first()))
                    ->waitForText('Edit faktúry:')
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
                ->waitForText('Faktúra bola úspešne aktualizovaná');
        });

        $companyDetails->update(['tax_profile' => 1]);
    }

    /** @test */
    public function it_should_calculate_invoice_summary_correctly_even_when_tax_is_set_to_0()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::first())
                    ->visit(new CreateInvoice)
                    ->within(new ClientPicker, function ($browser) {
                        $browser->selectFirstClient();
                    })
                    ->within(new InvoiceItemsTable, function ($browser) {
                        $browser->populateFirstRow('First Item', 18, null, 0, 1490);
                    })
                    ->pause(300)
                    ->assertAttribute('@first-row-subtotal-with-tax', 'modelvalue', '1490')
                    ->assertSeeIn('@summary', '1.490,00')
                    ->assertSeeIn('@summary', '0,00')
                    ->assertSeeIn('@summary', '1.490,00')
                    ->press('ULOŽIŤ')
                    ->waitForText('Faktúra bola úspešne vytvorená');

            // Check edit page data
            $browser->visit(new EditInvoice(Document::latest()->first()))
                    ->waitForText('Edit faktúry:')
                    ->assertInputValue('@first-row-name', 'First Item')
                    ->assertInputValue('@first-row-quantity', 18)
                    ->assertAttribute('@first-row-price', 'modelvalue', '82.78')
                    ->assertAttribute('@first-row-tax', 'modelvalue', '0')
                    ->assertAttribute('@first-row-subtotal-with-tax', 'modelvalue', '1490')
                    ->assertSeeIn('@summary', '1.490,00')
                    ->assertSeeIn('@summary', '0,00')
                    ->assertSeeIn('@summary', '1.490,00');

            // Change items on edit page
            $browser
                ->within(new InvoiceItemsTable, function ($browser) {
                    $browser->clearWithBackspaces('@first-row-subtotal-with-tax')
                            ->populateFirstRow('First Item (edited)', 18, null, 0, 1500);
                })
                ->assertInputValue('@first-row-name', 'First Item (edited)')
                ->assertInputValue('@first-row-quantity', 18)
                ->assertAttribute('@first-row-price', 'modelvalue', '83.33333333333333')
                ->assertAttribute('@first-row-tax', 'modelvalue', '0')
                ->assertAttribute('@first-row-subtotal-with-tax', 'modelvalue', '1500')
                ->assertSeeIn('@summary', '1.500,00')
                ->assertSeeIn('@summary', '0,00')
                ->assertSeeIn('@summary', '1.500,00')
                ->press('ULOŽIŤ')
                ->waitForText('Faktúra bola úspešne aktualizovaná');
        });
    }

    // TODO: add test where there is item with 0 tax
}
