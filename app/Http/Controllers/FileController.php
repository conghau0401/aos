<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facades\Cafe24ApiService;
use App\Models\Mall;
use Illuminate\Support\Facades\Log;

class FileController extends Controller
{
    protected $token;
    protected $apiResponse;

    public function __construct(\App\Contracts\ApiResponseInterface $apiResponse)
    {
        $this->token = request()->bearerToken();
        $this->apiResponse = $apiResponse;
    }

    /**
     * Upload image
     *
     * @param Request $request
     * @return response
     */
    public function uploadImage(Request $request)
    {
        // validation
        if (!$request->file('file')) {
            return $this->apiResponse->ok(false, []);
        }
        $file = $request->file('file');
        $path = $file->getPathName();
        $base64 = base64_encode(file_get_contents($path));

        $data = [
            'request' => [
                ['image' => $base64]
            ],
        ];
        $mall = Mall::where('mall_id', config('aos.mall_id'))->first();
        $result = Cafe24ApiService::post($mall->mall_id, $mall->access_token, 'products/images', $data);
        Log::info('Upload image:' . json_encode($result));
        if (isset($result->errors)) {
            return $this->apiResponse->ok(false, $result->errors);
        }

        $itemImage = end($result->images);
        return $this->apiResponse->ok(true, $itemImage);
    }
}
