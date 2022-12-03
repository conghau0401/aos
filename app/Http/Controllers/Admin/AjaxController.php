<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Facades\Cafe24ApiService;
use App\Models\BannerImage;
use App\Models\Mall;
use Illuminate\Support\Facades\Log;

class AjaxController extends Controller
{
    public function uploadProductImage(Request $request)
    {
        $file = $request->file('file');
        $path = $file->getPathName();
        $base64 = base64_encode(file_get_contents($path));
        $data = [
            'shop_no' => 1,
            'request' => [
                'additional_image' => [$base64],
            ],
        ];
        $mall = Mall::where('mall_id', config('aos.mall_id'))->first();
        $result = Cafe24ApiService::post($mall->mall_id, $mall->access_token, 'products/' . $request->idProduct . '/additionalimages', $data);
        Log::info(json_encode($result));
        if (isset($result->error)) {
            return;
        }
        $linkImage = '';
        foreach ($result->additionalimage->additional_image as $item) {
            $linkImage = $item->big;
        }
        return $linkImage;
    }

    public function deleteVariant(Request $request)
    {
        $mall = Mall::where('mall_id', config('aos.mall_id'))->first();
        $result = Cafe24ApiService::delete($mall->mall_id, $mall->access_token, 'products/' . $request->idProduct . '/variants/' . $request->code);
        Log::info('Deleted variant' . json_encode($result));
        return $result;
    }

    public function uploadBannerImage(Request $request)
    {
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
        Log::info('Upload banner image' . json_encode($result));
        if (isset($result->error)) {
            return;
        }
        $linkImage = '';
        foreach ($result->images as $item) {
            $linkImage = $item->path;
        }
        return $linkImage;
    }

    public function deleteBannerImage(Request $request)
    {
        return BannerImage::where('id', $request->id)->delete();
    }
}
