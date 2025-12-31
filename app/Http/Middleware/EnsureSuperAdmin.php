<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('api')->user();

        if (! $user || ! method_exists($user, 'isSuperAdmin') || ! $user->isSuperAdmin()) {
            return response()->json([
                'message' => 'Akses ditolak: hanya super admin yang diizinkan melakukan operasi ini'
            ], 403);
        }

        return $next($request);
    }
}

