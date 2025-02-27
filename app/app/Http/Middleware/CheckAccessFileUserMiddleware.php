<?php

namespace App\Http\Middleware;

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
        $user_id = Auth::guard('sanctum');

        $data_file = User::find($user_id->id())->access_files;
        foreach($data_file as $file){
            if ($file->user_id === $user_id->id()) {
                return $next($request);
            }
        }

        $file = Files::where('file_id', $request->fileId)->firstOrFail();

        if ($file->user_id === $user_id->id()) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Forbidden for you'
        ], Response::HTTP_FORBIDDEN);
    }
}
