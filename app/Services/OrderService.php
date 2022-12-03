<?php

namespace App\Services;

use App\Facades\AOSAPI;
use App\Facades\Cafe24ApiService;

use App\Models\Mall;
use App\Models\Mapp;
use Illuminate\Support\Facades\Log;
use App\Enums\ShippingMethodEnum;
use App\Models\Order;
use App\Models\OrderItem;

class OrderService
{
    protected $token;
    protected $cartService;

    public function __construct(\App\Services\CartService $cartService)
    {
        $this->token = request()->bearerToken();
        $this->cartService = $cartService;
    }

    /**
     * Create order cafe24
     *
     * @param Request $request
     * @param object $mall
     * @param object $user
     * @param object $userDetail
     * @param object $store
     * @param object $carts
     * @param object $productsCafe24
     * @param object $productsDoosoun
     * @param object $productsDoosoun
     * @return object|array
     */
    public function createOrderCafe24($mall, $request, $user, $userDetail, $store, $carts, $productsCafe24, $productsDoosoun, $mappings, $marginRates)
    {
        $prepare = $this->prepareCartItemCafe24($carts, $productsCafe24, $productsDoosoun, $mappings, $marginRates);
        Log::channel('order')->info('Step 4.1 - prepare order item cafe24: ' . jEncode($prepare));
        $shippingFee = $this->cartService->calculateShippingFee($user, $store, $carts, $productsDoosoun);
        // set receive user
        $receiverName = $request->receiver_name;
        $receiverCode = $request->receiver_zipcode;
        $receiverAddress1 = $request->receiver_address1;
        $receiverAddress2 = $request->receiver_address2;
        $receiverPhone = $request->receiver_phone;
        $receiverMobile = $request->receiver_phone;
        $receiverNote = $request->note;
        // check shipping_method = same day (suppiler)
        if ($carts[0]->shipping_method == ShippingMethodEnum::SUPPLIER) {
            $receiverName = $request->receiver_name_sameday;
            $receiverCode = $request->receiver_zipcode_sameday;
            $receiverAddress1 = $request->receiver_address1_sameday;
            $receiverAddress2 = $request->receiver_address2_sameday;
            $receiverPhone = $request->receiver_phone_sameday;
            $receiverMobile = $request->receiver_phone_sameday;
            $receiverNote = $request->note_sameday;
        }

        $dataOrder = [
            "shop_no" => 1,
            "requests" => [
                [
                    "order_type" => "self",
                    "paid" => "T",
                    "payment_request" => "F",
                    "buyer_name" => $userDetail->name,
                    "buyer_zipcode" => $receiverCode,
                    "buyer_address1" => $receiverAddress1,
                    "buyer_address2" => $receiverAddress2,
                    "buyer_phone" => formatPhoneNumber($userDetail->phone),
                    "buyer_cellphone" => formatPhoneNumber($userDetail->mobile),
                    "buyer_email" => $store->email,
                    "buyer_ip" => request()->ip(),
                    "receiver_name" =>  $receiverName,
                    "receiver_zipcode" => $receiverCode,
                    "receiver_address1" => $receiverAddress1,
                    "receiver_address2" =>  $receiverAddress2,
                    "receiver_phone" => formatPhoneNumber($receiverPhone),
                    "receiver_cellphone" => formatPhoneNumber($receiverMobile),
                    "shipping_message" => $receiverNote,
                    "billing_name" => $receiverName,
                    "payment_amount" => $prepare['amount'] + $shippingFee['shipping_fee'],
                    "additional_discount_name" => "",
                    // "additional_discount_price" => $prepare['additional_discount'],
                    "shipping_fee" => $shippingFee['shipping_fee'],
                    "shipping_type" => "B",
                    "country_code" => "KR",
                    "send_order_email" => config('aos.send_email'),
                    "include_not_mall_product" => "T",
                    "update_inventory" => "T",
                    "items" => $prepare['items'],
                ]
            ]
        ];

        Log::channel('order')->info('Step 4.2: payload cafe24' . jEncode($dataOrder));
        $result = Cafe24ApiService::post($mall->mall_id, $mall->access_token, 'orders', $dataOrder);
        $result->amount = $prepare['amount'];
        return $result;
    }

    /**
     * Create order cafe24
     *
     * @param Request $request
     * @param object $mall
     * @param object $userDetail
     * @param object $store
     * @param object $carts
     * @param object $orderCafe24
     * @param object $productsCafe24
     * @param object $productsDoosoun
     * @param object $marginRates
     * @return object|array
     */
    public function createOrderDoosoun($mall, $request, $user, $userDetail, $store, $carts, $orderCafe24, $productsDoosoun, $marginRates)
    {
        // prepare item product
        $items = $this->prepareCartItemDoosoun($request, $carts, $mall, $orderCafe24, $userDetail, $store, $user, $productsDoosoun, $marginRates);

        $dataOrder = [
            'mallCode' => config('aos.mall_code'),
            'requestList' => $items,
        ];
        Log::channel('order')->info('Step 5.1: payload doosoun' . jEncode($dataOrder));

        return AOSAPI::put($this->token, 'ecommerce/cf24/order', $dataOrder);
    }

