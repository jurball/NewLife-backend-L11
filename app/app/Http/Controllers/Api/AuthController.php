<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Неавторизованный пользователь
 *
 * Endpoints для неавторизованных пользователей (доступна регистрация и авторизация)
 */
class AuthController extends Controller
{
    /**
     * @unauthenticated
     * @response 200 {
     * "success": true,
     * "message": "Success",
     * "token": "you_token"
     * }
     *
     */
    public function registration(RegistrationRequest $request): JsonResponse
    {
        $user = User::create($request->all());

        $token = $user->createToken("Token user: $user->id");
        $status_code = Response::HTTP_OK;

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'token' => $token->plainTextToken,
        ], $status_code);
    }

    /**
     * @unauthenticated
     * @response 200 {
     * "email": "admin@admin.ru",
     * "password": "Qa1"
     * }
     */
    public function authorization(Request $request): JsonResponse
    {
        $rules = [
            'email' => 'required|string|email|max:255',
            'password' => ['required', 'string'],
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

        $user = Auth::user();
        $token = $user->createToken("User token: $user->id");
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'token'=> $token->plainTextToken,
        ]);
    }

    /**
     * @group Авторизованный пользователь
     * @response 200 {
     *  "success": true,
     *  "message": "Logout"
     * }
     * @response 403 {
     *     "message": "Login failed"
     * }
     */
    public function logout(): JsonResponse
    {
        $auth = Auth::guard('sanctum');
        $auth->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout'
        ]);
    }
}
