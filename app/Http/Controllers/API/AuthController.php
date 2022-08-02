<?php

namespace App\Http\Controllers\API;

use DateTime;
use App\Models\User;
use Illuminate\Http\Response;
use App\Services\Auth\LcobucciJWT;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\API\Auth\LoginRequest;
use App\Http\Requests\API\Auth\ResetPassword;
use App\Http\Requests\API\Auth\ForgotPassword;
use App\Http\Requests\API\Auth\RegisterRequest;

class AuthController extends Controller
{
    protected $lcobucciJwt;
    public function __construct()
    {
        $this->lcobucciJwt = new LcobucciJWT;
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
        $apiToken    = $request->bearerToken();

        if (Auth::attempt($credentials) && $this->lcobucciJwt->validateApiToken($apiToken)) {
            // if (Auth::attempt($credentials)) {

            // generate new token
            $token = $this->lcobucciJwt->getUserApiToken(Auth::user()->uuid);

            // in the response send back user token
            $response = [
                'success' => 1,
                'data'    => ['token' => $token],
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

    public function logout()
    {
        Auth::logout();

        $response = [
            'success' => 1,
            'data'    => [],
            'error'   => null,
            'errors'  => [],
            'extra'   => []
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function forgotPassword(ForgotPassword $request)
    {
        // get validated request data
        $email = $request->validated();
        $user = User::where('email', '=', $email)->first();

        if ($user === null) {
            $response = [
                'success' => 0,
                'data'    => [],
                'error'   => 'Invalid email',
                'errors'  => [],
                'extra'   => []
            ];

            return response()->json($response, Response::HTTP_NOT_FOUND);
        }

        // user found
        $token = $this->lcobucciJwt->getUserApiToken($user->uuid);
        $response = [
            'success' => 1,
            'data'    => ['reset_token' => $token],
            'error'   => null,
            'errors'  => [],
            'extra'   => []
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function resetPasswordToken(ResetPassword $request, User $user)
    {
        // get validated request data
        $credentials = $request->validated();
        $apiToken    = $credentials['token'];

        if ($user->resetPassword($credentials) && $this->lcobucciJwt->validateApiToken($apiToken)) {
            $response = [
                'success' => 1,
                'data'    => ["message" => "Password has been successfully updated"],
                'error'   => null,
                'errors'  => [],
                'extra'   => []
            ];

            return response()->json($response, Response::HTTP_OK);
        }

        $response = [
            'success' => 0,
            'data'    => [],
            'error'   => "Invalid or expired token",
            'errors'  => [],
            'extra'   => []
        ];

        return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
