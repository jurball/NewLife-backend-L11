<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Files;

class DeniedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ): Response
    {
        $FORBIDDEN = Response::HTTP_FORBIDDEN;
        $user_id = Auth::guard('sanctum');

        // Поиск файла (отдаст 404 если не найден файл)
        $file = Files::where('file_id', $request->fileId)->firstOrFail();

        // Если файл принадлежит пользователю, то продолжаем запрос
        if ($file->user_id === $user_id->id()) {
            return $next($request);
        }

        // Иначе сообщаем что файл недоступен
        return response()->json([
            'success' => false,
            'message' => 'Forbidden for you'
        ], $FORBIDDEN);
    }
}
