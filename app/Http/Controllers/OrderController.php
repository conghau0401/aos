<?php

namespace App\Http\Controllers;

use App\Facades\AOSAPI;
use App\Facades\Cafe24ApiService;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Mall;
use App\Models\Mapp;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    protected $token;
    protected $apiResponse;
    protected $orderService;
    protected $cartService;

    public function __construct(
        \App\Contracts\ApiResponseInterface $apiResponse,
        \App\Services\OrderService $orderService,
        \App\Services\CartService $cartService
    ) {
        $this->token = request()->bearerToken();
        $this->apiResponse = $apiResponse;
        $this->orderService = $orderService;
        $this->cartService = $cartService;
    }

    /**
     * List order
     *
     * @param Request $request
     * @return void
     */
    public function list(Request $request)
    {
        $user = User::where('access_token', $this->token)->first();
        $fromDate = $request->fromDate ?? date('Y-m-d');
        $endDate = $request->endDate ?? date('Y-m-d');
        $params = [
            'fromDate' => $fromDate,
            'endDate' => $endDate,
            'storeIds' => [$user->store_id],
        ];
        if ($request->has('mallCodes')) {
            $params['mallCodes'] = $request->mallCodes;
        }
        if ($request->has('orderType')) {
            $params['orderType'] = $request->orderType;
        }
        if ($request->has('shippingMethod')) {
            $params['shippingMethod'] = $request->shippingMethod;
        }
        $response = AOSAPI::post($this->token, 'ecommerce/orders', $params);
        return $response;
    }

    /**
     * Detail order
     *
     * @param int $idOrder
     * @return void
     */
    public function detail($idOrder)
    {
        $response = AOSAPI::get($this->token, 'ecommerce/orders/' . $idOrder);
        return $response;
    }

    /**
     * Create new order
     *
     * @param Request $request
     * @return void
     */
    public function create(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'receiver_name' => 'required',
            'receiver_zipcode' => 'required',
            'receiver_address1' => 'required',
            'receiver_address2' => 'required',
            'receiver_phone' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse->validationError(false, $validator->errors());
        }

        // check user
        $user = User::where('access_token', $this->token)->first();
        if (empty($user)) {
            return $this->apiResponse->notFound(false, [], ['message' => 'User not exitst']);
        }
        // get user
        $response = AOSAPI::get($user->access_token, 'user/user/' . $user->doosoun_id);
        $userDetail = $response->data;
        Log::channel('order')->info('Step1 - get user');
        // get store
        $response = AOSAPI::get($user->access_token, 'user/store/' . $user->store_id);
        $store = $response->data;
        Log::channel('order')->info('Step2 - get store');
        // get margin rate, shipping rate
        $marginRates = $this->cartService->getMarginRate($user, $store);
        Log::channel('order')->info('Step2.1 - get marginrate');

        // check have checkout type => add condition
        if (!empty($request->order_type)) {
            $condition = ['store_id' => $user->store_id];
            if ($request->order_type == 'pickup') {
                $condition['shipping_method'] = 1;
            } else if ($request->order_type == 'delivery') {
                $condition['shipping_method'] = 2;
            } else if ($request->order_type == 'sameDay') {
                $condition['shipping_method'] = 3;
            }
            $carts = Cart::where($condition)->get();
        } else { // get all shipping method
            $carts = Cart::where(['store_id' => $user->store_id])->get();
        }
        if ($carts->count() == 0) {
            return $this->apiResponse->ok(false, [], ['message' => 'Cart empty']);
        }

        $cartsByShippingMethod = $carts->groupBy('shipping_method');

        $result = ['success' => [], 'fail' => []];
        // get mall
        $mall = Mall::where('mall_id', config('aos.mall_id'))->first();
        foreach ($cartsByShippingMethod as $shippingMethod => $groupProducts) {
            Log::channel('order')->info('Start create order by shipping method: ' . $shippingMethod);
            // unique product in cart
            $groupProducts = json_decode((string)jEncode($groupProducts), true);
            $groupProducts = array_filter($groupProducts, 'uniqueItemCart');
            // convert to object again
            $groupProducts = json_decode((string)jEncode($groupProducts));
            $orderResult = $this->createOrderByShippingMethod($shippingMethod, $request, $groupProducts, $mall, $user, $userDetail, $store, $marginRates);
            if (isset($orderResult['status']) && $orderResult['status'] == true) {
                $result['success'][]['data'] = $orderResult['data'];
            } else {
                $result['fail'][]['data'] = $orderResult['data'];
                $result['fail'][]['message'] = $orderResult['message'];
            }
        }
        Log::channel('order')->info('Result create order: ' . jEncode($result));
        $orderNumber = '';
        if (!empty($result['success'])) {
            foreach ($result['success'] as $orderSuccess) {
                $orderNumber .= $orderSuccess['data']->data[0]->mallOrderId . ',';
            }
            $dataReturn = [
                'order_numbers' => trim($orderNumber, ','),
                'created_date' => microtime(true) * 1000,
            ];
            Log::channel('order')->info('dataReturn: ' . jEncode($dataReturn));
            return $this->apiResponse->ok(true, $dataReturn);
        }

        return $this->apiResponse->ok(false, $result);
    }

    // cancel order
    public function cancel(Request $request)
    {
        $params = $request->all();
        $response = AOSAPI::post($this->token, 'ecommerce/orders/cf24', $params);
        Log::channel('order')->info('Cancel order: ' . jEncode($params) . jEncode($response));
        return $response;
    }

    // cancel orders
    public function cancelByIds(Request $request)
    {
        $params = [
            'orderIds' => $request->orderIds,
        ];
        $response = AOSAPI::post($this->token, 'ecommerce/orders/cf24-cancel', $params);
        Log::channel('order')->info('Cancel order by ids: ' . jEncode($params) . jEncode($response));
        return $response;
    }

    // exchange request
    public function exchange()
    {
        $data = '{
            "shop_no": 1,
            "request": {
                "status": "accept",
                "recover_inventory": "T",
                "add_memo_too": "F",
                "items": [
                    {
                        "order_item_code": "20220531-0000022-01",
                        "quantity": 1
                    },
                    {
                        "order_item_code": "20220531-0000022-02",
                        "quantity": 1
                    }
                ],
                "same_product": "T"
            }
        }';
        $mall = Mall::where('mall_id', config('aos.mall_id'))->first();

        $result = Cafe24ApiService::post($mall->mall_id, $mall->access_token, 'orders/20220531-0000022/exchange', json_decode($data));
        return $result;
    }

    // return request
    public function returnRequest()
    {
        $data = '{
            "shop_no": 1,
            "requests": [
                {
                    "order_id": "20220530-0000028",
                    "items": [
                        {
                            "order_item_code": "20220530-0000028-01",
                            "quantity": 1
                        }
                    ],
                    "request_pickup": "F",
                    "pickup": null,
                    "tracking_no": "000cafe24",
                    "shipping_company_name": "Doosoun Express",
                    "reason_type": "I",
                    "reason": "A: Change of mind | E: Unsatisfactory product | K: Defective product | J: Shipping error | I: Others"
                }
            ]
        }';
        $mall = Mall::where('mall_id', config('aos.mall_id'))->first();

        $result = Cafe24ApiService::post($mall->mall_id, $mall->access_token, 'returnrequests', json_decode($data));
        return $result;
    }

    // return
    public function return()
    {
        $data = '{
            "shop_no": 1,
            "request": {
                "status": "returned",
                "reason": "Missing component",
                "claim_reason_type": "I",
                "add_memo_too": "T",
                "recover_inventory": "T",
                "refund_method_code": "G",
                "combined_refund_method": null,
                "items": [
                    {
                        "order_item_code": "20220530-0000028-01",
                        "quantity": 1
                    }
                ]
            }
        }';
        $mall = Mall::where('mall_id', config('aos.mall_id'))->first();

        $result = Cafe24ApiService::post($mall->mall_id, $mall->access_token, 'orders/20220530-0000028/return', json_decode($data));
        return $result;
    }

    /**
     * createOrderByShippingMethod function
     *
     * @param string $shippingMethod
     * @param Request $request
     * @param object $groupProducts
     * @param object $mall
     * @param object $user
     * @param object $userDetail
     * @param object $store
     * @param object $marginRates
     * @return void
     */
    private function createOrderByShippingMethod($shippingMethod, $request, $groupProducts, $mall, $user, $userDetail, $store, $marginRates)
    {
        // get product cafe24 and doosoun
        $productIdsCafe24 = [];
        $productIdsDoosoun = [];
        $productIdsDoosounMapped = [];

        foreach ($groupProducts as $cartItem) {
            array_push($productIdsDoosoun, $cartItem->ds_product_id);
        }
        $mappings = Mapp::whereIn('doosoun_id', $productIdsDoosoun)->get();
        foreach ($mappings as $mapp) {
            array_push($productIdsCafe24, $mapp->cafe24_id);
            array_push($productIdsDoosounMapped, $mapp->doosoun_id);
        }

        // get products doosoun
        $productsDoosoun = AOSAPI::post($this->token, 'product/cf24-product', ['product_ids' => $productIdsDoosoun]);

        // get products cafe24
        $collectionProduct = collect($productIdsCafe24);
        $chunksProduct = $collectionProduct->chunk(100)->toArray();
        $productsCafe24 = [];
        foreach ($chunksProduct as $chunk) {
            $paramsCafe24 = [
                'product_no' => implode(',', $chunk),
                'custom_product_code' => config('cafe24.random_string'),
                'embed' => 'variants',
                'limit' => 100
            ];
            $result = Cafe24ApiService::get($mall->mall_id, $mall->access_token, 'products', $paramsCafe24);
            foreach ($result->products as $product) {
                $productsCafe24[] = $product;
            }
        }

        if ($productsDoosoun->count != count($productsCafe24)) {
            $proName = '';
            foreach ($productsDoosoun->data as $proDoosoun) {
                if (!in_array($proDoosoun->productId, $productIdsDoosounMapped)) {
                    $proName = $proDoosoun->productNm;
                }
            }
            return [
                'status' => false,
                'data' => [$productIdsCafe24, $productsDoosoun->data],
                'message' => '주문 불가 상품 ' . $proName,
            ];
        }

        // check available order
        foreach ($groupProducts as $cart) {
            foreach ($productsDoosoun->data as $productAOS) {
                if ($cart->ds_product_id == $productAOS->productId) {
                    foreach ($productAOS->options as $option) {
                        if ($option->option_sell_yn == true || ($option->limited == true && $option->stockQty < $cart->quantity)) {
                            return [
                                'status' => false,
                                'data' => [],
                                'message' => '품절 ' . $productAOS->productNm,
                            ];
                        }
                    }
                }
            }
        }

        // create order cafe24 mall
        $orderCafe24 = $this->orderService->createOrderCafe24($mall, $request, $user, $userDetail, $store, $groupProducts, $productsCafe24, $productsDoosoun->data, $mappings, $marginRates);
        Log::channel('order')->info('Step4 - Create order on cafe24' . jEncode($orderCafe24));
        if (!isset($orderCafe24->orders) || empty($orderCafe24->orders)) {
            return [
                'status' => false,
                'data' => [],
                'message' => 'Error create order',
            ];
        }

        // create order doosoun
        $orderDoosoun = $this->orderService->createOrderDoosoun($mall, $request, $user, $userDetail, $store, $groupProducts, $orderCafe24, $productsDoosoun->data, $marginRates);
        Log::channel('order')->info('Step5 - Create order on doosoun' . jEncode($orderDoosoun));
        if (!isset($orderDoosoun->status) && $orderDoosoun->status != '201') {
            return [
                'status' => false,
                'data' => [],
                'message' => 'Error create order doosoun',
            ];
        }
        // delete cart if success
        Cart::where([
            'store_id' => $user->store_id,
            'shipping_method' => $shippingMethod,
        ])->delete();

        // create history order
        $this->orderService->saveOrderResult($orderDoosoun->data[0]);

        return [
            'status' => true,
            'data' => $orderDoosoun,
            'message' => 'Error create order doosoun',
        ];
    }

    public function getPaymentInfo(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse->ok(false, $validator->errors());
        }
        $hashString = bin2hex(hash('sha256', date('YmdHis') . config('aos.payment.mid') . $request->amount . config('aos.payment.merchant_key'), true));
        $result = [
            'name' => 'Doosoun AOS',
            'amount' => $request->amount,
            'mid' => config('aos.payment.mid'),
            'moid' => Str::uuid(),
            'date' => date('YmdHis'),
            'vitural_date' => date('Ymd', strtotime('+ 3 days')),
            'hash' => $hashString,
        ];
        return $this->apiResponse->ok(true, $result);
    }

    public function paymentResult(Request $request)
    {
        Log::info('Payment: ' . jEncode($request->all()));
    }
}
