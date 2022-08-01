<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\API\Auth\LoginRequest;
use App\Http\Requests\API\Auth\RegisterRequest;

class AuthController extends Controller
{
    public function __construct()
    {
    }

    public function register(RegisterRequest $request, User $user)
    {
        // get validated request data
        $requestData = $request->validated();

        // insert into user DB
        $newUser = $user->add($requestData);
        $newUserResponse = $newUser->first()->toArray();

        // create token
        $token = $user->getToken();
        $newUserResponse['token'] = $token;
        $newUserResponse['uuid']  = $newUser->pluck('uuid')->first();

        // in the response send back user data and token
        $response = [
            'success' => 1,
            'data'    => $newUserResponse,
            'error'   => null,
            'errors'  => [],
            'extra'   => []
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function login(LoginRequest $request, User $user)
    {
        // get validated request data
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            // in the response send back user token
            $response = [
                'success' => 1,
                'data'    => ['token' => $user->getToken()],
                'error'   => null,
                'errors'  => [],
                'extra'   => []
            ];
        } else {
            $response = [
                'success' => 0,
                'data'    => [],
                'error'   => "Failed to authenticate user",
                'errors'  => [],
                'extra'   => []
            ];
        }

        return response()->json($response, Response::HTTP_OK);
    }
}
