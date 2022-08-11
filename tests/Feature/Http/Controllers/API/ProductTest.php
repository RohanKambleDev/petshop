<?php

namespace Tests\Feature\Http\Controllers\API;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Services\Auth\Jwt;
use Illuminate\Http\Request;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * responseKeys
     *
     * @return Array
     */
    public function responseKeys()
    {
        return [
            'success',
            'data',
            'error',
            'errors',
            'extra'
        ];
    }

    /**
     * getBearerTokenForTest
     *
     * @return string
     */
    public function getBearerTokenForTest()
    {
        User::factory(1)->create();
        $uuid = User::all()->pluck('uuid')->first();
        $jwt = new Jwt();
        $apiToken = $jwt->getUserApiToken($uuid);
        return 'Bearer ' . $apiToken;
    }

    /**
     * test_product_listing
     *
     * @return void
     */
    public function test_product_listing()
    {
        //prepare
        Category::factory(10)->create();
        Product::factory(10)->create();

        //perform
        $response = $this->json('GET', '/api/v1/products');

        //Write the response in laravel.log
        \Log::info(1, [$response->getContent()]);

        // predict
        $response->assertStatus(200);

        // collect the reponse, in array
        $reponseArr = $response->json();

        // first assert base common keys expected in response
        $responseKeys = $this->responseKeys();
        foreach ($responseKeys as $responseKey) {
            $this->assertArrayHasKey($responseKey, $reponseArr);
        }

        // later assert any other keys required
        $this->assertArrayHasKey('uuid', $reponseArr['data'][0]);
        $this->assertEquals('1', $response['success']);
    }

    /**
     * test_product_view
     *
     * @return void
     */
    public function test_product_view()
    {
        //prepare
        Category::factory(10)->create();
        Product::factory(10)->create();
        $uuid = Product::where('id', 1)->pluck('uuid')->first();

        //perform
        $response = $this->json('GET', '/api/v1/product/' . $uuid);

        //Write the response in laravel.log
        \Log::info(1, [$response->getContent()]);

        // predict
        $response->assertStatus(200);

        // collect the reponse, in array
        $reponseArr = $response->json();

        // first assert base common keys expected in response
        $responseKeys = $this->responseKeys();
        foreach ($responseKeys as $responseKey) {
            $this->assertArrayHasKey($responseKey, $reponseArr);
        }

        // later assert any other keys required
        $this->assertArrayHasKey('uuid', $reponseArr['data'][0]);
        $this->assertEquals('1', $response['success']);
    }

    /**
     * test_product_create
     *
     * @return void
     */
    public function test_product_create()
    {
        //prepare
        $bearerToken = $this->getBearerTokenForTest();
        Category::factory(10)->create();
        $uuid = Category::where('id', 1)->pluck('uuid')->first();

        //perform
        $response = $this->json('POST', '/api/v1/product/create', [
            'category_uuid' => $uuid,
            'title' => 'Product new Test',
            'price' => 253.87,
            'description' => 'Test description',
            'metadata' => 'image:string'
        ], ['Authorization' => $bearerToken]);

        //Write the response in laravel.log
        \Log::info(1, [$response->getContent()]);

        // predict
        $response->assertStatus(200);

        // collect the reponse, in array
        $reponseArr = $response->json();

        // first assert base common keys expected in response
        $responseKeys = $this->responseKeys();
        foreach ($responseKeys as $responseKey) {
            $this->assertArrayHasKey($responseKey, $reponseArr);
        }

        $this->assertEquals('1', $response['success']);
    }

    /**
     * test_product_update
     *
     * @return void
     */
    public function test_product_update()
    {
        //prepare
        $bearerToken = $this->getBearerTokenForTest();
        Category::factory(10)->create();
        Product::factory(10)->create();
        $productUuid  = Product::where('id', 1)->pluck('uuid')->first();
        $categoryUuid = Category::where('id', 1)->pluck('uuid')->first();

        //perform
        $response = $this->json('PUT', '/api/v1/product/' . $productUuid, [
            'category_uuid' => $categoryUuid,
            'title' => 'Product new Test',
            'price' => 253.87,
            'description' => 'Test description',
            'metadata' => 'brand:string'
        ], ['Authorization' => $bearerToken]);

        //Write the response in laravel.log
        \Log::info(1, [$response->getContent()]);

        // predict
        $response->assertStatus(200);

        // collect the reponse, in array
        $reponseArr = $response->json();

        // first assert base common keys expected in response
        $responseKeys = $this->responseKeys();
        foreach ($responseKeys as $responseKey) {
            $this->assertArrayHasKey($responseKey, $reponseArr);
        }

        $this->assertEquals('1', $response['success']);
    }

    /**
     * test_product_delete
     *
     * @return void
     */
    public function test_product_delete()
    {
        //prepare
        $bearerToken = $this->getBearerTokenForTest();
        Category::factory(10)->create();
        Product::factory(10)->create();
        $productUuid  = Product::where('id', 1)->pluck('uuid')->first();

        //perform
        $response = $this->json('DELETE', '/api/v1/product/' . $productUuid, [], ['Authorization' => $bearerToken]);

        //Write the response in laravel.log
        \Log::info(1, [$response->getContent()]);

        // predict
        $response->assertStatus(200);

        // collect the reponse, in array
        $reponseArr = $response->json();

        // first assert base common keys expected in response
        $responseKeys = $this->responseKeys();
        foreach ($responseKeys as $responseKey) {
            $this->assertArrayHasKey($responseKey, $reponseArr);
        }

        $this->assertEquals('1', $response['success']);
    }
}
