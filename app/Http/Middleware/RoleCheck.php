<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleCheck
{
   public function handle(Request $request, Closure $next, ...$roles)
{
    if (!$request->user()) {
        return redirect()->route('login');
    }

    $userRole = strtolower(trim($request->user()->role));
    $allowedRoles = array_map(fn($r) => strtolower(trim($r)), $roles);

    if (!in_array($userRole, $allowedRoles)) {
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak.',
            ], 403);
        }

        return redirect()->route('dashboard');
    }

    return $next($request);
}
}