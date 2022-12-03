<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Facades\Cafe24Service;
use App\Models\Mall;
use Illuminate\Support\Facades\Storage;

class AuthorizationController extends Controller
{
    /**
     * Get token
     *
     * @param Request $request
     * @return void
     */
    public function getToken(Request $request)
    {
        if ($request->header('App-Key') != env('CAFE24_APP_KEY')) {
            return [
                'error' => 'App key invalid',
            ];
        }
        $contents = Storage::get('cafe24_token.json');
        return $contents;
    }
    /**
     * refresh a new token
     * @param Request $request
     */
    public function refreshToken(Request $request)
    {
        if ($request->header('Authorization') != config('aos.refresh_key')) {
            return ['error' => 'Refresh key incorrect'];
        }
        $appSetting = [
            "refresh_token" => $request->refresh_token,
            "client_id" => config('cafe24.client_id'),
            "client_secret" => config('cafe24.client_secret'),
            "mall_id" => config('aos.mall_id'),
        ];
        $cafe24NewToken = Cafe24Service::refreshToken($appSetting);
        Log::channel('token')->info('API Renew token at ' . date('d-m-Y H:i') . json_encode($cafe24NewToken));
        if (isset($cafe24NewToken['error'])) {
            return $cafe24NewToken;
        }

        // update mall data (access_token, refresh_token, refresh_token_expires_at)
        Cafe24Service::updateMallToken($cafe24NewToken['mall_id'], $cafe24NewToken['access_token'], $cafe24NewToken['refresh_token'], $cafe24NewToken['refresh_token_expires_at']);

        return [
            'access_token' => $cafe24NewToken['access_token'],
            'expires_at' => $cafe24NewToken['expires_at'],
            'refresh_token' => $cafe24NewToken['refresh_token'],
            'refresh_token_expires_at' => $cafe24NewToken['refresh_token_expires_at'],
        ];
    }

    /**
     * put local token
     * @param Request $request
     */
    public function tokenLocal(Request $request)
    {
        $result = $request->all();
        Storage::disk('local')->put('cafe24_token.json', jEncode($result));
    }
}
