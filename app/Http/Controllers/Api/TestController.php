<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Facades\Cafe24ApiService;
use App\Models\Mall;
use App\Models\Mapp;
use Illuminate\Support\Facades\Log;
use App\Facades\AOSAPI;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{
    public function index()
    {
        // get list menus
        $result = Storage::disk('local')->get('product.json');
        $result = json_decode($result, true);
        $result = $result['data'];
        $menus = [];
        foreach ($result as $key => $product) {
            $type = $product["productTp"];
            $largeCategory = $product["largeCategory"];
            $largeCategoryName = $product["largeCategoryName"];
            $mediumCategory = $product["mediumCategory"];
            $mediumCategoryName = $product["mediumCategoryName"];
            $smallCategory = $product["smallCategory"];
            $smallCategoryName = $product["smallCategoryName"];

            $cate_depth_2 = [
                "name" => $mediumCategoryName,
                "sub" => [
                    $smallCategory => [
                        "name" => $smallCategoryName
                    ]
                ]
            ];

            $root_cate = [
                "name" => $largeCategoryName,
                "sub" => [
                    $mediumCategory => $cate_depth_2
                ]
            ];
            $menus[$type]['name'] = $product["productTpName"];
            $menus[$type][$largeCategory] = $root_cate;
        }

        dd($menus);

        // get token aos
        $url = config('aos.domain_api') . 'iam/oauth/token';
        $params = [
            'username' => 'smdc@gmail.com',
            'password' => '1234',
            'grant_type' => 'password',
        ];
        $header = [
            'Authorization' => config('aos.login.authorization'),
        ];

        $result = Http::withHeaders($header)->asForm()->post($url, $params);
        $result = json_decode($result);
        $a = '{
            "pageIndex": 0,
            "pageSize": 1,
            "orderBy": false,
            "productNames": "et"
        }';
        $timeStart = microtime(true);
        $result = AOSAPI::post($result->access_token, 'product/product', json_decode($a, true));
        $timeEnd = microtime(true);
        echo ('Doosoun: ' . $timeEnd - $timeStart . ' Items: ' . $result->count . ' <br>');

        $ids = [];
        foreach ($result->data as $key => $product) {
            array_push($ids, $product->productId);
        }

        // query db app time
        $timeStart = microtime(true);
        $result = Mapp::where('type', 'product')->whereIn('doosoun_id', $ids)->get();
        $timeEnd = microtime(true);
        echo ('App: ' . $timeEnd - $timeStart . ' Items: ' . count($result) . '<br>');

        $ids = [];
        foreach ($result as $key => $map) {
            array_push($ids, $map->cafe24_id);
        }

        // cafe24 time
        $timeStart = microtime(true);
        $mall = Mall::where('mall_id', config('aos.mall_id'))->first();
        $result = Cafe24ApiService::get(config('aos.mall_id'), $mall->access_token, 'products', ['product_no' => implode(',', $ids), 'limit' => 100]);
        $timeEnd = microtime(true);
        echo ('Cate24: ' . $timeEnd - $timeStart . ' Items: ' . count($result->products) . '<br>');

        $productResult = [];
        foreach ($result->products as $key => $value) {
            array_push($productResult, $value);
        }

        // add meta key - value to results
        $metaResult = Mapp::where('type', 'product')->whereIn('cafe24_id', $ids)->leftJoin('mapp_options', 'mapps.id', '=', 'mapp_options.map_id')->get();
        foreach ($productResult as &$pro) {
            foreach ($metaResult as $meta) {
                if ($pro->product_no == $meta->cafe24_id) {
                    $pro->{$meta->meta_key} = $meta->meta_value;
                }
            }
        }
        dd($productResult);
    }
}
