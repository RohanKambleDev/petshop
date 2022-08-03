<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\Auth\LcobucciJWT;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\User\UserUpdateRequest;

class UserController extends Controller
{
    protected $lcobucciJwt;
    protected $success = 0;
    protected $data = [];
    protected $error = null;
    protected $errors = [];
    protected $extra = [];
    protected $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
    protected $apiToken = '';
    protected $uuid = '';
    protected $userObj = '';

    public function __construct(Request $request, User $user)
    {
        $this->lcobucciJwt = new LcobucciJWT;
        $this->apiToken    = $request->bearerToken();
        $this->uuid        = $this->lcobucciJwt->getUserUuid($this->apiToken);
        $this->userObj     = $user->getUserByUuid($this->uuid);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
