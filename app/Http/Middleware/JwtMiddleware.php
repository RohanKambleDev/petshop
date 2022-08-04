<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Facades\LcobucciJwtFacade as Jwt;

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
        $response = [
            'success' => 0,
            'data'    => [],
            'error'   => "Failed to authenticate user",
            'errors'  => [],
            'extra'   => []
        ];

        try {
            $apiToken = $request->bearerToken();

            if (empty($apiToken) || !Jwt::validateApiToken($apiToken)) {
                return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (Exception $e) {
            return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $next($request);
    }
}
