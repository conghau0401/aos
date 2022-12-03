<?php

namespace App\Services;

use App\Models\Mall;
use App\Facades\Cafe24Service;
use App\Facades\Cafe24ApiService;
use Illuminate\Support\Facades\Log;

class MallService
{
    protected $initService;

    public function __construct(\App\Services\InitAppService $initService)
    {
        $this->initService = $initService;
    }

    /**
     * @param array $params
     *
     * @return bool|Mall
     */
    public function createOrUpdateMall($params)
    {
        $shopEndpoint = "shops";
        $result = Cafe24ApiService::get($params['mall_id'], $params['access_token'], $shopEndpoint);

        Log::info(json_encode($result));
        foreach ($result->shops as $key => $shop) {
            $mallUrl = !empty($shop->primary_domain) ? $shop->primary_domain : $shop->base_domain;
            $dataMall = [
                'mall_id' => $params['mall_id'],
                'mall_name' => $shop->shop_name,
                'mall_url' => $mallUrl,
                'shop_no' => $shop->shop_no,
                'default' => $shop->default,
                'pc_skin_no' => $shop->pc_skin_no,
                'mobile_skin_no' => $shop->mobile_skin_no,
                'access_token' => $params['access_token'],
                'refresh_token' => $params['refresh_token'],
                'refresh_token_expires_at' => $params['refresh_token_expires_at'],
                'is_deleted' => 0,
            ];
            $checkMall = Mall::where([
                'mall_id' => $params['mall_id'],
                'shop_no' => $shop->shop_no,
            ])->first();
            //create mall to database
            if (empty($checkMall)) {
                Mall::create($dataMall);
            }
            //update mall to database
            Mall::where([
                'mall_id' => $params['mall_id'],
                'shop_no' => $shop->shop_no,
            ])->update($dataMall);
        }
        return Mall::where('mall_id', $params['mall_id'])->first();
    }

    /**
     * @param string $mallId
     * @param string $accessToken
     * @param string $refreshToken
     *
     * @return Object
     */
    public function checkMall($mallId, $accessToken, $refreshToken)
    {
        $endpoint = "shops";
        return Cafe24ApiService::get($mallId, $accessToken, $endpoint);
    }

    /**
     * @param Request $request
     *
     * @return Mall
     */
    public function installApp($request)
    {
        $mallId = Cafe24Service::getMallId();
        if(empty($mallId)) {
            abort('403');
        }

        /******** App Setting ********/
        $appSetting = [
            'mall_id' => $mallId,
            'state' => config('cafe24.state'),
            'client_id' => config('cafe24.client_id'),
            'client_secret' => config('cafe24.client_secret'),
            'redirect_uri' => config('cafe24.redirect_uri'),
            'scope' => config('cafe24.scope'),
            'code'=> $request->code ?? '',
        ];

        if (!$request->has('code')) {
            // check hmac & timestamp
            $this->initService->verifyCafe24Request($request->all());
            Cafe24Service::installApp($appSetting);
        }

        $tokenData = Cafe24Service::getToken($appSetting);
        return $this->createOrUpdateMall($tokenData);
    }

    /**
     * Enable - Disable app
     *
     * @param string $mallId
     * @param int $isDisable
     *
     * @return Mall
     */
    public function updateStatusApp($mallId, $isDisable)
    {
        //update status mall
        return Mall::where('mall_id', $mallId)->update(['is_disabled' => $isDisable]);
    }
}
