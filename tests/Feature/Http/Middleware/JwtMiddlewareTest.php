<?php

namespace Tests\Feature;

use Log;
use Tests\TestCase;
use App\Models\User;
use App\Services\Auth\Jwt;
use Illuminate\Http\Request;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JwtMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_handle()
    {
        // prepare
        User::factory(1)->create();
        $user = User::all();
        $uuid = $user->first()->uuid;
        // $uuid = '96f26bc1-3cd2-4154-bec9-1df5c3026387';

        // perform
        $jwt = new Jwt();
        $apiToken = $jwt->getUserApiToken($uuid);
        // $apiToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJodHRwczovL3JvaHV0ZWNoLmNvbSIsImp0aSI6Ijk2ZjI3MGRiLWEwODItNDFkYS05MmMxLTBiMjFmZGVmNGY5OSIsImlhdCI6MTY1OTY4MDE5Ny4wNjM1MjIsIm5iZiI6MTY1OTY4MDE5OC4wNjM1MjIsImV4cCI6MTY1OTY4MDc5Ny4wNjM1MjJ9.ERnOCXWDkrEgbF4TSimOFei5Lh_VPGweKyUSksP-QgCZqUomIxyCYCnW8bULt0nSt_YfG1p6BXkdCNgqqhkn4xO3gEg5wkiXbqwbt-5QoUDG1uiKntsIfdiJT-MK6OwjsWrveV0sPRZV6DjUEz2p2hfrkKBBQMdK1dMttrZxBje5dOcWiul9GaVRXt623KHpBCTK2kieWFyAlwranmgYrXDKZ58PZX7icMJDljg-nF7RajxrVc2U5XiZSpEH47ZcEBKL8-xkipVSRrbDav-zMP3kEvwRfaHH4Smoidy-1AzjZMIVXBIEG4GVI2Bvumll94J0CB9DsA2t79zxPZ1Hqq0mdHTGv5VynavqungXn-Va4fntg-axAjtsl06dlS8lmzMxgZQ9bdtN5tRKle37WuIaucTJURSSK6AyqoWTcEtypQQZ-Hbn7rAPHmDRPaKAUZbpZs0DDOw5GMins8grJ2Xo_tH28aaZAD58aGviepYw8mGMjXd7e1fpkl2tiV7NLbd7MRKD4LjPem1vuW_bNxCp-DPKJVtKTZ_kYoYMf_0BlZp37q41RXUQsf07-aLAIjQmAC12KEuU70Oy_uKv61-zyIyHWup6ziOM7-6iT6jVt68nnhURH_aJJG43QtUFrqg4xlxOLJT2l2a4h0X1ouiTrz044GFox9MnLfxbK9M';

        $bearerToken = 'Bearer ' . $apiToken;
        $request = new Request;
        $request->headers->set('Authorization', $bearerToken);

        // predict
        $jwtMiddleware = new JwtMiddleware;
        $jwtMiddleware->handle($request, function ($req) use ($apiToken) {
            $this->assertEquals($apiToken, $req->bearerToken());
        });

        //Write the response in laravel.log
        info('JWT MIddleware will always show as risky since the jwt token\'s nbf claim is minimum +1 sec & tests are run immediately');
    }
}
