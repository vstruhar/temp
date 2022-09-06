<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class InvoicePaymentRequest extends FormRequest
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
            'payment_method' => 'required',
            'amount'         => 'required|numeric',
            'payment_date'   => 'required|date',
        ];
    }

    public function attributes()
    {
        return [
            'payment_method' => __('Payment method'),
            'amount'         => __('Sum'),
            'payment_date'   => __('Payment date'),
        ];
    }
}
