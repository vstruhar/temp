<?php

namespace App\Services\Invoice;

use Intervention\Image\Facades\Image;
use LaravelDaily\Invoices\Classes\InvoiceItem;

class PonfysInvoiceItem extends InvoiceItem
{
    public $tax_percent_value = null;

    public $calculated_sub_total_price = null;

    public $image = null;

    /**
     * @param $value
     *
     * @return \App\Services\Invoice\PonfysInvoiceItem
     */

    public function taxPercentValue($value)
    {
        $this->tax_percent_value = $value;

        return $this;
    }

    /**
     * @param $calculated_sub_total_price
     *
     * @return \App\Services\Invoice\PonfysInvoiceItem
     */
    public function calculatedSubTotalPrice($calculated_sub_total_price)
    {
        $this->calculated_sub_total_price = $calculated_sub_total_price;

        return $this;
    }

    /**
     * @param string|null $path
     *
     * @return $this
     */
    public function image(?string $path): PonfysInvoiceItem
    {
        $this->image = $path ? Image::make($path)->encode('data-url') : null;

        return $this;
    }
}
