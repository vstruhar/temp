<?php

namespace App\Http\Requests;

use App\Rules\HexColor;
use Illuminate\Foundation\Http\FormRequest;

class InvoiceSettingsStoreRequest extends FormRequest
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
            'invoice_color' => ['required', new HexColor],
            'logo'          => 'nullable|mimes:jpg,jpeg,png|max:4096',
            'signature'     => 'nullable|mimes:jpg,jpeg,png|max:4096',
        ];
    }

    public function attributes()
    {
        return [
            'invoice_color' => __('Color'),
            'logo'          => __('Logo'),
            'signature'     => __('Signature'),
        ];
    }
}
