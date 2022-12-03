<?php

namespace App\Services;

use App\Models\Mall;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Cafe24Service
{
    /**
     * Run first time to install application
     *
     * @param array $appSetting (need params: mall_id, client_id, redirect_uri, state, scope)
     *
     * @return void
     */
    public function installApp($appSetting)
    {
        if (!empty($appSetting['mall_id'])) {
            $scope = config('cafe24.scope');
            if (!empty($appSetting['scope'])) {
                $scope .= $appSetting['scope'] . ",";
            }
            $urlCode = "https://{$appSetting['mall_id']}.cafe24api.com/api/v2/oauth/authorize?response_type=code&client_id={$appSetting['client_id']}&state={$appSetting['state']}&redirect_uri={$appSetting['redirect_uri']}&scope={$appSetting['scope']}";
            redirect($urlCode)->send();
            exit;
        }
    }

    /**
     * Get Mall ID
     *
     * @return string $mallId
     */
    public function getMallId()
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $parseReferer = parse_url($referer);
        if (empty($referer) || strpos($parseReferer['host'], '.' . config('cafe24.host')) == false) {
            return Session::get('mall_id');
        }
        $mallId = explode('.', $parseReferer['host'])[0];
        Session::put('mall_id', $mallId);
        return $mallId;
    }

    /**
     * Get Token Cafe24 API
     *
     * @param array $appSetting (need params: mall_id, client_id, client_secret, redirect_uri, code, scope)
     * @return array $data
     */
    public function getToken($appSetting)
    {
        $params = [
            'grant_type' => 'authorization_code',
            'code' => $appSetting['code'],
            'redirect_uri' => $appSetting['redirect_uri'],
        ];
        $url = "https://{$appSetting['mall_id']}.cafe24api.com/api/v2/oauth/token";
        $header = [
            "Authorization" => "Basic {" . base64_encode("{$appSetting['client_id']}:{$appSetting['client_secret']}") . "}",
        ];
        $response = Http::asForm()->withHeaders($header)->post($url, $params);

        return json_decode($response->getBody(), true);
    }

    /**
     * Method refreshToken
     * @param string $appSetting [refresh_token , client_id , client_secret , mall_id]
     * @return array $data
     */
    public function refreshToken($appSetting)
    {
        $params = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $appSetting['refresh_token'],
        ];
        $header = [
            "Authorization" => "Basic {" . base64_encode("{$appSetting['client_id']}:{$appSetting['client_secret']}") . "}",
        ];
        $url = "https://{$appSetting['mall_id']}.cafe24api.com/api/v2/oauth/token";

        $response = Http::asForm()->withHeaders($header)->post($url, $params);
        $result = json_decode($response->getBody(), true);
        if (isset($result['access_token'])) {
            try {
                if (env('APP_ENV') == 'local') {
                    $resultAos = Http::withHeaders([
                        'Authorization' => config('aos.auth_send_token'),
                    ])->put(env('URL_TOKEN_LOCAL'), [
                        'access_token' => $result['access_token'],
                        'expires_at' => $result['expires_at'],
                        'refresh_token' => $result['refresh_token'],
                        'refresh_token_expires_at' => $result['refresh_token_expires_at'],
                    ]);
                    Log::channel('api')->info('Sent token local: ' . $result);
                } else {
                    $resultAos = Http::withHeaders([
                        'Authorization' => config('aos.auth_send_token'),
                    ])->put(config('aos.url_token'), [
                        'access_token' => $result['access_token'],
                        'expires_at' => $result['expires_at'],
                        'refresh_token' => $result['refresh_token'],
                        'refresh_token_expires_at' => $result['refresh_token_expires_at'],
                    ]);
                    Log::channel('api')->info('Sent token: ' . $resultAos->getBody()->getContents());
                }
            } catch (\Throwable $th) {
                Log::channel('api')->info('Error send token: ' . json_encode($result));
            }
        }
        return json_decode($response->getBody(), true);
    }

    /**
     * Update token for mall
     *
     * @param string $mallId
     * @param string $accessToken
     * @param string $refreshToken
     * @param string $refreshTokenExpires
     *
     * @return boolean
     */
    public function updateMallToken($mallId, $accessToken, $refreshToken, $refreshTokenExpires)
    {
        return Mall::where("mall_id", $mallId)->update([
            "access_token" => $accessToken,
            "refresh_token" => $refreshToken,
            "refresh_token_expires_at" => $refreshTokenExpires,
        ]);
    }
}
