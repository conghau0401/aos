<?php

namespace App\Services;

class InitAppService
{
    public function checkHmac($data)
    {
        $hmac = $data['hmac'];
        unset($data['hmac']);
        ksort($data);
        $planText = http_build_query($data, '?', '&', PHP_QUERY_RFC3986);
        $hashData = base64_encode(hash_hmac('sha256', $planText, config('cafe24.client_secret'), true));
        return $hashData == $hmac;
    }

    public function checkTimestamp($timestamp)
    {
        $currentTime = time();
        $diffHour = ($currentTime - (int)$timestamp)/3600; // check time <= 2 hour
        return $diffHour <= 2;
    }

    public function verifyCafe24Request($request)
    {
        if(isset($request['timestamp']) && isset($request['hmac'])) {
            if(!$this->checkHmac($request)) {
                abort(403);
            }
            if(!$this->checkTimestamp($request['timestamp'])) {
                throw new \Exception('Request time out!');
            }
        }
    }
}
