<?php

namespace App\Http\Requests\API\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'category_uuid' => 'required|string',
            'title'         => 'required|string',
            'price'         => 'required|numeric',
            'description'   => 'required|string',
            'metadata'      => 'required|json'
        ];
    }
}
