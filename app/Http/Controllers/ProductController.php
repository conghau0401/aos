<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facades\AOSAPI;
use App\Facades\Cafe24ApiService;
use App\Models\Mall;
use App\Models\Mapp;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    protected $token;
    protected $apiResponse;

    public function __construct(\App\Contracts\ApiResponseInterface $apiResponse)
    {
        $this->token = request()->bearerToken();
        $this->apiResponse = $apiResponse;
    }

    /**
     * Get list product
     *
     * @param Request $request
     * @return object
     */
    public function list(Request $request)
    {
        // validation
        // $validator = Validator::make($request->all(), [
        //     'category_id' => 'required_without:product_ids',
        //     'product_ids' => 'required_without:category_id',
        // ]);

        // if ($validator->fails()) {
        //     return $this->apiResponse->validationError(false, $validator->errors());
        // }
        $params = [
            'pageIndex' => $request->page ?? 1,
            'pageSize' => config('aos.page_size'),
        ];
        if ($request->product_ids) {
            $params['product_ids'] = explode(',', $request->product_ids);
        }
        if ($request->category_id) {
            $params['category_id'] = $request->category_id;
        }
        if ($request->productNames) {
            $params['keySearch'] = $request->productNames;
        }

        $response = AOSAPI::post($this->token, 'product/cf24-product', $params);
        if (empty($response->data)) {
            return $response;
        }
        // $ids = [];
        // foreach ($response->data as &$product) {
        //     $ids[] = $product->productId;
        //     $product->price = 0;
        //     $product->discount_price = 0;
        //     if (!empty($product->options)) {
        //         $product->price = $product->options[0]->supplyUnitPrice;
        //         $product->discount_price = $product->options[0]->discount_price;
        //     }
        // }

        // $dataProducts = [];
        // $filterIds = Mapp::whereIn('doosoun_id', $ids)->pluck('doosoun_id')->toArray();
        // foreach ($response->data as &$product) {
        //     if (in_array($product->productId, $filterIds)) {
        //         $dataProducts[] = $product;
        //     }
        // }
        // $response->data = $dataProducts;

        return $response;
    }

    /**
     * Get product detail
     *
     * @param Request $request
     * @return object
     */
    public function detail($id, Request $request)
    {
        $response = AOSAPI::get($this->token, 'product/cf24-product/' . $id, $request->all());
        if (!empty($response->data)) {
            $response->data->additionalimages = null;
            $mapping = Mapp::where([
                'type' => 'product',
                'doosoun_id' => $id,
            ])->first();
            if (!empty($mapping)) {
                $mall = Mall::where('mall_id', config('aos.mall_id'))->first();
                $cafe24Product = Cafe24ApiService::get($mall->mall_id, $mall->access_token, 'products/' . $mapping->cafe24_id, ['embed' => 'additionalimages']);
                Log::channel('api_front')->info('Get product detail cafe24: ' . json_encode($cafe24Product));
                if (isset($cafe24Product->product)) {
                    $response->data->additionalimages = $cafe24Product->product->additionalimages;
                }
            }
        }
        return $response;
    }

    /**
     * Get list product discount
     *
     * @param Request $request
     * @return object
     */
    public function discount(Request $request)
    {
        $params = $request->all();
        return AOSAPI::get($this->token, 'product/cf24-product/discount', $params);
    }

    /**
     * Get list store size
     *
     * @param Request $request
     * @return object
     */
    public function bundleStoreSize(Request $request)
    {
        $params = $request->all();
        $params['useIndex'] = 1;
        return AOSAPI::post($this->token, 'product/bundle-storesize', $params);
    }

    /**
     * Get store size detail
     *
     * @param Request $request
     * @return object
     */
    public function bundleStoreSizeDetail($id, Request $request)
    {
        $params = $request->all();
        $response = AOSAPI::get($this->token, 'product/bundle-storesize/' . $id, $params);
        $dataProducts = $response->data->details ?? [];
        $response->countProducts = count($dataProducts);
        // rename property
        $dataProducts = array_map(function ($data) {
            $ojb = $data;
            $ojb->quantity = $data->bundleStoresizeQt;
            return $ojb;
        }, $dataProducts);

        $result = $this->groupByProduct($dataProducts, 'store_size');
        $response->data = $result;
        return $response;
    }

    /**
     * Get list events
     *
     * @param Request $request
     * @return object
     */
    public function listEvents(Request $request)
    {
        $params = $request->all();
        $params['use'] = 1;
        $response = AOSAPI::post($this->token, 'product/recommended-products-by-event/', $params);
        if (!empty($response->data)) {
            $result = [];
            // limit event happening
            foreach ($response->data as $event) {
                if ($event->startDate < time() * 1000 && $event->endDate > time() * 1000) {
                    $result[] = $event;
                }
            }
            $response->data = $result;
            $response->count = count($result);
        }
        return $response;
    }

    /**
     * recommended products by event
     *
     * @param Request $request
     * @return object
     */
    public function recommendedProductByEvent(Request $request)
    {
        $params = [
            'bundleAnniversaryId' => $request->bundleAnniversaryId,
        ];
        $response = AOSAPI::post($this->token, 'product/cf24-product/event', $params);
        $dataProducts = $response->data->selected ?? [];

        $result = $this->groupByProduct($dataProducts, 'plan');
        $response->data = $result;
        return $response;
    }

    /**
     * list deal
     *
     * @param Request $request
     * @return object
     */
    public function listDeal(Request $request)
    {
        $params = [
            'use' => true,
        ];
        $response = AOSAPI::post($this->token, 'account/product-deal', $params);
        return $response;
    }

    /**
     * list product by deal
     *
     * @param int $idDeal
     * @return object
     */
    public function detailDeal($idDeal)
    {
        $response = AOSAPI::get($this->token, 'account/product-deal/' . $idDeal);
        $dataProducts = $response->data->productDealDets ?? [];
        // rename property
        $dataProducts = array_map(function ($data) {
            $ojb = $data;
            $ojb->productNm = $data->productName;
            $ojb->optionNm = $data->optionName;
            $ojb->quantity = $data->productDealQuantity;
            unset($ojb->productName);
            unset($ojb->optionName);
            return $ojb;
        }, $dataProducts);

        $result = $this->groupByProduct($dataProducts, 'discount');

        $response->data = $result;
        return $response;
    }

    /**
     * List product wishlist of store
     *
     * @return void
     */
    public function wishlist()
    {
        $user = User::where('access_token', $this->token)->first();
        $response = AOSAPI::get($this->token, 'product/store-interest/' . $user->store_id);
        if (!isset($response->data->selected)) {
            return $this->apiResponse->ok(false, []);
        }
        return $this->apiResponse->ok(true, $response->data->selected);
    }

    /**
     * Add products wishlist of store
     *
     * @return void
     */
    public function addToWishlist(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'productIds' => 'array',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->ok(false, $validator->errors());
        }
        $user = User::where('access_token', $this->token)->first();
        $params = [
            'storeId' => $user->store_id,
            'productIds' => $request->productIds,
            'productDeleteIds' => $request->productDeleteIds,
        ];
        $response = AOSAPI::put($this->token, 'product/store-interest', $params);
        Log::info('Add to wishlist: ' . jEncode($params) . jEncode($response));
        if ($response->status != 201) {
            return $response;
        }
        return $this->apiResponse->ok(true, $response->data->selected);
    }


    /**
     * List product newest of store
     *
     * @return void
     */
    public function newest()
    {
        return AOSAPI::get($this->token, 'product/cf24-product/newest');
    }

    /**
     * List product best of store
     *
     * @return void
     */
    public function best($number, Request $request)
    {
        $params = [];
        if (!empty($request->category_id)) {
            $params['categoryId'] = $request->category_id;
        }
        return AOSAPI::get($this->token, 'product/cf24-product/best/' . $number, $params);
    }

    /**
     * Add product to regular delivery
     * @param Request $request
     *
     * @return void
     */
    public function regularDelivery(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'regularDeliveryProductAdd.*.productId' => 'required',
            'regularDeliveryProductAdd.*.quantityPerPack' => 'required',
            'regularDeliveryProductAdd.*.quantity' => 'required',
            'regularDeliveryProductAdd.*.optionId' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->validationError(false, $validator->errors());
        }
        $params = [
            'regularDeliveryProductAdd' => $request->regularDeliveryProductAdd,
            'regularDeliveryProductDelete' => $request->regularDeliveryProductDelete ?? [],
        ];
        $user = User::where('access_token', $this->token)->first();
        return AOSAPI::put($this->token, 'ecommerce/regular-delivery-product/' . $user->store_id, $params);
    }

    /**
     * get List Regular
     *
     * @return void
     */
    public function getListRegular()
    {
        $user = User::where('access_token', $this->token)->first();
        return AOSAPI::post($this->token, 'ecommerce/regular-delivery-product/detail', ['storeId' => $user->store_id]);
    }



    /**
     * Add product to regular delivery
     * @param Request $request
     *
     * @return void
     */
    public function monthlyHistory(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'startDate' => 'required',
            'endDate' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->validationError(false, $validator->errors());
        }
        $user = User::where('access_token', $this->token)->first();
        $params = [
            'startDate' => str_replace('-', '', $request->startDate),
            'endDate' => str_replace('-', '', $request->endDate),
            'storeId' => $user->store_id,
        ];
        return AOSAPI::post($this->token, 'product/cf24-product/monthly-history', $params);
    }

    /**
     * Search product by barcode
     * @param Request $request
     *
     * @return void
     */
    public function barcode(Request $request)
    {
        return AOSAPI::get($this->token, 'product/product/product-option-list-by-barcode', ['barcode' => $request->barcode]);
    }

    /**
     * Group by product by type
     *
     * @param array $dataProducts
     * @param string $type
     * @return array
     */
    private function groupByProduct($dataProducts, $type = 'plan')
    {
        $result = [];
        $collection = collect($dataProducts);
        $groupbyType = $collection->sortBy('productTypeCode')->groupBy('productTypeCode');
        foreach ($groupbyType as $key => &$group) {
            $result[(int)$key] = $group->groupBy('productId')->toArray();
        }

        foreach ($result as &$groupType) {
            $listIdsProduct = [];
            foreach ($groupType as $keyPro => &$groupPro) {
                $i = 0;
                foreach ($dataProducts as &$v) {
                    if ($keyPro == $v->productId) {
                        if ($i == 0) {
                            $groupPro['productNm'] = $v->productNm;
                            $groupPro['productId'] = $v->productId;
                            $groupPro['image'] = $v->imageProduct;
                            $groupPro['marketableSize'] = $v->marketableSize;
                            $groupPro['price'] = $v->price;
                            $groupPro['quantity'] = $v->quantity;
                            $groupPro['productTp'] = $v->productTypeCode;
                            $groupType['productType'] = $v->productTypeName;
                            $groupType['productTypeCode'] = $v->productTypeCode;
                            if (isset($v->discount_price)) {
                                $groupPro['discount_price'] = $v->discount_price;
                            } elseif (isset($v->discountPrice)) {
                                $groupPro['discount_price'] = $v->discountPrice;
                            }
                            switch ($groupType['productTypeCode']) {
                                case 1:
                                    $groupType['icon'] = config('aos.product_type.industrial.icon');
                                    $groupType['note'] = config('aos.product_type.industrial.note');
                                    break;
                                case 2:
                                    $groupType['icon'] = config('aos.product_type.miscellaneous.icon');
                                    $groupType['note'] = config('aos.product_type.miscellaneous.note');
                                    break;
                                case 3:
                                    $groupType['icon'] = config('aos.product_type.alcoholic.icon');
                                    $groupType['note'] = config('aos.product_type.alcoholic.note');
                                    break;
                                default:
                                    $groupType['icon'] = '';
                                    $groupType['note'] = '';
                                    break;
                            }
                        }
                        $groupPro['options'][] = $v;
                        // unique product
                        if (!in_array($groupPro['productId'], $listIdsProduct)) {
                            $groupType['products'][] = &$groupPro;
                            array_push($listIdsProduct, $groupPro['productId']);
                        }
                        unset($groupPro[$i]);
                        unset($groupType[$keyPro]);
                        $i++;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Any path with get method
     *
     * @param string $any
     * @param Request $request
     */
    public function anyGet($any, Request $request)
    {
        return AOSAPI::get($this->token, $any, $request->all());
    }

    /**
     * Any path with post method
     *
     * @param string $any
     * @param Request $request
     */
    public function anyPost($any, Request $request)
    {
        return AOSAPI::post($this->token, $any, $request->all());
    }
}
