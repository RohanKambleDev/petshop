<?php

namespace App\Http\Requests\API\User;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            'first_name'   => 'string',
            'last_name'    => 'string',
            'email'        => 'string|email|max:50',
            'password'     => 'confirmed',
            // The field under validation must be present in the input data but can be empty.
            'avatar'       => 'present',
            'address'      => 'string',
            'phone_number' => 'string'
        ];
    }
}
