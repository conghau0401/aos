<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Facades\Cafe24ApiService;
use App\Models\Mapp;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class APICategoryController extends Controller
{
    /**
     * Category list function
     *
     * @param Request $request
     * @return response
     */
    public function index(Request $request)
    {
        $params = $request->all();
        return Cafe24ApiService::get(config('aos.mall_id'), $request->bearerToken(), 'categories', $params);
    }

    /**
     * Create a category
     * @param Request $request
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'request.category_id' => 'required',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'category_id is required',
            ];
        }

        $params = $request->all();
        $params = $params['request'];

        // check existed category
        $checkExitst = Mapp::where(['type' => 'category', 'doosoun_id' => @$params['category_id']])->first();
        if (!empty($checkExitst)) {
            return [
                'success' => false,
                'message' => 'Category id existed',
            ];
        }

        $data = [
            'shop_no' => 1,
            'request' => [
                'category_name' => @$params['category_name'],
                'use_display' => 'T',
            ],
        ];
        $result = Cafe24ApiService::post(config('aos.mall_id'), $request->bearerToken(), 'categories', $data);
        Log::channel('api')->info('API created category: ' . json_encode($result));
        if (isset($result->error)) {
            return $result;
        }

        // create product in app
        $mapp = Mapp::create([
            'doosoun_id' => @$params['category_id'],
            'cafe24_id' => $result->category->category_no,
            'shop_no' => 1,
            'mall_id' => config('aos.mall_id'),
            'type' => 'category',
        ]);
        Log::channel('api')->info('Insert mapping category table app: ' . json_encode($mapp));
        return $this->apiResponse->ok(true, $result->category);
    }

    /**
     * update a category
     *
     * @param int $categoryNo
     * @param Request $request
     * @return void
     */
    public function update($categoryNo, Request $request)
    {
        $checkExitst = Mapp::where(['type' => 'category', 'doosoun_id' => $categoryNo])->first();
        if (empty($checkExitst)) {
            return [
                'success' => false,
                'message' => 'Category not exitst',
            ];
        }
        $categoryNo = $checkExitst->cafe24_id;

        $params = $request->all();
        $params = $params['request'];
        $data = [
            'shop_no' => 1,
            'request' => [
                'category_name' => @$params['category_name'],
            ],
        ];
        $result = Cafe24ApiService::put(config('aos.mall_id'), $request->bearerToken(), 'categories/' . $categoryNo, $data);
        Log::channel('api')->info('API updated category: ' . json_encode($result));
        if (isset($result->error)) {
            return $result;
        }
        return $this->apiResponse->ok(true, $result->category);
    }

    /**
     * Delete a category
     *
     * @param int $categoryNo
     * @return void
     */
    public function delete($categoryNo, Request $request)
    {
        $checkExitst = Mapp::where(['type' => 'category', 'doosoun_id' => $categoryNo])->first();
        if (empty($checkExitst)) {
            return [
                'success' => false,
                'message' => 'Category not exitst',
            ];
        }
        $categoryNo = $checkExitst->cafe24_id;
        $result = Cafe24ApiService::delete(config('aos.mall_id'), $request->bearerToken(), 'categories/' . $categoryNo);
        Log::channel('api')->info('API deleted category: ' . json_encode($result));
        if (isset($result->error)) {
            return $result;
        }
        Mapp::where(['type' => 'category', 'cafe24_id' => $categoryNo])->delete();
        return [
            'success' => true
        ];
    }
}
