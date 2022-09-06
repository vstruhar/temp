<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Str;
use Rikudou\Iban\Iban\IBAN;
use rikudou\SkQrPayment\QrPayment;

class QrCodeService
{
    /**
     * @param  \App\Models\Document $invoice
     *
     * @return mixed
     */
    public static function generate(Document $invoice)
    {
        $amount  = $invoice->amount_with_tax ?? $invoice->amount;
        $iban    = optional($invoice->company->bankAccountDefault)->iban ?? $invoice->company->bankAccounts()->first()->iban;
        $country = optional($invoice->company->companyDetails)->country;

        $iban = Str::of($iban)->snake()->replace('_', '');

        $payment = new QrPayment();

        $payment
            //->setCurrency($invoice->invoice_currency)
            //->setDueDate($invoice->billing_date)
            //->setConstantSymbol($invoice->constant_symbol ?? '')
            //->setPayeeName('John Doe')
            //->setSpecificSymbol($invoice->specific_symbol ?? '')
            ->setAmount($amount)
            ->setCountry($country ?? '')
            ->setVariableSymbol($invoice->variable_symbol ?? '')
            ->setIbans([new IBAN($iban)]);

        return $payment->getQrCode();
    }
}
