<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
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
            'name'            => 'required',
            'organization_id' => 'nullable',
            'address'         => 'required',
            'postal_code'     => 'required',
            'city'            => 'required',
            'country'         => 'required',
            'value_added_tax' => ['nullable'],
        ];
    }

    public function attributes()
    {
        return [
            'name'            => __('Name'),
            'organization_id' => __('Business ID'),
            'address'         => __('Address'),
            'postal_code'     => __('Zip'),
            'city'            => __('City'),
            'country'         => __('Country'),
            'value_added_tax' => __('VAT'),
        ];
    }
}
