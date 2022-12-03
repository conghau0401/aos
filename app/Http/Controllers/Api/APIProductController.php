<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Facades\Cafe24ApiService;
use App\Models\MappOption;
use App\Models\Mapp;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class APIProductController extends Controller
{
    protected $productService;
    protected $apiResponse;

    public function __construct(
        \App\Services\Api\ProductService $productService,
        \App\Contracts\ApiResponseInterface $apiResponse
    ) {
        $this->productService = $productService;
        $this->apiResponse = $apiResponse;
    }

    /**
     * List product
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        $params = $request->all();
        return Cafe24ApiService::get(config('aos.mall_id'), $request->bearerToken(), 'products', $params);
    }

    /**
     * Get a product detail
     * @param int $productNo
     * @param Request $request
     */
    public function detail($productNo, Request $request)
    {
        $params = $request->all();
        $checkExitst = Mapp::where(['type' => 'product', 'doosoun_id' => @$productNo])->first();
        if (empty($checkExitst)) {
            return $this->apiResponse->ok(false, [], ['message' => 'Product is not exist']);
        }
        $result = Cafe24ApiService::get(config('aos.mall_id'), $request->bearerToken(), 'products/' . $checkExitst->cafe24_id, $params);
        return $this->apiResponse->ok(true, $result);
    }

    /**
     * Get list id doosoun
     * @param int $productNo
     */
    public function listId()
    {
        return Mapp::select('doosoun_id')->pluck('doosoun_id');
    }



    /**
     * create a product
     * @param Request $request
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'request.product_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->validationError(false, [], $validator->errors());
        }
        // check exitst doosoun product id
        $params = $request->all();
        $checkExitst = Mapp::where(['type' => 'product', 'doosoun_id' => @$params['request']['product_id']])->first();
        if (!empty($checkExitst)) {
            return $this->apiResponse->ok(false, [], ['message' => 'Product is exist']);
        }
        $productResponse = $this->productService->saveProduct($request);
        return $this->apiResponse->ok(true, $productResponse);
    }

    /**
     * update a product
     *
     * @param int $productId
     * @param Request $request
     * @return void
     */
    public function update($productId, Request $request)
    {
        $checkExitst = Mapp::where(['type' => 'product', 'doosoun_id' => @$productId])->first();
        if (empty($checkExitst)) {
            return $this->apiResponse->ok(false, [], ['message' => 'Product is not exitst']);
        }
        $productResponse = $this->productService->saveProduct($request, $checkExitst->cafe24_id);
        return $this->apiResponse->ok(true, $productResponse);
    }

    /**
     * Delete a product
     * @param int productNo
     *
     * @return response
     */
    public function delete($productNo, Request $request)
    {
        $checkExitst = Mapp::where(['type' => 'product', 'doosoun_id' => $productNo])->first();
        if (empty($checkExitst)) {
            return [
                'success' => false,
                'message' => 'Product not exitst',
            ];
        }
        $result = Cafe24ApiService::delete(config('aos.mall_id'), $request->bearerToken(), 'products/' . $checkExitst->cafe24_id);
        Mapp::where(['type' => 'product', 'doosoun_id' => $productNo])->delete();
        MappOption::where([
            'map_id' => $checkExitst->id,
        ])->delete();
        Log::channel('api')->info('Deleted product: ' . jEncode($result));
        return [
            'success' => true,
        ];
    }
}
