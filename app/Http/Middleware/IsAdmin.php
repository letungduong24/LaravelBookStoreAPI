<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
{
    if ($request->user()->role !=0) {
        return response()->json([
            'status' => false,
            'message' => 'Bạn không có quyền truy cập.',
        ], 403);
    }

    return $next($request);
}

}
