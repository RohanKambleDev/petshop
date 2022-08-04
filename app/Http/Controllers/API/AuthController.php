<?php

namespace App\Http\Controllers\API;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\JwtToken;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\API\Auth\LoginRequest;
use App\Http\Requests\API\Auth\ResetPassword;
use App\Http\Requests\API\Auth\ForgotPassword;
use App\Http\Requests\API\Auth\RegisterRequest;
use App\Facades\LcobucciJwtFacade as Jwt;

class AuthController extends Controller
{
    protected $lcobucciJwt;
    protected $success = 0;
    protected $data = [];
    protected $error = null;
    protected $errors = [];
    protected $extra = [];
    protected $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * register
     *
     * @param  RegisterRequest $request
     * @param  User $user
     * @param  JwtToken $jwtToken
     * 
     * @return json
     */
    public function register(RegisterRequest $request, User $user, JwtToken $jwtToken)
    {
        // get validated request data
        $requestData = $request->validated();
        unset($requestData['avatar']);

        // insert into user DB
        $newUser = $user->add($requestData);
        if (!$newUser instanceof User) {
            throw new Exception('User not registered');
        }
        $this->data = $newUser->first()->toArray();

        // generate new token
        $token = Jwt::getUserApiToken($this->data['uuid']);
        // insert jwt claims to db
        $insertedToken = $jwtToken->add($token, $newUser->first());
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

            // update last logged in at
            $user->updateField(Auth::user()->uuid, 'last_login_at', Carbon::now());

            // if found try to use the same
            $existingJwt = $jwtToken->getRecordByUser(Auth::user()->uuid);

            // generate new token
            $token = Jwt::getUserApiToken(Auth::user()->uuid);
            // insert jwt claims to db
            $insertedToken = $jwtToken->add($token, Auth::user());
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
     * @param  mixed $request
     * @param  mixed $jwtToken
     * @return array
     */
    public function logout(Request $request, JwtToken $jwtToken)
    {
        $token = $request->bearerToken();
        // delete jwt token from db
        if (!empty($token) && $jwtToken->removeJwtToken($token)) {
            // prepare for response
            $this->data['message'] = "Logout successfully";
            $this->success = 1;
            $this->statusCode = Response::HTTP_OK;
        } else {
            $this->error = "Failed to authenticate user";
        }

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
        $token = Jwt::getUserApiToken($user->uuid);

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

        $userRecord = $user->getUserByEmail($credentials['email']);
        if ($userRecord instanceof User && Jwt::validateApiToken($apiToken)) {
            if ($user->resetPassword($credentials)) {
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