    /**
     * prepare item product order cafe24
     *
     * @param object $carts
     * @param object $productsCafe24
     * @param object $productsDoosoun
     * @param object $mappings
     * @param object $marginRates
     * @return array
     */
    private function prepareCartItemCafe24($carts, $productsCafe24, $productsDoosoun, $mappings, $marginRates)
    {
        $items = [];
        $amount = 0;
        $amountDiscount = 0;
        // check mapping product cafe24 vs doosoun
        foreach ($carts as $cart) {
            foreach ($mappings as $mapp) {
                foreach ($productsCafe24 as $productCafe24) {
                    foreach ($productsDoosoun as $productAOS) {
                        if (
                            $mapp->cafe24_id == $productCafe24->product_no
                            && $mapp->doosoun_id == $productAOS->productId
                            && $cart->ds_product_id == $mapp->doosoun_id
                        ) {
                            $optionCart = new \stdClass();
                            foreach ($productAOS->options as $option) {
                                if ($cart->ds_option_id == $option->optionId) {
                                    $optionCart = $option;
                                    $optionCart->productTp = $productAOS->productTp;
                                }
                            }
                            $realPrice = ($optionCart->discount_price != 0) ? $optionCart->discount_price : $optionCart->supplyUnitPrice;
                            // if local (vn mall) => parse int
                            if (env('APP_ENV') == 'local') {
                                $realPrice = (int)$realPrice;
                            }
                            $realPrice = $this->calculatePrice($cart->shipping_method, $optionCart, $marginRates, 1);

                            $items[] = [
                                "product_code" => $productCafe24->product_code,
                                "product_no" => $productCafe24->product_no,
                                "product_name" => $productCafe24->product_name,
                                "options" => [
                                    [
                                        "name" => "Package",
                                        "value" => $optionCart->optionName,
                                    ]
                                ],
                                "option_id" => "000" . $optionCart->optionId,
                                "quantity" => $cart->quantity,
                                "product_price" => $realPrice,
                                "product_tax_type" => "A",
                                "tax_amount" => 10,
                                // "supply_price" => $optionCart->supplyUnitPrice,
                                // "option_price" => $optionCart->supplyUnitPrice,
                            ];

                            $amount = $amount + ($realPrice * $cart->quantity);
                        }
                    }
                }
            }
        }
        Log::channel('order')->info('Step3 - product info');
        return [
            'items' => $items,
            'amount' => $amount,
            'additional_discount' => $amountDiscount,
        ];
    }

