<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleCheck
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$request->user() || !in_array($request->user()->role, $roles)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak. Anda tidak memiliki izin.',
            ], 403);
        }

        return $next($request);
    }
}