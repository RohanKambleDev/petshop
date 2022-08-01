<?php

namespace App\Http\Requests\API\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'first_name'   => 'required|string',
            'last_name'    => 'required|string',
            'email'        => 'required|string|email|unique:users,email|max:50',
            'password'     => 'required|confirmed',
            'avatar'       => 'string',
            'address'      => 'required|string',
            'phone_number' => 'required|string'
        ];
    }

    /**
     * custom error messages
     *
     * @return array
     */
    public function messages()
    {
        // can have custom error messages field & rule wise
        // return [
        //     'email.required' => 'Please enter email, We need to know your email address!',
        //     'email.email'    => 'Please enter a valid Email'
        // ];
        return [];
    }
}
