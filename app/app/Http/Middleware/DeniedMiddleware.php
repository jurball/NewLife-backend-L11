<?php

namespace App\Http\Middleware;

use App\Models\FileAccess;
use App\Models\User;
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

        $file = Files::where('file_id', $request->fileId)->firstOrFail();

        if (!($file->user_id === $user_id->id())) {
            return response()->json([
                'message' => 'Forbidden for you'
            ], $FORBIDDEN);
        }

        return $next($request);
    }
}
