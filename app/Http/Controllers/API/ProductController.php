<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Product\StoreRequest;
use App\Http\Requests\API\Product\UpdateRequest;

class ProductController extends Controller
{
    protected $success = 0;
    protected $data    = [];
    protected $error   = null;
    protected $errors  = [];
    protected $extra   = [];
    protected $statusCode = Response::HTTP_UNAUTHORIZED;
    protected $product = '';

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *      path="/products",
     *      operationId="products.index",
     *      tags={"Product"},
     *      summary="View all products",
     *      description="View all products",
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
    public function index(Category $category)
    {
        $this->success      = 1;
        $this->data         = $this->product->getAllProducts();
        $this->statusCode   = Response::HTTP_OK;
        return $this->buildResponse();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Post(
     *      path="/product/create",
     *      operationId="products.store",
     *      tags={"Product"},
     *      summary="create a new product",
     *      description="create new product",
     *      security={{"bearer_token":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          description="Pass product information",
     *          @OA\JsonContent(
     *              @OA\Property(property="category_uuid", type="string", example="a5da1fb0-fc09-359a-86dc-6b7983e7b693"),
     *              @OA\Property(property="title", type="string", example="New Pet Food"),
     *              @OA\Property(property="price", type="string", example="256.88"),
     *              @OA\Property(property="description", type="string", example="About New Pet Food"),
     *              @OA\Property(property="metadata", type="string", example="{'image': 'string','brand': 'string'}"),
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
     *      ),
     * )
     */
    public function store(StoreRequest $request)
    {
        // get validated request data
        $requestData = $request->validated();
        $newProduct  = $this->product->add($requestData);
        if (!$newProduct instanceof Product) {
            throw new Exception('Product not created');
        }
        $newProduct->uuid = $newProduct->uuid->toString();

        $this->success      = 1;
        $this->data         = $newProduct->toArray();
        $this->statusCode   = Response::HTTP_OK;
        return $this->buildResponse();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $uuid
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *      path="/product/{uuid}",
     *      operationId="products.show",
     *      tags={"Product"},
     *      summary="fetch a product ",
     *      description="fetch a product",
     *      @OA\Parameter(
     *          in="path",
     *          name="uuid",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
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
    public function show($uuid)
    {
        if (!Str::isUuid($uuid)) {
            throw new Exception('not a valid uuid');
        }

        $productResponse = $this->product->show($uuid);
        $this->error = "Product not found";
        if ($productResponse->isNotEmpty() && $productResponse->count()) {
            $this->success    = 1;
            $this->data       = $productResponse->toArray();
            $this->statusCode = Response::HTTP_OK;
            $this->error      = '';
        }

        return $this->buildResponse();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $uuid
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Put(
     *      path="/product/{uuid}",
     *      operationId="products.update",
     *      tags={"Product"},
     *      summary="update a product",
     *      description="update a product",
     *      security={{"bearer_token":{}}},
     *      @OA\Parameter(
     *          in="path",
     *          name="uuid",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          description="Pass product information",
     *          @OA\JsonContent(
     *              @OA\Property(property="category_uuid", type="string", example="a5da1fb0-fc09-359a-86dc-6b7983e7b693"),
     *              @OA\Property(property="title", type="string", example="New Pet Food"),
     *              @OA\Property(property="price", type="string", example="256.88"),
     *              @OA\Property(property="description", type="string", example="About New Pet Food"),
     *              @OA\Property(property="metadata", type="string", example="{'image': 'string','brand': 'string'}"),
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
     *      ),
     * )
     */
    public function update($uuid, UpdateRequest $request)
    {
        if (!Str::isUuid($uuid)) {
            throw new Exception('not a valid uuid');
        }

        // get validated request data
        $requestData = $request->validated();
        $this->error = "Product updated failed";
        if ($this->product->updateDetails($uuid, $requestData)) {
            $this->success    = 1;
            $this->data       = $this->product->show($uuid)->toArray(); // get fresh data from DB
            $this->statusCode = Response::HTTP_OK;
            $this->error      = '';
        }

        return $this->buildResponse();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $uuid
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Delete(
     *      path="/product/{uuid}",
     *      operationId="products.delete",
     *      tags={"Product"},
     *      summary="delete a product",
     *      description="delete a product",
     *      security={{"bearer_token":{}}},
     *      @OA\Parameter(
     *          in="path",
     *          name="uuid",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
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
    public function destroy($uuid)
    {
        if (!Str::isUuid($uuid)) {
            throw new Exception('not a valid uuid');
        }

        $this->error = "Product delete failed";
        if ($this->product->remove($uuid)) {
            $this->success    = 1;
            $this->statusCode = Response::HTTP_OK;
            $this->error      = '';
        }

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
