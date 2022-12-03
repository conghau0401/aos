<?php

namespace App\Http\Controllers\Api;

use App\Facades\Cafe24ApiService;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class APIOrderController extends Controller
{
    protected $token;
    protected $apiResponse;

    public function __construct(
        \App\Contracts\ApiResponseInterface $apiResponse
    ) {
        $this->token = request()->bearerToken();
        $this->apiResponse = $apiResponse;
    }

    /**
     * Update order status function
     *
     * @param Request $request
     * @return void
     */
    public function updateOrderStatus(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'request.order_id' => 'required',
            'request.order_item_code' => 'required|array',
            'request.status' => [
                'required',
                Rule::in(['prepareproduct', 'preparing', 'shipping', 'shipped']),
            ],
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->validationError(false, $validator->errors(), ['error' => "Validation error"]);
        }

        $params = $request->all();
        $params = $params['request'];
        // Get shipping code from order item code
        $shipments = Cafe24ApiService::get($request->mall_id, $this->token, 'orders/' . $params['order_id'] . '/shipments');

        $shippingCodes = [];
        foreach ($shipments->shipments as $shipment) {
            foreach ($shipment->items as $item) {
                if (in_array($item->order_item_code, $params['order_item_code'])) {
                    $shippingCodes[$shipment->tracking_no] = $shipment->shipping_code;
                }
            }
        }

        // return Cafe24ApiService::get($request->mall_id, $this->token, 'orders/' . $params['order_id'] . '/items');

        // find company code
        // return Cafe24ApiService::get($request->mall_id, $this->token, 'carriers');
        $updateSuccess = [];
        $updateFalse = [];

        switch ($params['status']) {
            case 'prepareproduct':
                if (empty($shippingCodes)) {
                    return $this->apiResponse->ok(true, [], ['message' => 'Order ready on prepareproduct']);
                }
                // update from preparing => prepareproduct
                $data = [
                    "shop_no" => $request->cafe24_shop_no,
                    "request" => [
                        "process_status" => "prepareproduct",
                        "order_item_code" => $params['order_item_code'],
                    ]
                ];
                $response = Cafe24ApiService::put($request->mall_id, $this->token, 'orders/' . $params['order_id'], $data);
                if (isset($response->error)) {
                    $updateFalse[$params['order_id']] = $response->error;
                } else {
                    $updateSuccess[$params['order_id']] = 'prepareproduct';
                }
                return $this->apiResponse->ok(true, ['success' => $updateSuccess, 'false' => $updateFalse]);
                break;
            case 'preparing':
                // update from prepareproduct => preparing
                if (empty($shippingCodes)) {
                    $data = [
                        "shop_no" => $request->cafe24_shop_no,
                        "request" => [
                            "process_status" => "prepare",
                            "order_item_code" => $params['order_item_code'],
                        ]
                    ];
                    $response = Cafe24ApiService::put($request->mall_id, $this->token, 'orders/' . $params['order_id'], $data);
                    if (isset($response->error)) {
                        $updateFalse[$params['order_id']] = $response->error;
                    } else {
                        $updateSuccess[$params['order_id']] = 'preparing';
                    }
                    return $this->apiResponse->ok(true, ['success' => $updateSuccess, 'false' => $updateFalse]);
                }
                // update from shipping => preparing
                foreach ($shippingCodes as $tracking => $shippingCode) {
                    $response = Cafe24ApiService::delete($request->mall_id, $this->token, 'orders/' . $params['order_id'] . '/shipments/' . $shippingCode);
                    Log::channel('api_order')->info('Shipment update - shipping => preparing' . jEncode($response));
                    if (isset($response->error)) {
                        $updateFalse[$tracking] = $response->error;
                    } else {
                        $updateSuccess[$tracking] = 'preparing';
                    }
                }
                break;
            case 'shipping':
                $data = [
                    "shop_no" => $request->cafe24_shop_no,
                    "request" => [
                        "tracking_no" => @$params['tracking_no'],
                        "status" => @$params['status'],
                        "order_item_code" => $params['order_item_code'],
                        "carrier_id" => config('aos.order.carrier_id'), // Self delivery
                        "shipping_company_code" => config('aos.order.shipping_company_code'),
                    ]
                ];
                $response = Cafe24ApiService::post($request->mall_id, $this->token, 'orders/' . $params['order_id'] . '/shipments', $data);
                Log::channel('api_order')->info('Shipment update - preparing => shipping' . jEncode($response));
                if (isset($response->error)) {
                    $updateFalse[$params['tracking_no']] = $response->error;
                } else {
                    $updateSuccess[$params['tracking_no']] = 'shipping';
                }
                break;
            case 'shipped':
                if (empty($shippingCodes)) {
                    return $this->apiResponse->error(false, [], ['message' => 'Shipping code is not exitst']);
                }
                foreach ($shippingCodes as $tracking => $shippingCode) {
                    $data = [
                        "shop_no" => $request->cafe24_shop_no,
                        "request" => [
                            "status" => @$params['status'],
                        ]
                    ];
                    $response = Cafe24ApiService::put($request->mall_id, $this->token, 'orders/' . $params['order_id'] . '/shipments/' . $shippingCode, $data);
                    Log::channel('api_order')->info('Shipment update - shipping => shipped' . jEncode($response));
                    if (isset($response->error)) {
                        $updateFalse[$tracking] = $response->error;
                    } else {
                        $updateSuccess[$tracking] = 'shipped';
                    }
                }
                break;
            default:
                # code...
                break;
        }
        return $this->apiResponse->ok(true, ['success' => $updateSuccess, 'false' => $updateFalse]);

        $shipments = Cafe24ApiService::get($request->mall_id, $this->token, 'orders/' . $params['order_id'] . '/shipments');
        // return $shipments;

        $shippingCodes = [];
        foreach ($shipments->shipments as $shipment) {
            foreach ($shipment->items as $item) {
                if (in_array($item->order_item_code, $params['order_item_code'])) {
                    $shippingCodes[$shipment->tracking_no] = $shipment->shipping_code;
                }
            }
        }

        // delete order shipment where status = preparing
        if ($params['status'] == 'preparing') {
            foreach ($shippingCodes as $shippingCode) {
                $response = Cafe24ApiService::delete($request->mall_id, $this->token, 'orders/' . $params['order_id'] . '/shipments/' . $shippingCode);
                Log::channel('api_order')->info('Shipment update - preparing' . jEncode($response));
                if (isset($response->error)) {
                    return $this->apiResponse->error(false, [], ['error' => $response->error]);
                }
            }
            return $this->apiResponse->ok(true, $response);
        }

        // create shipment
        if (@$params['tracking_no'] && @$params['status'] == 'shipping') { // create order shipment
            $data = [
                "shop_no" => $request->cafe24_shop_no,
                "request" => [
                    "tracking_no" => @$params['tracking_no'],
                    "status" => @$params['status'],
                    "order_item_code" => $params['order_item_code'],
                    "carrier_id" => config('aos.order.carrier_id'), // Self delivery
                    "shipping_company_code" => config('aos.order.shipping_company_code'),
                ]
            ];
            $response = Cafe24ApiService::post($request->mall_id, $this->token, 'orders/' . $params['order_id'] . '/shipments', $data);
            if (isset($response->error)) {
                return $this->apiResponse->error(false, [], ['error' => $response->error]);
            }
            return $this->apiResponse->ok(true, $response);
        } else { // update status order shipment
            foreach ($shippingCodes as $shippingCode) {
                $data = [
                    "shop_no" => $request->cafe24_shop_no,
                    "request" => [
                        "status" => @$params['status'],
                    ]
                ];
                Cafe24ApiService::put($request->mall_id, $this->token, 'orders/' . $params['order_id'] . '/shipments/' . $shippingCode, $data);
            }
            return $this->apiResponse->ok(true, []);
        }
    }

    /**
     * Request cancellation order
     *
     * @param Request $request
     * @return void
     */
    public function cancellationRequest(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'request.order_id' => 'required',
            'request.items' => 'required|array',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->validationError(false, $validator->errors(), ['error' => "Validation error"]);
        }
        $params = $request->all();
        $params = $params['request'];

        $data = [
            "shop_no" => $request->cafe24_shop_no,
            "requests" => [
                [
                    "order_id" => $params['order_id'],
                    "reason_type" => "I",
                    "reason" => $params['reason'],
                    "items" => $params['items'],
                ]
            ]
        ];
        $response = Cafe24ApiService::post($request->mall_id, $this->token, 'cancellationrequests', $data);
        if (isset($response->errors)) {
            return $this->apiResponse->error(false, [], ['error' => $response->errors]);
        }
        return $this->apiResponse->ok(true, $response);
    }

    /**
     * Request cancellation order
     *
     * @param Request $request
     * @return void
     */
    public function cancellation(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'request.order_id' => 'required',
            'request.items' => 'required|array',
        ]);

        // $params = [
        //     'start_date' => '2022-05-01',
        //     'end_date' => '2022-07-01',
        //     'order_status' => 'R40',
        // ];
        // return Cafe24ApiService::get($request->mall_id, $this->token, 'orders', $params);

        if ($validator->fails()) {
            return $this->apiResponse->validationError(false, $validator->errors(), ['error' => "Validation error"]);
        }
        $params = $request->all();
        $params = $params['request'];

        $data = [
            "shop_no" => $request->cafe24_shop_no,
            "requests" => [
                [
                    "order_id" => $params['order_id'],
                    "status" => "canceled",
                    "reason" => $params['reason'],
                    "claim_reason_type" => "I",
                    "refund_method_code" => "T",
                    "refund_bank_code" => "bank_m",
                    "refund_bank_name" => "AOS Doosun",
                    "refund_bank_account_no" => "000000111111",
                    "refund_bank_account_holder" => "AOS Doosun",
                    "items" => $params['items'],
                ]
            ]
        ];
        $response = Cafe24ApiService::post($request->mall_id, $this->token, 'cancellation', $data);
        if (isset($response->errors)) {
            return $this->apiResponse->error(false, [], ['error' => $response->errors]);
        }
        return $this->apiResponse->ok(true, $response);
    }

    /**
     *  Exchange order
     *
     * @param Request $request
     * @return void
     */
    public function exchange(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'request.order_id' => 'required',
            'request.items' => 'required|array',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->validationError(false, $validator->errors(), ['error' => "Validation error"]);
        }
        $params = $request->all();
        $params = $params['request'];

        $data = [
            'shop_no' => 1,
            'request' => [
                'status' => $params['status'],
                'items' => $params['items'],
                'same_product' => 'T'
            ]
        ];

        $response = Cafe24ApiService::post($request->mall_id, $this->token, 'orders/' . $params['order_id'] . '/exchange', $data);

        if (isset($response->error)) {
            return $this->apiResponse->error(false, [], ['error' => $response->error]);
        }
        return $this->apiResponse->ok(true, $response);
    }

    /**
     *  Return order
     *
     * @param Request $request
     * @return void
     */
    public function return(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'request.order_id' => 'required',
            'request.reason' => 'required',
            'request.items' => 'required|array',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->validationError(false, $validator->errors(), ['error' => "Validation error"]);
        }
        $params = $request->all();
        $params = $params['request'];

        $data = [
            'shop_no' => 1,
            'request' => [
                "status" => "returned",
                "reason" => $params['reason'],
                'items' => $params['items'],
                "claim_reason_type" => "I",
                "refund_method_code" => "T",
                "refund_bank_code" => "bank_m",
                "refund_bank_name" => "AOS Doosun",
                "refund_bank_account_no" => "000000111111",
                "refund_bank_account_holder" => "AOS Doosun",
            ]
        ];

        $response = Cafe24ApiService::post($request->mall_id, $this->token, 'orders/' . $params['order_id'] . '/return', $data);

        if (isset($response->error)) {
            return $this->apiResponse->error(false, [], ['error' => $response->error]);
        }
        return $this->apiResponse->ok(true, $response);
    }

    /**
     *  update return order
     *
     * @param Request $request
     * @return void
     */
    public function returnUpdate(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'request.order_id' => 'required',
            'request.items' => 'required|array',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->validationError(false, $validator->errors(), ['error' => "Validation error"]);
        }
        $params = $request->all();
        $params = $params['request'];

        $data = [
            'shop_no' => 1,
            'request' => [
                "status" => "collected",
                // 'items' => $params['items'],
            ]
        ];
        return Cafe24ApiService::get($request->mall_id, $this->token, 'orders/' . $params['order_id'] . '/items');

        $response = Cafe24ApiService::put($request->mall_id, $this->token, 'orders/' . $params['order_id'] . '/return/' . $params['claim_code'], $data);

        if (isset($response->error)) {
            return $this->apiResponse->error(false, [], ['error' => $response->error]);
        }
        return $this->apiResponse->ok(true, $response);
    }
}
