<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

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
     * test_user_register
     *
     * @return void
     */
    public function test_user_register()
    {
        // prepare

        // perform
        $response = $this->json('POST', '/api/v1/user/create', [
            'first_name' => 'Rohan',
            'last_name' => 'Kamble',
            'email' => 'rohan@gmail.com',
            'password' => 'rohu2187',
            'password_confirmation' => 'rohu2187',
            'avatar' => null,
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


    /**
     * test_user_login
     *
     * @return void
     */
    public function test_user_login()
    {
        // prepare
        User::factory(1)->create();

        // perform
        $response = $this->json('POST', '/api/v1/user/login', [
            'email' => 'rohu2187@gmail.com',
            'password' => 'rohan'
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
