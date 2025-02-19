<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
{
    if (!$request->user()->phone) {
        return response()->json([
            'status' => false,
            'message' => 'Vui lòng cập nhật số điện thoại.',
        ], 403);
    }
    if (!$request->user()->address) {
        return response()->json([
            'status' => false,
            'message' => 'Vui lòng cập nhật địa chỉ.',
        ], 403);
    }

    return $next($request);
}

}
