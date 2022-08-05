<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Facades\LcobucciJwtFacade as Jwt;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class ProductController extends Controller
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
        // can be added to middleware and 
        // bind the current user object to container
        $this->apiToken    = $request->bearerToken();
        $this->uuid        = Jwt::getUserUuid($this->apiToken);
        $this->userObj     = $user->getUserByUuid($this->uuid);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
