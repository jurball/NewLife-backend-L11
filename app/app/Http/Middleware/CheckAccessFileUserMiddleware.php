<?php

namespace App\Http\Middleware;

use App\Models\FileAccess;
use App\Models\Files;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAccessFileUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $FORBIDDEN = Response::HTTP_FORBIDDEN;
        $user_id = Auth::guard('sanctum');

        // Поиск файла (отдаст 404 если не найден файл)
        $file = Files::where('file_id', $request->fileId)->firstOrFail();
        $file_access = FileAccess::where('file_id', $file->id)->first();

        // Если файл принадлежит пользователю, то продолжаем запрос
        if (!($file->user_id === $user_id->id())) {
            if ($file_access->user_id === $user_id->id()) {
                return $next($request);
            }
            return response()->json([
                'success' => false,
                'message' => 'Forbidden for you'
            ], $FORBIDDEN);
        }

        return $next($request);
    }
}
