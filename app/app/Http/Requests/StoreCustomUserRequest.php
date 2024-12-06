<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'field email can not be blank',
            'password.required' => 'field password can not be blank',
            'first_name.required'=> 'field first_name can not be blank',
            'last_name.required'=> 'field last_name can not be blank',
        ];
    }
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'unique:custom_users,email'],
            'password' => ['required', 'string', 'min:3'],
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
        ];
    }
}
