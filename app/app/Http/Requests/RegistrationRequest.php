<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

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
            'password' => ['required', 'string', 'min:3',   'regex:/[a-z]/', // Должна быть хотя бы одна строчная буква
                                                            'regex:/[A-Z]/',  // Должна быть хотя бы одна прописная буква
                                                            'regex:/[0-9]/',],
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
            'password.regex' => 'Пароль должен содержать хотя бы одну строчную букву, одну прописную букву и одну цифру.',
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
