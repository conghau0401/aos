<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    /**
     * Login view
     *
     * @return void
     */
    public function index()
    {
        return redirect(route('products.index'));
    }

    /**
     * Login submit
     *
     * @param Request $request
     * @return void
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
        $result = $response->getBody()->getContents();
        $result = json_decode($result);
        if (isset($result->access_token)) {
            $request->session()->put('admin_token', $result->access_token);
            return redirect(route('products.index'));
        }
        return redirect(route('admin.login'))->with('status', 'Username or password incorrect!');
    }
}
