<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Session;

class MyTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //  // Ambil token dan waktu expired dari session
        // $storedToken = $request->session()->get('custom_token');
        // $tokenExpireTime = $request->session()->get('custom_token_expire');

        // // Ambil token dari header atau input
        // $clientToken = $request->header('X-CSRF-TOKEN') ?? $request->_token;
        
        // // Validasi: Token tidak ada
        // if (!$clientToken || !$storedToken) {
        //     return response()->json(['message' => 'You did not send a token.','token_dikirim' => $clientToken,'token_sesi' => $storedToken], 200);
        // }

        // // Validasi: Token tidak sama
        // if (!hash_equals($storedToken, $clientToken)) {
        //     return response()->json(['message' => 'Invalid CSRF token.'], 200);
        // }

        // // Validasi: Token expired
        // if (now()->greaterThan($tokenExpireTime)) {
        //     return response()->json(['message' => 'CSRF token has expired.'], 200);
        // }
        $tokenFromClient = $request->header('X-CSRF-TOKEN') ?? $request->_token; // atau dari header

        $tokenFromSession = Session::token();

        if (!$tokenFromClient || $tokenFromClient !== $tokenFromSession) {
            return response()->json(['message' => 'Invalid or expired token'], 400);
        }
        // Token valid dan belum expired
        return $next($request);
    }
}
