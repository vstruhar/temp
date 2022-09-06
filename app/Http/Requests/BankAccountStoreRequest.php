<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BankAccountStoreRequest extends FormRequest
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
            'accounts.*.name'           => 'required',
            'accounts.*.currency'       => 'required',
            'accounts.*.number_account' => 'required',
            'accounts.*.bank_code'      => 'required',
            'accounts.*.iban'           => 'required',
            'accounts.*.swift'          => 'required',
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'accounts.*.name'           => __('Name'),
            'accounts.*.currency'       => __('Currency'),
            'accounts.*.number_account' => __('Account number'),
            'accounts.*.bank_code'      => __('Bank code'),
            'accounts.*.iban'           => __('IBAN'),
            'accounts.*.swift'          => __('SWIFT'),
        ];
    }
}
