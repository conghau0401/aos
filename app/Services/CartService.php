<?php

namespace App\Services;

use App\Enums\ShippingMethodEnum;
use App\Facades\AOSAPI;
use App\Models\Cart;
use Illuminate\Support\Facades\Log;

class CartService
{
    /**
     * Calculate shipping fee
     *
     * @param Request $request
     * @param object $user
     * @param object $store
     * @param array $carts
     * @return array
     */
    public function calculateShippingFee($user, $store, $carts, $productsDoosoun)
    {
        $marginRates = [];
        $shippingFee = 0;

        // get margin rate
        $resMarginRate = AOSAPI::get($user->access_token, 'product/maginRate');

        foreach ($resMarginRate->data as $rate) {
            if ($store->unitPriceCode == $rate->supplyUnitPriceGroup) {
                switch ($rate->shipmentType) {
                    case ShippingMethodEnum::CENTER_PICKUP:
                        $marginRates[$rate->productType]["center_pickup"]["margin"] = $rate->marginRate;
                        $marginRates[$rate->productType]["center_pickup"]["shipping"] = $rate->shippingRate;
                        break;

                    case ShippingMethodEnum::DELIVERY:
                        $marginRates[$rate->productType]["delivery"]["margin"] = $rate->marginRate;
                        $marginRates[$rate->productType]["delivery"]["shipping"] = $rate->shippingRate;
                        break;

                    case ShippingMethodEnum::SUPPLIER:
                        $marginRates[$rate->productType]["supplier"]["margin"] = $rate->marginRate;
                        $marginRates[$rate->productType]["supplier"]["shipping"] = $rate->shippingRate;
                        break;
                }
            }
        }

        foreach ($productsDoosoun as $product) {
            $rateCenterPickup = $marginRates[$product->productTp]["center_pickup"];
            $rateDelivery = $marginRates[$product->productTp]["delivery"]["shipping"];
            $rateSupplier = $marginRates[$product->productTp]["supplier"]["shipping"];
            foreach ($product->options as $option) {
                foreach ($carts as &$cartItem) {
                    if ($product->productId == $cartItem->ds_product_id && $option->optionId == $cartItem->ds_option_id) {
                        $productFeeShipping = 0;
                        // calculate fee ship delivery
                        if ($cartItem->shipping_method == ShippingMethodEnum::DELIVERY && $product->productTp != 3) { // not ancohol
                            $unitPrice = $option->supplyUnitPrice + ($option->supplyUnitPrice * $marginRates[$product->productTp]["delivery"]["margin"] / 100);
                            $productFeeShipping = ($rateDelivery * $unitPrice / 100) * $cartItem->quantity;
                            $shippingFee += (int)$productFeeShipping;
                        } else if ($cartItem->shipping_method == ShippingMethodEnum::SUPPLIER && $product->productTp != 3) { // not ancohol
                            $unitPrice = $option->supplyUnitPrice + ($option->supplyUnitPrice * $marginRates[$product->productTp]["supplier"]["margin"] / 100);
                            $productFeeShipping = ($rateSupplier * $unitPrice / 100) * $cartItem->quantity;
                            $shippingFee += (int)$productFeeShipping;
                        }
                        // add field to cart
                        $cartItem->maxOrder = $option->maxOrder;
                        $cartItem->price = $option->supplyUnitPrice;
                        $cartItem->supplyUnitPrice = $option->supplyUnitPrice;
                        $cartItem->discount_price = $option->discount_price;
                        $cartItem->optionName = $option->optionName;
                        $cartItem->quantityPerPack = $option->quantityPerPack;
                        $cartItem->bottlePrice = $option->bottlePrice;
                        $cartItem->containerUnitPrice = $option->containerUnitPrice;
                        $cartItem->limited = $option->limited;
                        $cartItem->stockQty = $option->stockQty;
                        $cartItem->option_sell_yn = $option->option_sell_yn;
                        //product
                        $cartItem->product_image = $product->image;
                        $cartItem->productNm = $product->productNm;
                        $cartItem->marketableSize = $product->marketableSize;
                        $cartItem->productTp = $product->productTp;
                        $cartItem->productTpName = $product->productTpName;
                        $cartItem->shippingFee = $productFeeShipping;
                    }
                }
            }
        }

        // remove product deleted from admin ERP
        foreach ($carts as $key => $cart) {
            if (!isset($cart->productTp)) {
                Cart::where('id', $cart->id)->delete();
                unset($carts[$key]);
            }
        }

        return [
            'carts' => $carts,
            'shipping_fee' => (int)$shippingFee,
        ];
    }

    /**
     * Add field to cart product
     *
     * @param object $user
     * @param object $cart
     * @return void
     */
    public function addFields(&$cartItem, $product)
    {
        foreach ($product->options as $option) {
            if ($cartItem->ds_option_id == $option->optionId) {
                Log::info('111' . jEncode($option));
                // add field to cart
                $cartItem->maxOrder = $option->maxOrder;
                $cartItem->price = $option->supplyUnitPrice;
                $cartItem->discount_price = $option->discount_price;
                $cartItem->optionName = $option->optionName;
                $cartItem->quantityPerPack = $option->quantityPerPack;
                $cartItem->limited = $option->limited;
                $cartItem->stockQty = $option->stockQty;
                $cartItem->option_sell_yn = $option->option_sell_yn;
                //product
                $cartItem->product_image = $product->image;
                $cartItem->productNm = $product->productNm;
                $cartItem->marketableSize = $product->marketableSize;
                $cartItem->productTp = $product->productTp;
                $cartItem->productTpName = $product->productTpName;
            }
        }
        return $cartItem;
    }

    /**
     * Get margin rate
     *
     * @param object $user
     * @param object $user
     * @return void
     */
    public function getMarginRate($user, $store)
    {
        $marginRates = [];

        // get margin rate
        $resMarginRate = AOSAPI::get($user->access_token, 'product/maginRate');

        foreach ($resMarginRate->data as $rate) {
            if ($store->unitPriceCode == $rate->supplyUnitPriceGroup) {
                switch ($rate->shipmentType) {
                    case ShippingMethodEnum::CENTER_PICKUP:
                        $marginRates[$rate->productType]["center_pickup"]["margin"] = $rate->marginRate;
                        $marginRates[$rate->productType]["center_pickup"]["shipping"] = $rate->shippingRate;
                        break;

                    case ShippingMethodEnum::DELIVERY:
                        $marginRates[$rate->productType]["delivery"]["margin"] = $rate->marginRate;
                        $marginRates[$rate->productType]["delivery"]["shipping"] = $rate->shippingRate;
                        break;

                    case ShippingMethodEnum::SUPPLIER:
                        $marginRates[$rate->productType]["supplier"]["margin"] = $rate->marginRate;
                        $marginRates[$rate->productType]["supplier"]["shipping"] = $rate->shippingRate;
                        break;
                }
            }
        }

        return $marginRates;
    }
}
