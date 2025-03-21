<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Password;

class RegistrationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'unique:users'],
            'password' => ['required', 'string', Password::min(3)->letters()->mixedCase()->numbers()],
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'field email can not be blank',
            'password.required' => 'field password can not be blank',
            'first_name.required'=> 'field first_name can not be blank',
            'last_name.required'=> 'field last_name can not be blank',
            'email.unique' => 'email already exists',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $errors,
        ], 422));
    }
}
