<?php

namespace App\Http\Controllers;

use App\Facades\AOSAPI;
use App\Models\Banner;
use App\Models\BannerImage;

class BannerController extends Controller
{
    protected $token;
    protected $apiResponse;

    public function __construct(\App\Contracts\ApiResponseInterface $apiResponse)
    {
        $this->token = request()->bearerToken();
        $this->apiResponse = $apiResponse;
    }

    /**
     * Get banners
     *
     * @return void
     */
    public function list()
    {
        $params = [
            'isAOS' => true,
        ];
        return AOSAPI::get($this->token, 'product/m1043_8/AOS', $params);
    }

    /**
     * Banner detail
     * @param int $id
     */
    public function detail($bannerId)
    {
        $result = BannerImage::select('id', 'banner_id', 'name', 'url', 'image')->where('banner_id', $bannerId)->get();
        return $this->apiResponse->ok(true, $result);
    }
}
