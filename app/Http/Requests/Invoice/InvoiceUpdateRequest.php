<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'number'       => 'required',
            'date_created' => 'required',
            'client_id'    => 'required',
            'items.*.name' => 'required',
            'items.*.price'=> 'required',
        ];
    }

    public function attributes()
    {
        return [
            'number'       => __('Invoice number'),
            'date_created' => __('Date of issue'),
            'client_id'    => __('Customer'),
            'items.*.name' => __('Item name'),
            'items.*.price'=> __('Price'),
        ];
    }
}
