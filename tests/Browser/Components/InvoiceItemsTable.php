<?php

namespace Tests\Browser\Components;

use InvalidArgumentException;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class InvoiceItemsTable extends BaseComponent
{
    /**
     * @param \Laravel\Dusk\Browser $browser
     * @param                       $title
     * @param                       $quantity
     * @param                       $price
     * @param                       $tax
     * @param                       $subtotal
     *
     * @return void
     */
    public function populateFirstRow(Browser $browser, $title, $quantity, $price, $tax, $subtotal): void
    {
        $browser->clearWithBackspaces('@first-row-name')
                ->type('@first-row-name', $title)
                ->type('@first-row-quantity', $quantity);

        if ($tax !== null) {
            $browser->type('@first-row-tax', $tax);
        }
        if ($price) {
            $browser->type('@first-row-price', $price);
        }
        if ($subtotal && $tax === null) {
            $browser->type('@first-row-subtotal', $subtotal);
        } elseif ($subtotal && $tax !== null) {
            $browser->type('@first-row-subtotal-with-tax', $subtotal);
        }
    }

    /**
     * @param \Laravel\Dusk\Browser $browser
     * @param                       $amount
     * @param                       $percent
     * @param bool                  $clickOnDiscountButton
     *
     * @return void
     */
    public function addDiscountToFirstRow(Browser $browser, $amount, $percent, bool $clickOnDiscountButton = true): void
    {
        if ($clickOnDiscountButton) {
            try {
                $browser->press('@first-row-discount-button-tax')
                        ->pause(50);
            } catch (InvalidArgumentException $e) {
                $browser->press('@first-row-discount-button')
                        ->pause(50);
            }
        }

        if ($amount) {
            $browser->type('@first-extra-row-amount-discount', $amount);
        }
        if ($percent) {
            $browser->type('@first-extra-row-percent-discount', $percent);
        }
    }

    public function clearWithBackspaces(Browser $browser, $selector)
    {
        $value = $browser->value($selector);

        $browser->pause(300)
                ->keys($selector, ...array_fill(0, strlen($value), '{backspace}'))
                ->pause(300);
    }

    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return 'table#invoice-items';
    }

    /**
     * Assert that the browser page contains the component.
     *
     * @param Browser $browser
     *
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertVisible($this->selector());
    }

    /**
     * Get the element shortcuts for the component.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@first-row'                        => 'tbody > tr:first-child',
            '@first-row-name'                   => 'tbody > tr:first-child td:nth-child(1) input',
            '@first-row-quantity'               => 'tbody > tr:first-child td:nth-child(2) input',
            '@first-row-price'                  => 'tbody > tr:first-child td:nth-child(4) input',
            '@first-row-tax'                    => 'tbody > tr:first-child td:nth-child(5) input',
            '@first-row-subtotal'               => 'tbody > tr:first-child td:nth-child(5) input',
            '@first-row-subtotal-with-tax'      => 'tbody > tr:first-child td:nth-child(6) input',
            '@first-row-discount-button-tax'    => 'tbody > tr:first-child td:nth-child(7) button:first-child',
            '@first-row-discount-button'        => 'tbody > tr:first-child td:nth-child(6) button:first-child',
            '@first-extra-row-discount'         => 'tbody > tr:nth-child(2) td:nth-child(2)',
            '@first-extra-row-percent-discount' => 'tbody > tr:nth-child(2) td:nth-child(2) > div > div > div:nth-child(1) input',
            '@first-extra-row-amount-discount'  => 'tbody > tr:nth-child(2) td:nth-child(2) > div > div > div:nth-child(2) input',
            '@summary'                          => '#invoice-summary',
        ];
    }
}
