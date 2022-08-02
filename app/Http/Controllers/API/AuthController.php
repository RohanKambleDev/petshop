<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use App\Models\JwtToken;
use Illuminate\Http\Response;
use App\Services\Auth\LcobucciJWT;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\API\Auth\LoginRequest;
use App\Http\Requests\API\Auth\ResetPassword;
use App\Http\Requests\API\Auth\ForgotPassword;
use App\Http\Requests\API\Auth\RegisterRequest;

class AuthController extends Controller
{
    protected $lcobucciJwt;
    protected $success = 0;
    protected $data = [];
    protected $error = null;
    protected $errors = [];
    protected $extra = [];
    protected $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

    public function __construct()
    {
        $this->lcobucciJwt = new LcobucciJWT;
        // $this->middleware('jwt.verify', ['except' => ['login', 'register']]);
    }

    /**
     * register
     *
     * @param  mixed $request
     * @param  mixed $user
     * @param  mixed $jwtToken
     * @return void
     */
    public function register(RegisterRequest $request, User $user, JwtToken $jwtToken)
    {
        // get validated request data
        $requestData = $request->validated();

        // insert into user DB
        $newUser = $user->add($requestData);
        if (!$newUser instanceof User) {
            throw new Exception('User not registered');
        }
        $this->data = $newUser->first()->toArray();

        // generate new token
        $token = $this->lcobucciJwt->getUserApiToken($this->data['uuid']);
        $insertedToken = $jwtToken->add($token);
        if (!$insertedToken->first() instanceof JwtToken) {
            throw new Exception('Token not generated');
        }
        $this->data['token'] = $token;
        $this->data['uuid']  = $newUser->pluck('uuid')->first();

        // prepare for response
        $this->success = 1;
        $this->statusCode = Response::HTTP_OK;
        return  $this->buildResponse();
    }

    /**
     * login
     *
     * @param  mixed $request
     * @param  mixed $user
     * @param  mixed $jwtToken
     * @return array
     */
    public function login(LoginRequest $request, User $user, JwtToken $jwtToken)
    {
        // get validated request data
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {

            // if found try to use the same
            $existingJwt = $jwtToken->getRecordByUser(Auth::user()->uuid);

            // generate new token
            $token = $this->lcobucciJwt->getUserApiToken(Auth::user()->uuid);
            $insertedToken = $jwtToken->add($token);
            if (!$insertedToken->first() instanceof JwtToken) {
                throw new Exception('Token not generated');
            }

            // prepare for response
            $this->success = 1;
            $this->data['token'] = $token;
            $this->statusCode = Response::HTTP_OK;
        } else {
            $this->error = "Failed to authenticate user";
        }

        return $this->buildResponse();
    }

    /**
     * logout
     *
     * @return void
     */
    public function logout()
    {
        // prepare for response
        $this->success = 1;
        $this->statusCode = Response::HTTP_OK;
        return $this->buildResponse();
    }

    /**
     * forgotPassword
     *
     * @param  mixed $request
     * @param  mixed $user
     * @return array
     */
    public function forgotPassword(ForgotPassword $request, User $user)
    {
        // get validated request data
        $email = $request->validated();
        $user = $user->getUserByEmail($email);

        if ($user === null) {
            $this->error = "Failed to authenticate user";
            $this->statusCode = Response::HTTP_NOT_FOUND;
            return $this->buildResponse();
        }

        // user found so get the token
        $token = $this->lcobucciJwt->getUserApiToken($user->uuid);

        // prepare for response
        $this->success = 1;
        $this->data['reset_token'] = $token;
        $this->statusCode = Response::HTTP_OK;
        return $this->buildResponse();
    }

    /**
     * resetPasswordToken
     *
     * @param  mixed $request
     * @param  mixed $user
     * @return array
     */
    public function resetPasswordToken(ResetPassword $request, User $user)
    {
        // get validated request data
        $credentials = $request->validated();
        $apiToken    = $credentials['token'];

        if ($user->resetPassword($credentials)) {
            $userRecord = $user->getUserByEmail($credentials['email']);
            if ($userRecord instanceof User && $this->lcobucciJwt->validateApiToken($apiToken, $userRecord->uuid)) {
                $this->data['message'] = "Password has been successfully updated";
                $this->success = 1;
                $this->statusCode = Response::HTTP_OK;
                return $this->buildResponse();
            }
        }

        $this->error = "Invalid or expired token";
        $this->statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;
        return $this->buildResponse();
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function buildResponse()
    {
        $response = [
            'success' => $this->success,
            'data'    => $this->data,
            'error'   => $this->error,
            'errors'  => $this->errors,
            'extra'   => $this->extra,
        ];

        return response()->json($response, $this->statusCode);
    }
}
