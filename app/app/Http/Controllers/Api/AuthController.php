<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomUserRequest;
use App\Http\Requests\LoginCustomUserRequest;
use App\Models\CustomUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\DocBlock\Tags\Author;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $rules = [
            'email' => ['required', 'string', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:3',   'regex:/[a-z]/', // Должна быть хотя бы одна строчная буква
                                                            'regex:/[A-Z]/',  // Должна быть хотя бы одна прописная буква
                                                            'regex:/[0-9]/',],
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
        ];

        $messages = [
            'email.required' => 'field email can not be blank',
            'password.required' => 'field password can not be blank',
            'first_name.required'=> 'field first_name can not be blank',
            'last_name.required'=> 'field last_name can not be blank',
            'password.regex' => 'Пароль должен содержать хотя бы одну строчную букву, одну прописную букву и одну цифру.',
            'email.unique' => 'email already exists',
        ];

        // Валидация
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            // Получаем ошибки валидации
            $errors = $validator->errors()->messages();

            return response()->json([
                'success' => false,
                'message' => $errors,
            ], 422);
        }

        $user = User::create($request->all());

        $token = $user->createToken($request->first_name);
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'token' => $token->plainTextToken,
        ]);
    }

    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:3',
        ];

        $messages = [
            'email.required' => 'field email is required',
            'password.required' => 'field password is required',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errors = $validator->errors()->messages();
            return response()->json([
                'success' => false,
                'message' => $errors,
            ], 422);
        }

        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed'
            ], 401);
        }

//        $user = CustomUser::query()->where('email', $request->email);
        $user = Auth::user();
        $token = $user->createToken($user->first_name);
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'token'=> $token->plainTextToken,
        ]);
    }

    public function logout()
    {
        $auth = Auth::guard('sanctum');

        $auth->user()->tokens()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Logout'
        ]);
    }
}
