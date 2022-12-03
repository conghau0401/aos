<?php

namespace App\Services;

use App\Facades\Cafe24Service;
use App\Models\Mall;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class Cafe24ApiService
{
    protected $method = "GET";
    protected $domain;
    protected $adminApiPrefix;
    protected $version;
    protected $clientId;
    protected $clientSecret;
    protected $mallId;
    protected $clientHttp;

    public function __construct()
    {
        $this->domain = config('cafe24.api_domain');
        $this->adminApiPrefix = config('cafe24.api_admin_prefix');
        $this->version = config('cafe24.api_version');
        $this->clientId = config('cafe24.client_id');
        $this->clientSecret = config('cafe24.client_secret');
    }

    /**
     * Method get
     *
     * @param string $mallId
     * @param string $accessToken
     * @param string $endPoint
     * @param array $params
     *
     * @return void
     */
    public function get($mallId, $accessToken, $endPoint, $params = array(), $addition = [])
    {
        $this->method = "GET";
        $result = $this->callAPI($mallId, $accessToken, $endPoint, $params, $addition);
        return $result;
    }

    /**
     * Method post
     *
     * @param string $mallId
     * @param string $accessToken
     * @param string $endPoint
     * @param array $params
     *
     * @return void
     */
    public function post($mallId, $accessToken, $endPoint, $params = array())
    {
        $this->method = "POST";
        $result = $this->callAPI($mallId, $accessToken, $endPoint, $params);
        return $result;
    }

    /**
     * Method PUT
     *
     * @param string $mallId
     * @param string $accessToken
     * @param string $endPoint
     * @param array $params
     *
     * @return void
     */
    public function put($mallId, $accessToken, $endPoint, $params = array())
    {
        $this->method = "PUT";
        $result = $this->callAPI($mallId, $accessToken, $endPoint, $params);
        return $result;
    }

    /**
     * Method DELETE
     *
     * @param string $mallId
     * @param string $accessToken
     * @param string $endPoint
     * @param array $params
     *
     * @return void
     */
    public function delete($mallId, $accessToken, $endPoint, $params = array())
    {
        $this->method = "DELETE";
        $result = $this->callAPI($mallId, $accessToken, $endPoint, $params);
        return $result;
    }

    /**
     * Method getMallInfo
     *
     * @param string $mallId
     * @param string $accessToken
     * @param string $endPoint
     * @param array $params
     *
     * @return array $mall_info
     */
    public function callAPI($mallId, $accessToken, $endPoint, $params = array(), $addition = [])
    {
        $url = "https://{$mallId}.{$this->domain}/{$this->adminApiPrefix}/{$endPoint}";

        $header = [
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
            'X-Cafe24-Api-Version' => $this->version,
            'X-Cafe24-Api-Spec-Cache' => 'off',
        ];
        switch ($this->method) {
            case 'GET':
                $response = Http::withHeaders($header)->get($url, $params);
                break;
            case 'POST':
                $response = Http::withHeaders($header)->post($url, $params);
                break;
            case 'PUT':
                $response = Http::withHeaders($header)->put($url, $params);
                break;
            case 'DELETE':
                $response = Http::withHeaders($header)->delete($url, $params);
                break;
        }
        $result = json_decode($response->getBody()->getContents());

        // If have errors
        if (isset($result->error)) {
            if (isset($addition['aos_request'])) {
                return $result;
            }
            if (strpos($result->error->message, config('cafe24.text_status.token_expired')) > -1) { //access_token expired
                $mallInfo = Mall::where('mall_id', $mallId)->first();
                $appSetting = [
                    "refresh_token" => $mallInfo->refresh_token,
                    "client_id" => $this->clientId,
                    "client_secret" => $this->clientSecret,
                    "mall_id" => $mallId,
                ];

                // get new token
                $cafe24NewToken = Cafe24Service::refreshToken($appSetting);
                Log::channel('token')->info('Renew token at ' . date('d-m-Y H:i') . json_encode($cafe24NewToken));
                if (isset($cafe24NewToken['error'])) {
                    return $cafe24NewToken;
                }

                // update mall data (access_token, refresh_token, refresh_token_expires_at)
                Cafe24Service::updateMallToken($cafe24NewToken['mall_id'], $cafe24NewToken['access_token'], $cafe24NewToken['refresh_token'], $cafe24NewToken['refresh_token_expires_at']);

                // recall Cafe24 API
                $accessToken = $cafe24NewToken['access_token'];
                return $this->callAPI($mallId, $accessToken, $endPoint, $params);
            }
        }
        return $result;
    }
}
