<?php

namespace Tests\Browser\Pages;

use App\Models\Document;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class EditProformaInvoice extends Page
{
    private Document $invoice;

    /**
     * @param \App\Models\Document $invoice
     */
    public function __construct(Document $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return route('documents.edit', ['proforma-invoices', $this->invoice->id], false);
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param Browser $browser
     *
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url());
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@first-row-name'                   => '#invoice-items tbody > tr:first-child td:nth-child(1) input',
            '@first-row-quantity'               => '#invoice-items tbody > tr:first-child td:nth-child(2) input',
            '@first-row-price'                  => '#invoice-items tbody > tr:first-child td:nth-child(4) input',
            '@first-row-tax'                    => '#invoice-items tbody > tr:first-child td:nth-child(5) input',
            '@first-row-subtotal'               => '#invoice-items tbody > tr:first-child td:nth-child(5) input',
            '@first-row-subtotal-with-tax'      => '#invoice-items tbody > tr:first-child td:nth-child(6) input',
            '@first-row-discount-button'        => 'tbody > tr:first-child td:nth-child(7) button:first-child',
            '@first-extra-row-discount'         => 'tbody > tr:nth-child(2) td:nth-child(2)',
            '@first-extra-row-percent-discount' => 'tbody > tr:nth-child(2) td:nth-child(2) > div > div > div:nth-child(1) input',
            '@first-extra-row-amount-discount'  => 'tbody > tr:nth-child(2) td:nth-child(2) > div > div > div:nth-child(2) input',
            '@summary'                          => '#invoice-summary',
        ];
    }
}
