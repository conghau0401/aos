<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AOSService
{
    protected $method = "GET";
    protected $domain;

    public function __construct()
    {
        $this->domain = config('aos.domain_api');
    }

    /**
     * Method get
     *
     * @param string $token
     * @param string $endPoint
     * @param array $params
     *
     * @return object
     */
    public function get($token, $endPoint, $params = array())
    {
        $this->method = "GET";
        $result = $this->callAPI($token, $endPoint, $params);
        return $result;
    }

    /**
     * Method post
     *
     * @param string $token
     * @param string $endPoint
     * @param array $params
     *
     * @return object
     */
    public function post($token, $endPoint, $params = array())
    {
        $this->method = "POST";
        $result = $this->callAPI($token, $endPoint, $params);
        return $result;
    }

    /**
     * Method put
     *
     * @param string $token
     * @param string $endPoint
     * @param array $params
     *
     * @return object
     */
    public function put($token, $endPoint, $params = array())
    {
        $this->method = "PUT";
        $result = $this->callAPI($token, $endPoint, $params);
        return $result;
    }

    /**
     * Method callAPI
     *
     * @param string $token
     * @param string $endPoint
     * @param array $params
     *
     * @return object
     */
    public function callAPI($token, $endPoint, $params = array())
    {
        $url = $this->domain . $endPoint;

        $header = [
            'Authorization' => 'Bearer ' . $token,
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
        }
        $result = json_decode($response->getBody()->getContents());
        return $result;
    }
}
