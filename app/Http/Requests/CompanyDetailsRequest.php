<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyDetailsRequest extends FormRequest
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
            'name'            => 'required|min:3',
            'type'            => 'required',
            'country'         => 'required',
            'postal_code'     => 'nullable',
            'organization_id' => 'required',
            'tax'             => 'nullable',
            'tax_profile'     => 'required|min:0|max:2',
            'value_added_tax' => [
                Rule::requiredIf(request()->tax_profile != 0),
                'nullable',
            ],
        ];
    }

    public function attributes()
    {
        return [
            'name'            => __('Name'),
            'type'            => __('Type'),
            'country'         => __('Country'),
            'address'         => __('Address'),
            'postal_code'     => __('Zip'),
            'city'            => __('City'),
            'phone'           => __('Phone'),
            'fax'             => __('Fax'),
            'web'             => __('Web'),
            'register'        => __('Registered in'),
            'organization_id' => __('Business ID'),
            'tax'             => __('Tax ID'),
            'tax_profile'     => __('Payer type'),
            'value_added_tax' => __('VAT'),
        ];
    }
}
