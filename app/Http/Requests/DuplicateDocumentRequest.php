<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DuplicateDocumentRequest extends FormRequest
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
            'company_id'  => 'required|integer',
            'copy_client' => 'required|boolean',
            'client_id'   => [Rule::requiredIf($this->copy_client === false), 'nullable', 'integer'],
            'document_id' => 'required|integer',
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'company_id'  => __('Company'),
            'client_id'   => __('Buyer'),
            'document_id' => __('Document'),
            'copy_client' => __('Copy buyer'),
        ];
    }
}
