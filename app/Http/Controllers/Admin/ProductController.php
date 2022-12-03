<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Facades\Cafe24ApiService;
use App\Models\Mall;
use App\Models\Mapp;
use App\Models\MappOption;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $perPage = config('cafe24.per_page');
        $appends = [];
        $paramCafe24 = [
            'limit' => $perPage,
            'embed' => 'additionalimages,options',
        ];
        if (!empty($request->page)) {
            $paramCafe24['offset'] = ($request->page - 1) * $perPage;
        }
        if (!empty($request->name)) {
            $paramCafe24['product_name'] = $request->name;
            $appends['name'] = $request->name;
        }
        $curPage = $request->page ?? 1;

        $mall = Mall::where('mall_id', config('aos.mall_id'))->first();
        $totalProduct = Cafe24ApiService::get($mall->mall_id, $mall->access_token, 'products/count', $paramCafe24);
        $products = Cafe24ApiService::get($mall->mall_id, $mall->access_token, 'products', $paramCafe24);
        $links = paginateAPI($totalProduct->count, $perPage, $curPage, route('products.index'), $appends);

        return view('admin.products.index', compact('products', 'links'));
    }

    /**
     * product detail
     * @param int $id
     */
    public function show($idCafe24)
    {
        $mall = Mall::where('mall_id', config('aos.mall_id'))->first();

        $productCafe24 = Cafe24ApiService::get($mall->mall_id, $mall->access_token, 'products/' . $idCafe24, ['embed' => 'additionalimages,options,variants']);
        $product = $productCafe24->product;
        // $listIdCate = [];
        // if ($product->category) {
        //     foreach ($product->category as $category) {
        //         array_push($listIdCate, $category->category_no);
        //     }
        // }

        // // categories
        // $categories = Cafe24ApiService::get($mall->mall_id, $mall->access_token, 'categories', ['limit' => 100]);
        // $categories2 = Cafe24ApiService::get($mall->mall_id, $mall->access_token, 'categories', ['limit' => 100, 'offset' => 100]);
        // $categories = array_merge($categories2->categories, $categories->categories);
        // $categories = buildTree($categories);
        // $options = buildOptions($categories, '=', $listIdCate);

        // Origin
        if (Cache::has('origin')) {
            $origin = json_decode(Cache::get('origin'));
        } else {
            $origin1 = Cafe24ApiService::get($mall->mall_id, $mall->access_token, 'origin', ['limit' => 100]);
            $origin2 = Cafe24ApiService::get($mall->mall_id, $mall->access_token, 'origin', ['limit' => 100, 'offset' => 100]);
            $origin3 = Cafe24ApiService::get($mall->mall_id, $mall->access_token, 'origin', ['limit' => 100, 'offset' => 200]);
            $origin = array_merge($origin1->origin, $origin2->origin, $origin3->origin);
            Cache::put('origin', jEncode($origin), 84600);
        }

        // manufacturers
        $manufacturers = Cafe24ApiService::get($mall->mall_id, $mall->access_token, 'manufacturers', ['limit' => 100]);

        // suppliers
        $suppliers = Cafe24ApiService::get($mall->mall_id, $mall->access_token, 'suppliers', ['limit' => 100]);

        // icons
        // $icons = Cafe24ApiService::get($mall->mall_id, $mall->access_token, 'products/icons');

        return view('admin.products.detail', compact('product', 'origin', 'manufacturers', 'suppliers', 'mall'));
    }

    /**
     * Update a product
     * @param int $id
     */
    public function update($id, Request $request)
    {
        $mall = Mall::where('mall_id', config('aos.mall_id'))->first();
        $additional = [];
        $i = 0;
        if (is_array($request->additional)) {
            foreach ($request->additional as $key => $value) {
                $additional[$i]['key'] = $key;
                $additional[$i]['value'] = $value;
                $i++;
            }
        }
        $data = [
            'shop_no' => 1,
            'request' => [
                "delete_category_no" => json_decode($request->old_cate),
                'product_name' => $request->product_name,
                'additional_image' => $request->additional_image ?? [],
                'additional_information' => $additional,
                'icon' => $request->icons,
                'custom_product_code' => $request->custom_product_code,
                'internal_product_name' => $request->internal_product_name,
                'product_material' => $request->product_material,
                'manufacturer_code' => $request->manufacturer_code,
                'tax_type' => $request->tax_type,
                'tax_amount' => $request->tax_amount,
                'minimum_quantity' => 1,
                'maximum_quantity' => $request->maximum_quantity,
                'made_in_code' => $request->made_in_code,
                'release_date' => $request->release_date,
                'selling' => !empty($request->selling) ? 'T' : 'F',
                'display' => !empty($request->display) ? 'T' : 'F',
                'supplier_code' => $request->supplier_code,
                'description' => $request->description,
                'supply_product_name' => $request->supply_product_name,
                'product_tag' => $request->product_tag,
                'summary_description' => $request->summary_description,
            ],
        ];

        // list category no
        $listCategoryNo = [];
        if (is_array($request->category_no)) {
            foreach ($request->category_no as $item) {
                array_push($listCategoryNo, [
                    'category_no' => $item,
                ]);
            }
            $data['request']['add_category_no'] = $listCategoryNo;
        }
        if ($request->hasFile('detail_image')) {
            $file =  $request->file('detail_image');
            $base64 = base64_encode(file_get_contents($file->getPathName()));
            $dataImage = [
                "shop_no" => 1,
                "request" => [
                    "image_upload_type" => "A",
                    "detail_image" => $base64,
                ]
            ];
            $result = Cafe24ApiService::post($mall->mall_id, $mall->access_token, 'products/' . $id . '/images', $dataImage);
            Log::info('Updated product image: ' . json_encode($result));
        }
        $result = Cafe24ApiService::put($mall->mall_id, $mall->access_token, 'products/' . $id, $data);
        if (isset($result->error)) {
            return redirect(route('products.index'))->with(['error' => $result->error->message]);
        }
        $mapp = Mapp::where(['type' => 'product', 'cafe24_id' => $id])->first();
        foreach ($additional as $key => $value) {
            if ($value['key'] == 'custom_option1') {
                MappOption::updateOrCreate([
                    'map_id' => $mapp->id,
                    'meta_key' => 'marketable_size',
                ], [
                    'meta_value' => $value['value'],
                ]);
            }
        }
        return redirect(route('products.index'));
    }
}
