<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Auth\LoginRequest;
use App\Http\Requests\API\Auth\RegisterRequest;
use Exception;

use function PHPUnit\Framework\throwException;

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

        // in the response send back user uuid and token
        $response = [
            'success' => 1,
            'data'    => $newUserResponse,
            'error'   => null,
            'errors'  => [],
            'extra'   => []
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function login(LoginRequest $request)
    {
        $response = $request->all();
        return response()->json($response, 200);
    }
}
