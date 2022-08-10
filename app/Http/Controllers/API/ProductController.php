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
