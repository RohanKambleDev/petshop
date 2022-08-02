<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\Auth\LcobucciJWT;
use Illuminate\Support\Facades\Auth;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {

            $response = [
                'success' => 0,
                'data'    => [],
                'error'   => "Failed to authenticate user",
                'errors'  => [],
                'extra'   => []
            ];

            $lcobucciJwt = new LcobucciJWT;
            $apiToken    = $request->bearerToken();
            $uuid        = $request->validate(['user' => 'required']);

            if (empty($uuid) || !$lcobucciJwt->validateApiToken($apiToken, $uuid)) {
                return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (Exception $e) {
            return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $next($request);
    }
}
