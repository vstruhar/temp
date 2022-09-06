<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentNumberRequest extends FormRequest
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
            'name'            => 'required|max:255',
            'period'          => 'required',
            'format'          => 'required',
            'type'            => 'required',
            'current_counter' => 'required|integer',
            'is_default'      => 'required',
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'name'            => __('Title'),
            'period'          => __('Period'),
            'format'          => __('Format'),
            'type'            => __('Document type'),
            'current_counter' => __('Starting number'),
            'is_default'      => __('Default'),
        ];
    }
}
