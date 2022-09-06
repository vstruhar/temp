<?php

namespace App\Services\Invoice;

use App\Models\BankAccount;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use LaravelDaily\Invoices\Invoice;
use Mexitek\PHPColors\Color;
use Rikudou\QrPaymentQrCodeProvider\QrCode;

class PonfysInvoice extends Invoice
{
    public $model;

    public $amount;
    public $amount_with_tax;
    public $variableSymbol;
    public $dateAdded;
    public $billingDate;
    public $noteBeforeItems;
    public $signatureFile;
    public $invoiceIssued;
    public $issuedByPhone;
    public $issuedByEmail;
    public $invoiceNumber;
    public $qrCode;
    public $bankAccounts;
    public $paymentMethod;
    public $taxable;
    public $color;

    /**
     * @param mixed $model
     *
     * @return PonfysInvoice
     */
    public function model($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @param mixed $amount
     *
     * @return PonfysInvoice
     */
    public function amount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @param mixed $amount_with_tax
     *
     * @return PonfysInvoice
     */
    public function amountWithTax($amount_with_tax)
    {
        $this->amount_with_tax = $amount_with_tax;

        return $this;
    }

    /**
     * @param $value
     *
     * @return \App\Services\Invoice\PonfysInvoice
     */
    public function variableSymbol($value): PonfysInvoice
    {
        $this->variableSymbol = $value ?? null;

        return $this;
    }

    /**
     * @param $value
     *
     * @return \App\Services\Invoice\PonfysInvoice
     */
    public function dateAdded($value): PonfysInvoice
    {
        $this->dateAdded = $value ? $value->format($this->date_format) : $value;

        return $this;
    }

    /**
     * @param $value
     *
     * @return \App\Services\Invoice\PonfysInvoice
     */
    public function noteBeforeItems($value): PonfysInvoice
    {
        $this->noteBeforeItems = $value ?? null;

        return $this;
    }

    /**
     * @param $value
     *
     * @return \App\Services\Invoice\PonfysInvoice
     */
    public function signatureFile($value): PonfysInvoice
    {
        if ($value != null) {
            $type = pathinfo($value, PATHINFO_EXTENSION);
            $data = file_get_contents($value);

            $this->signatureFile = 'data:image/' . $type . ';base64,' . base64_encode($data);
        } else {
            $this->signatureFile = null;
        }

        return $this;
    }

    /**
     * @param $value
     *
     * @return \App\Services\Invoice\PonfysInvoice
     */
    public function invoiceIssued($value): PonfysInvoice
    {
        $this->invoiceIssued = $value ?? null;

        return $this;
    }

    /**
     * @param $value
     *
     * @return \App\Services\Invoice\PonfysInvoice
     */
    public function issuedByPhone($value): PonfysInvoice
    {
        $this->issuedByPhone = $value ?? null;

        return $this;
    }

    /**
     * @param $value
     *
     * @return \App\Services\Invoice\PonfysInvoice
     */
    public function issuedByEmail($value): PonfysInvoice
    {
        $this->issuedByEmail = $value ?? null;

        return $this;
    }

    /**
     * @param $value
     *
     * @return \App\Services\Invoice\PonfysInvoice
     */
    public function billingDate($value): PonfysInvoice
    {
        $this->billingDate = $value ? $value->format($this->date_format) : null;

        return $this;
    }

    /**
     * @param string $invoiceNumber
     *
     * @return \App\Services\Invoice\PonfysInvoice
     */
    public function invoiceNumber($invoiceNumber): PonfysInvoice
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    /**
     * @param $paymentMethod
     *
     * @return \App\Services\Invoice\PonfysInvoice
     */
    public function paymentMethod($paymentMethod): PonfysInvoice
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * @param bool $taxable
     *
     * @return \App\Services\Invoice\PonfysInvoice
     */
    public function taxable(bool $taxable): PonfysInvoice
    {
        $this->taxable = $taxable;

        return $this;
    }

    /**
     * @param string $color
     *
     * @return \App\Services\Invoice\PonfysInvoice
     */
    public function invoiceColor(string $color): PonfysInvoice
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @param int $amount
     *
     * @return string
     * @throws \Exception
     */
    public function lightColor(int $amount): string
    {
        $color = new Color($this->color);

        return '#' . $color->lighten($amount);
    }

    /**
     * @param $qrCode
     *
     * @return \App\Services\Invoice\PonfysInvoice
     */
    public function qrCode(QrCode $qrCode): PonfysInvoice
    {
        //$this->qrCode = $qrCode->getDataUri();
        $this->qrCode = Image::make($qrCode->getRawString())
                             ->encode('jpg', 100)
                             ->encode('data-url');

        return $this;
    }

    /**
     * @param array $bankAccounts
     *
     * @return \App\Services\Invoice\PonfysInvoice
     */
    public function bankAccounts(array $bankAccounts): PonfysInvoice
    {
        $ids = collect($bankAccounts)
            ->filter(static function ($account) {
                return $account['show_on_documents'] == 1;
            })
            ->pluck('id');

        $this->bankAccounts = BankAccount::whereIn('id', $ids)
                                         ->orderBy('default', 'desc')
                                         ->orderBy('name')
                                         ->get();

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentMethod(): string
    {
        return Str::of($this->paymentMethod)->replace('_', ' ')->ucfirst();
    }

    /**
     * @return bool
     */
    public function hasItemsWithZeroTax(): bool
    {
        return $this->items->some(fn($item) => $item->tax_percent_value === 0);
    }

    /**
     * @return float
     */
    public function getSubtotalOfItemsWithZeroTax(): float
    {
        return $this->items->reduce(function ($sum, $item) {
            $price = ($item->tax_percent_value === 0)
                ? ($item->price_per_unit * $item->quantity - $item->discount)
                : 0;

            return $sum + $price;
        }, 0);
    }
}