    /**
     * prepare item product order doosun
     *
     * @param Request $request
     * @param object $carts
     * @param object $mall
     * @param object $orderCafe24
     * @param object $userDetail
     * @param object $store
     * @param object $user
     * @param object $productsDoosoun
     * @param object $marginRates
     * @return array
     */
    private function prepareCartItemDoosoun($request, $carts, $mall, $orderCafe24, $userDetail, $store, $user, $productsDoosoun, $marginRates)
    {
        $items = [];

        // get items order cafe24
        $orderItemsCafe24 = Cafe24ApiService::get($mall->mall_id, $mall->access_token, 'orders/' . $orderCafe24->orders[0]->order_id . '/items');
        Log::channel('order')->info('Get order items: ' . jEncode($orderItemsCafe24));
        foreach ($carts as $cart) {
            foreach ($productsDoosoun as $productAOS) {
                if ($cart->ds_product_id == $productAOS->productId) {
                    $map = Mapp::where('doosoun_id', $cart->ds_product_id)->first();

                    $optionCart = new \stdClass();
                    foreach ($productAOS->options as $option) {
                        if ($cart->ds_option_id == $option->optionId) {
                            $optionCart = $option;
                            $optionCart->productTp = $productAOS->productTp;
                        }
                    }

                    $codeItemOrderCafe24 = '';
                    foreach ($orderItemsCafe24->items as $item) {
                        // check product and option similar
                        if ($map->cafe24_id == $item->product_no && $cart->ds_option_id == (int)$item->option_id) {
                            $codeItemOrderCafe24 = $item->order_item_code;
                            break;
                        }
                    }

                    // time delivery | default pickup center
                    $timeDelivery = $request->date_time_pickup;
                    // calculate shipping fee
                    $shippingFee = 0;
                    $realPrice = $this->calculatePrice($cart->shipping_method, $optionCart, $marginRates, 1);
                    if ($cart->shipping_method == ShippingMethodEnum::DELIVERY) {
                        $timeDelivery = $request->date_time_delivery;
                        $unitPrice = $realPrice * $cart->quantity;
                        $shippingFee = $marginRates[$productAOS->productTp]["delivery"]["shipping"] * $unitPrice / 100;
                    } else if ($cart->shipping_method == ShippingMethodEnum::SUPPLIER) {
                        $timeDelivery = $request->date_time_same_day;
                        $unitPrice = $realPrice * $cart->quantity;
                        $shippingFee = $marginRates[$productAOS->productTp]["supplier"]["shipping"] * $unitPrice / 100;
                    }

                    $receiverName = $request->receiver_name;
                    $receiverCode = $request->receiver_zipcode;
                    $receiverAddress1 = $request->receiver_address1;
                    $receiverAddress2 = $request->receiver_address2;
                    $receiverPhone = $request->receiver_phone;
                    $receiverMobile = $request->receiver_phone;
                    $receiverNote = $request->note;
                    // check shipping_method = same day (suppiler)
                    if ($cart->shipping_method == ShippingMethodEnum::SUPPLIER) {
                        $receiverName = $request->receiver_name_sameday;
                        $receiverCode = $request->receiver_zipcode_sameday;
                        $receiverAddress1 = $request->receiver_address1_sameday;
                        $receiverAddress2 = $request->receiver_address2_sameday;
                        $receiverPhone = $request->receiver_phone_sameday;
                        $receiverMobile = $request->receiver_phone_sameday;
                        $receiverNote = $request->note_sameday;
                    }

                    $items[] = [
                        "order_date" => date('Y-m-d'),
                        "delivery_request_dm" => $timeDelivery,
                        "order_item_code" => $codeItemOrderCafe24,
                        "mall_order_id" => $orderCafe24->orders[0]->order_id,
                        "order_place_id" => $store->storeNo,
                        "receiver_name" => $receiverName,
                        "receiver_zipcode" => $receiverCode,
                        "receiver_address1" => $receiverAddress1,
                        "receiver_address2" => $receiverAddress2,
                        "receiver_cellphone" => $receiverPhone,
                        "buyer_name" => $userDetail->name,
                        "buyer_phone" => $userDetail->phone,
                        "buyer_cellphone" => $userDetail->mobile,
                        "shipping_message" => $receiverNote,
                        "payment_amount" => $orderCafe24->amount,
                        "custom_variant_code" => $productAOS->productId . $cart->ds_option_id,
                        "product_name" => $productAOS->productNm,
                        "quantity" => $cart->quantity,
                        "product_price" => $realPrice,
                        "pay_unit_price" => $realPrice,
                        "shipping_fee" => (int)$shippingFee,
                        "shipping_type" => "A",
                        "buyer_email" => $userDetail->email,
                        "paid" => "T",
                        "shipping_status" => "F",
                        "shipping_method" => $cart->shipping_method,
                        "currency" => "KRW",
                        "country_code" => "KR",
                        "variant_code" => "P000BFGQ000A"
                    ];
                }
            }
        }

        return $items;
    }

    /**
     * save order result doosoun
     *
     * @param object $data
     * @return void
     */
    public function saveOrderResult($data)
    {
        Order::create([
            'order_id' => $data->orderId,
            'mall_id' => $data->mallCode,
            'mall_order_id' => $data->mallOrderId,
            'user_id' => $data->createdBy,
            'store_id' => $data->orderPlaceId,
            'payment_amount' => $data->paymentAmount,
            'shipping_method' => $data->shippingMethod,
        ]);
        Log::channel('order')->info('create order...');
        foreach ($data->orderProducts as $product) {
            OrderItem::create([
                'order_id' => $data->orderId,
                'product_id' => $product->productId,
                'option_id' => $product->optionId,
                'quantity' => $product->quantity,
                'price' => $product->productPrice,
                'shipping_fee' => $product->shippingFee,
            ]);
            Log::channel('order')->info('create order items...');
        }
    }

    /**
     * calculatePrice function
     *
     * @param string $shippingMethod
     * @param object $option
     * @param object $marginRates
     * @param int $quantity
     * @return int
     */
    private function calculatePrice($shippingMethod, $option, $marginRates, $quantity)
    {
        $price = $option->supplyUnitPrice;
        $productType = (int)$option->productTp;
        switch ($shippingMethod) {
            case 1:
                $rateByShippingMethod = $marginRates[$productType]["center_pickup"]["margin"];
                break;
            case 2:
                $rateByShippingMethod = $marginRates[$productType]["delivery"]["margin"];
                break;
            default:
                $rateByShippingMethod = $marginRates[$productType]["supplier"]["margin"];
                break;
        }
        if ($productType == 3) {
            $price = ($quantity * $price) + ($option->quantityPerPack * $option->bottlePrice) + ($quantity * $option->containerUnitPrice) + ($quantity * $price * $rateByShippingMethod / 100);
        } else {
            $price = $price + ($price * $rateByShippingMethod / 100);
        }
        return round($price);
    }
}
