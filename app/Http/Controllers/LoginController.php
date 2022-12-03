<?php

namespace App\Http\Controllers;

use App\Facades\AOSAPI;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    protected $token;

    public function __construct()
    {
        $this->token = request()->bearerToken();
    }

    /**
     * Login AOS function
     *
     * @param Request $request
     * @return object
     */
    public function login(Request $request)
    {
        $url = config('aos.domain_api') . 'iam/oauth/token';
        $params = [
            'username' => $request->username,
            'password' => $request->password,
            'grant_type' => 'password',
        ];
        $header = [
            'Authorization' => config('aos.login.authorization'),
        ];

        $response = Http::withHeaders($header)->asForm()->post($url, $params);
        $result = json_decode($response->getBody()->getContents());
        if (isset($result->access_token)) {
            // check role type
            $userAOS = AOSAPI::get($result->access_token, 'user/user/' . $result->userId);
            if (!in_array($userAOS->data->roleId, config('aos.role_type')) || empty($userAOS->data->clientId)) {
                return [
                    'error' => 'unauthorized',
                    'message' => 'Incorrect role or empty store'
                ];
            }
            $user = User::where('email', $request->username)->first();
            if (empty($user)) {
                User::create([
                    'email' => $request->username,
                    'name' => $result->userId,
                    'doosoun_id' => $result->userId,
                    'access_token' => $result->access_token,
                    'password' => Hash::make($request->password),
                    'store_id' => $userAOS->data->clientId,
                ]);
            } else {
                User::where('email', $request->username)->update([
                    'password' => Hash::make($request->password),
                    'access_token' => $result->access_token,
                    'store_id' => $userAOS->data->clientId,
                ]);
            }
        }

        return $result;
    }

    /**
     * Logout function
     *
     * @return void
     */
    public function logout()
    {
        AOSAPI::get($this->token, 'iam/oauth/logout');
    }
}
