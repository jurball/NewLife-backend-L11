<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\files;

class DeniedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ): Response
    {
        $user_id = Auth::guard('sanctum');
        $file = files::where('ids', $request->fileId)->first();

        if($file == null) {
            return response()->json([
                'success' => false,
                'message' => 'Not found file!',
            ], 404);
        }

        if ($file->user_id === $user_id->id()) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Forbidden!'
        ], 422);
    }
}
