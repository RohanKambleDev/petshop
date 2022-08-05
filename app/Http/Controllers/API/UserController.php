<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Facades\LcobucciJwtFacade as Jwt;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\User\UserUpdateRequest;

class UserController extends Controller
{
    protected $success = 0;
    protected $data = [];
    protected $error = null;
    protected $errors = [];
    protected $extra = [];
    protected $statusCode = Response::HTTP_UNAUTHORIZED;
    protected $apiToken = '';
    protected $uuid = '';
    protected $userObj = '';

    public function __construct(Request $request, User $user)
    {
        $this->apiToken    = $request->bearerToken();
        $this->uuid        = Jwt::getUserUuid($this->apiToken);
        $this->userObj     = $user->getUserByUuid($this->uuid);
    }

    /**
     * Display the specified resource
     * by getting uuid from the token.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *      path="/user",
     *      operationId="view-user-account",
     *      tags={"User"},
     *      summary="View a user account",
     *      description="View a user account",
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
    public function show()
    {
        $this->success = 1;
        $this->data = $this->userObj->toArray();
        $this->statusCode = Response::HTTP_OK;
        return $this->buildResponse();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Put(
     *      path="/user/edit",
     *      operationId="update-user",
     *      tags={"User"},
     *      summary="Update a User Account",
     *      description="Update a User Account",
     *      security={{"bearer_token":{}}},
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
     *              @OA\Property(property="avatar", type="string", example="Avatar image UUID"),
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
    public function update(UserUpdateRequest $request, User $user)
    {
        // get validated request data
        $requestData = $request->validated();
        unset($requestData['avatar']);

        if ($this->userObj->updateFieldsInBulk($this->uuid, $requestData)) {
            $this->success = 1;
            $this->data = $user->getUserByUuid($this->uuid)->toArray(); // get fresh data from DB
            $this->statusCode = Response::HTTP_OK;
        }

        $this->error = "Failed to update user detail";
        return $this->buildResponse();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        // check if this user has access to delete user
        // $this->uuid

        // $uuid to delete the record

        if ($this->userObj->deleteRecord($uuid)) {
            $this->success = 1;
            $this->data['message'] = "Record successfully deleted";
            $this->statusCode = Response::HTTP_OK;
        }

        $this->error = "Failed to delete user";
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
