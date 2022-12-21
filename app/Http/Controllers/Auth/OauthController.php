<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client as Guzzle;

class OauthController extends Controller
{
    protected $client;

    public function __construct(Guzzle $client)
    {
        $this->middleware('auth');
        $this->client = $client;
    }

    public function redirect()
    {
        $query = http_build_query([
            'client_id' => '4',
            'redirect_uri' => 'http://laravel-passclient.test/auth/passport/callback',
            'response_type' => 'code',
            'scope' => 'view-tweet'
        ]);

        return redirect('http://laravel-passport.test/oauth/authorize?' . $query);
    }

    public function callback(Request $request)
    {
        $response = $this->client->post('http://laravel-passport.test/oauth/token', [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => '4',
                'client_secret' => 'vmhDClw1AlMBwo6Wma0IHpwe3m0jnUmHxBL2RbV6',
                'redirect_uri' => 'http://laravel-passclient.test/auth/passport/callback',
                'code' => $request->code
            ]
        ]);

        $response = json_decode($response->getBody());

        $request->user()->token()->delete();
        $request->user()->token()->create([
            'expires_in' => $response->expires_in,
            'access_token' => $response->access_token,
            'refresh_token' => $response->refresh_token
        ]);

        return redirect('/home');
    }

    public function refresh(Request $request)
    {
        $response = $this->client->post('http://laravel-passport.test/oauth/token', [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => $request->user()->token->refresh_token,
                'client_id' => '4',
                'client_secret' => 'vmhDClw1AlMBwo6Wma0IHpwe3m0jnUmHxBL2RbV6',
                'scope' => 'view-tweet'
            ]
        ]);

        $response = json_decode($response->getBody());

        $request->user()->token()->update([
            'expires_in' => $response->expires_in,
            'access_token' => $response->access_token,
            'refresh_token' => $response->refresh_token
        ]);

        return redirect('/home');
    }
}
