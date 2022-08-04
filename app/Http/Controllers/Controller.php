<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * schemes={"http,https"},
 * @OA\Info(
 *      version="1.0.0",
 *      title="Demo PetShop API - Swagger Documentation",
 *      description="This API is a demo API for a petshop store built in Laravel 9",
 *      @OA\Contact(
 *          email="rohu2187@gmail.com"
 *      ),
 *      @OA\License(
 *          name="Github Repo",
 *          url="https://github.com/rohu2187/petshop"
 *      )
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Demo API Server"
 * )
 * 
 *  @OAS\securityScheme(
 *     securitySchemes="bearer_token",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 *  )
 * 
 * 
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
