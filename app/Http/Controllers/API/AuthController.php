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
    protected $success = 0;
    protected $data = [];
    protected $error = null;
    protected $errors = [];
    protected $extra = [];
    protected $statusCode = Response::HTTP_UNAUTHORIZED;

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
    /**
     * @OA\Post(
     *      path="/user/create",
     *      operationId="register",
     *      tags={"User"},
     *      summary="Create a User Account",
     *      description="Create a User Account",
     *      @OA\RequestBody(
     *          required=true,
     *          description="Pass user information",
     *          @OA\JsonContent(
     *              required={"email"},
     *              @OA\Property(property="first_name", type="string", example="Rohan"),
     *              @OA\Property(property="last_name", type="string", example="Kamble"),
     *              @OA\Property(property="email", type="string", example="rohu2187@gmail.com"),
     *              @OA\Property(property="password", type="string", example="test4321"),
     *              @OA\Property(property="password_confirmation", type="string", example="test4321"),
     *              @OA\Property(property="avatar", type="string", example=""),
     *              @OA\Property(property="address", type="string", example="Mumabi"),
     *              @OA\Property(property="phone_number", type="string", example="9999999999"),
     *              @OA\Property(property="is_marketing", type="string", example="1"),
     *          ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error"
     *      )
     * )
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
        $newUser->uuid = $newUser->uuid->toString();
        $this->data = $newUser->toArray();

        // generate new token
        $token = Jwt::getUserApiToken($newUser->uuid);
        $this->data['token'] = $token;

        // insert jwt claims to db
        $insertedToken = $jwtToken->add($token, $newUser);
        if (!$insertedToken->first() instanceof JwtToken) {
            throw new Exception('Token not generated');
        }

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
    /**
     * @OA\Post(
     *      path="/user/login",
     *      operationId="login",
     *      tags={"User"},
     *      summary="Login as user account",
     *      description="Login as user account",
     *      @OA\RequestBody(
     *          required=true,
     *          description="Pass user information",
     *          @OA\JsonContent(
     *              required={"email"},
     *              @OA\Property(property="email", type="string", example="rohu2187@gmail.com"),
     *              @OA\Property(property="password", type="string", example="test4321"),
     *          ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error"
     *      )
     * )
     */
    public function login(LoginRequest $request, User $user, JwtToken $jwtToken)
    {
        // get validated request data
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {

            // update last logged in at
            $user->updateField(Auth::user()->uuid, 'last_login_at', Carbon::now());

            // check
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
    /**
     * @OA\Get(
     *      path="/user/logout",
     *      operationId="logout",
     *      tags={"User"},
     *      summary="Logout an user account",
     *      description="Logout an user account",
     *      security={{"bearer_token":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error"
     *      ),
     * )
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
    /**
     * @OA\Post(
     *      path="/user/forgot-password",
     *      operationId="forgotPassword",
     *      tags={"User"},
     *      summary="Create a token to reset user password",
     *      description="Create a token to reset user password",
     *      @OA\RequestBody(
     *          required=true,
     *          description="Pass user information",
     *          @OA\JsonContent(
     *              required={"email"},
     *              @OA\Property(property="email", type="string", example="rohu2187@gmail.com"),
     *          ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error"
     *      )
     * )
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
    /**
     * @OA\Post(
     *      path="/user/reset-password-token",
     *      operationId="resetPasswordToken",
     *      tags={"User"},
     *      summary="Reset a User Password with a token",
     *      description="Reset a User Password with a token",
     *      @OA\RequestBody(
     *          required=true,
     *          description="Pass user information",
     *          @OA\JsonContent(
     *              required={"token"},
     *              @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOZjA1ZTJhLWY1Y2YtNGQ1Ny05YWNhLTA0M"),
     *              @OA\Property(property="email", type="string", example="rohu2187@gmail.com"),
     *              @OA\Property(property="password", type="string", example="test4321"),
     *              @OA\Property(property="password_confirmation", type="string", example="test4321"),
     *          ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error"
     *      )
     * )
     */
    public function resetPasswordToken(ResetPassword $request, User $user)
    {
        // get validated request data
        $credentials = $request->validated();
        $apiToken    = $credentials['token'];

        $userRecord = $user->getUserByEmailAndToken($credentials['email'], $apiToken);
        if ($userRecord instanceof User && Jwt::validateApiToken($apiToken)) {
            if ($user->resetPassword($credentials)) {
                $this->data['message'] = "Password has been successfully updated";
                $this->success = 1;
                $this->statusCode = Response::HTTP_OK;
                return $this->buildResponse();
            }
        }

        $this->error = "Invalid or expired token";
        $this->statusCode = Response::HTTP_FORBIDDEN;
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
