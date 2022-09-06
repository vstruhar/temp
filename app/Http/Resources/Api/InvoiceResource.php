<?php

namespace App\Http\Resources\Api;

use App\Models\DocumentItem;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'company' => [
                'name'                   => $this->company_details['name'],
                'type'                   => $this->company_details['type'],
                'country'                => $this->company_details['country'],
                'address'                => $this->company_details['address'],
                'postal_code'            => $this->company_details['postal_code'],
                'city'                   => $this->company_details['city'],
                'register'               => $this->company_details['register'],
                'organization_id'        => $this->company_details['organization_id'], // IČO
                'tax'                    => $this->company_details['tax'],             // DIČ
                'value_added_tax'        => $this->company_details['value_added_tax'], // IČ DPH
                'tax_profile'            => $this->company_details['tax_profile'],
                'default_tax_percentage' => (float)$this->company_details['default_tax_percentage'],
            ],
            'client'  => [
                'name'        => $this->client['name'],
                'address'     => $this->client['address'],
                'postal_code' => $this->client['postal_code'],
                'city'        => $this->client['city'],
                'country'     => $this->client['country'],

                'organization_id' => (string)$this->client['organization_id'], // IČO
                'tax'             => (string)$this->client['tax'],             // DIČ
                'value_added_tax' => (string)$this->client['value_added_tax'], // IČ DPH
            ],
            'invoice' => [
                'number'            => $this->number,
                'name'              => $this->name,
                'date_created'      => $this->date_created->format('Y-m-d'),
                'date_added'        => $this->date_added->format('Y-m-d'),
                'billing_date'      => $this->billing_date->format('Y-m-d'),
                'payment_method'    => $this->payment_method,
                'invoice_currency'  => $this->invoice_currency,
                'order_number'      => $this->order_number,
                'variable_symbol'   => $this->variable_symbol,
                'constant_symbol'   => $this->constant_symbol,
                'specific_symbol'   => $this->specific_symbol,
                'lang_code'         => $this->lang_code,
                'generate_qr_code'  => (boolean)$this->generate_qr_code,
                'with_logo'         => $this->with_logo,
                'note'              => $this->note,
                'note_before_items' => $this->note_before_items,
                'items'             => $this->items
                    ->map(function (DocumentItem $item) {
                        return [
                            'name'             => $item->name,
                            'description'      => $item->description,
                            'unit'             => $item->unit,
                            "tax"              => $item->tax,
                            'quantity'         => $item->quantity,
                            'price'            => $item->price,
                            'discount_amount'  => $item->discount_amount,
                            'discount_percent' => $item->discount_percent,
                        ];
                    })
                    ->toArray(),
                'issued_by'         => $this->issued_by,
                'issued_by_email'   => $this->issued_by_email,
                'issued_by_phone'   => $this->issued_by_phone,
                'pdf_url'           => Storage::disk('invoices')
                                              ->temporaryUrl(urlencode($this->getPdfLocation()), now()->addMinutes(15), []),
            ],
        ];
    }
}
