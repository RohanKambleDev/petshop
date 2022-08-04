<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use Illuminate\Support\Facades\App;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

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

    public function testRegister()
    {
        // prepare

        // perform
        $response = $this->json('POST', '/api/v1/user/create', [
            'first_name' => 'Rohan',
            'last_name' => 'Kamble',
            'email' => 'rohantest@gmail.com',
            'password' => 'rohu2187',
            'password_confirmation' => 'rohu2187',
            'avatar' => '818748927349',
            'address' => 'Bhandup',
            'phone_number' => '9967802187',
            'is_marketing' => '1',
        ]);

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
        $this->assertArrayHasKey('token', $reponseArr['data']);
    }


    public function testLogin()
    {
        // prepare

        // perform
        $response = $this->json('POST', '/api/v1/user/login', [
            'email' => 'rohantest@gmail.com',
            'password' => 'rohu2187'
        ]);

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
        $this->assertArrayHasKey('token', $reponseArr['data']);
    }
}
