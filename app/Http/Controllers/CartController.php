<?php

namespace App\Http\Controllers;

use App\Facades\AOSAPI;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    protected $token;
    protected $apiResponse;
    protected $cartService;

    public function __construct(
        \App\Contracts\ApiResponseInterface $apiResponse,
        \App\Services\CartService $cartService
    ) {
        $this->token = request()->bearerToken();
        $this->apiResponse = $apiResponse;
        $this->cartService = $cartService;
    }

    /**
     * Create new cart item
     *
     * @param Request $request
     * @return void
     */
    public function create(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'ds_product_id' => 'required',
            'ds_option_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'shipping_method' => [
                'required',
                Rule::in(config('aos.shipping_method')),
            ],
        ]);
        if ($validator->fails()) {
            return $this->apiResponse->validationError(false, $validator->errors());
        }

        // check user
        $user = User::where('access_token', $this->token)->first();
        if (empty($user)) {
            return $this->apiResponse->notFound(false, [], ['message' => 'User not exitst']);
        }

        // check product
        $product = AOSAPI::get($this->token, 'product/cf24-product/' . $request->ds_product_id);
        if (empty($product->data)) {
            return $this->apiResponse->notFound(false, [], ['message' => 'Product not exitst']);
        }

        // check option
        $optionIds = [];
        foreach ($product->data->options as $key => $option) {
            array_push($optionIds, $option->optionId);
        }
        if (!in_array($request->ds_option_id, $optionIds)) {
            return $this->apiResponse->notFound(false, [], ['message' => 'Product option not exitst']);
        }

        // create or update cart item
        $conditions = [
            'store_id' => $user->store_id,
            'ds_product_id' => $request->ds_product_id,
            'ds_option_id' => $request->ds_option_id,
            'user_id' => $user->doosoun_id,
            'shipping_method' => $request->shipping_method,
        ];
        $updateData = [
            'quantity' => $request->quantity,
        ];
        $result = Cart::updateOrCreate($conditions, $updateData);

        Log::info(jEncode($product));
        $result = $this->cartService->addFields($result, $product->data);

        return $this->apiResponse->ok(true, $result);
    }

    /**
     * Create new cart item
     *
     * @param Request $request
     * @return void
     */
    public function createMultiple(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'listRequest' => 'array',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse->validationError(false, $validator->errors());
        }

        // check user
        $user = User::where('access_token', $this->token)->first();
        if (empty($user)) {
            return $this->apiResponse->notFound(false, [], ['message' => 'User not exitst']);
        }

        $productIdsDoosoun = [];
        foreach ($request->listRequest as $item) {
            array_push($productIdsDoosoun, $item['ds_product_id']);
        }
        // get products doosoun
        $productsDoosoun = AOSAPI::post($this->token, 'product/cf24-product', ['product_ids' => $productIdsDoosoun]);
        if (empty($productsDoosoun->data)) {
            return $this->apiResponse->notFound(false, [], ['message' => 'Product not exitst']);
        }
        $listItem = [];
        foreach ($productsDoosoun->data as $product) {
            foreach ($product->options as $option) {
                foreach ($request->listRequest as $item) {
                    if ($item['ds_option_id'] == $option->optionId && $item['ds_product_id'] == $option->productId) {
                        // create or update cart item
                        $conditions = [
                            'store_id' => $user->store_id,
                            'ds_product_id' => $item['ds_product_id'],
                            'ds_option_id' => $item['ds_option_id'],
                            'user_id' => $user->doosoun_id,
                            'shipping_method' =>  $item['shipping_method'] ?? 2
                        ];
                        $updateData = [
                            'quantity' => $item['quantity'],
                        ];
                        $result = Cart::updateOrCreate($conditions, $updateData);
                        $listItem[] = $this->cartService->addFields($result, $product);
                        if ($item['quantity'] == 0) {
                            Cart::where($conditions)->delete();
                        }
                    }
                }
            }
        }
        return $this->apiResponse->ok(true, $listItem);
    }

    /**
     * Get list cart items
     *
     * @param Request $request
     * @return void
     */
    public function list(Request $request)
    {
        // check user
        $user = User::where('access_token', $this->token)->first();
        if (empty($user)) {
            return $this->apiResponse->notFound(false, [], ['message' => 'User not exitst']);
        }

        $conditions = [
            'store_id' => $user->store_id
        ];
        if ($request->has('shipping_method')) {
            $conditions['shipping_method'] = $request->shipping_method;
        }
        // get list cart items
        $carts = Cart::where($conditions)->get();

        if ($carts->count() == 0) {
            return $this->apiResponse->ok(true, [], ['shipping_fee' => 0]);
        }
        $ids = [];
        // getProducts
        foreach ($carts as $item) {
            array_push($ids, $item->ds_product_id);
        }
        $products = AOSAPI::post($user->access_token, 'product/cf24-product', ['product_ids' => $ids]);
        if (empty($products->data)) {
            Log::error('Get products fail:' . jEncode($products));
            return $this->apiResponse->ok(false, [], ['shipping_fee' => 0], ['message' => 'Load data error']);
        }

        // get store
        $store = AOSAPI::get($user->access_token, 'user/store/' . $user->store_id);

        // calculate shipping fee
        $result = $this->cartService->calculateShippingFee($user, $store->data, $carts, $products->data);

        if ($request->has('group_by')) {
            $cartsCollection = collect($result['carts']);
            $carts = $cartsCollection->groupBy('productTp');
        }

        return $this->apiResponse->ok(true, $carts, ['shipping_fee' => $result['shipping_fee']]);
    }

    /**
     * Update a cart item
     *
     * @param Request $request
     * @return void
     */
    public function update(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'shipping_method' => [
                'required',
                Rule::in(config('aos.shipping_method')),
            ],
        ]);
        if ($validator->fails()) {
            return $this->apiResponse->ok(false, $validator->errors());
        }
        // check user
        $user = User::where('access_token', $this->token)->first();
        if (empty($user)) {
            return $this->apiResponse->notFound(false, [], ['message' => 'User not exitst']);
        }

        // check cart item exitst
        $cartUpdate = Cart::where([
            'store_id' => $user->store_id,
        ])->whereIn('id', $request->ids)->get();
        if ($cartUpdate->count() == 0) {
            return $this->apiResponse->notFound(false, [], ['message' => 'Item not found']);
        }
        $carts = Cart::where(['store_id' => $user->store_id])->get();

        foreach ($cartUpdate as $checkCart) {
            $flag = 0;
            $quantity = $checkCart->quantity;
            $idsDelete = [];
            foreach ($carts as $cart) {
                // check merge quantity
                if (
                    $checkCart->ds_product_id == $cart->ds_product_id
                    && $checkCart->ds_option_id == $cart->ds_option_id
                    && $checkCart->id != $cart->id
                ) {
                    $flag = 1;
                    $quantity += $cart->quantity;
                    array_push($idsDelete, $cart->id);
                }
            }
            if ($flag == 0) {
                Cart::where('id', $checkCart->id)->update([
                    'shipping_method' => $request->shipping_method,
                ]);
            } else {
                Cart::where('id', $checkCart->id)->update([
                    'quantity' => $quantity,
                    'shipping_method' => $request->shipping_method,
                ]);
                Cart::whereIn('id', $idsDelete)->delete();
            }
        }

        return $this->apiResponse->ok(true, [], ['message' => 'Updated cart item']);
    }

    /**
     * Update a cart item
     *
     * @param Request $request
     * @return void
     */
    public function updateListQuantity(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'listRequest' => 'array',
            'listRequest.*.id' => 'required',
            'listRequest.*.quantity' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse->validationError(false, $validator->errors());
        }
        // check user
        $user = User::where('access_token', $this->token)->first();
        if (empty($user)) {
            return $this->apiResponse->notFound(false, [], ['message' => 'User not exitst']);
        }

        // update quantity
        foreach ($request->listRequest as $item) {
            // check cart item exitst
            $cartItem = Cart::where('store_id', $user->store_id)->where('id', $item['id'])->first();
            if (!empty($cartItem)) {
                Cart::where('id', $item['id'])->update(['quantity' => $item['quantity']]);
            }
        }

        return $this->apiResponse->ok(true, [], ['message' => 'Updated cart item']);
    }

    /**
     * Delete a cart item
     *
     * @param int $id
     * @return void
     */
    public function delete($id)
    {
        // check user
        $user = User::where('access_token', $this->token)->first();
        if (empty($user)) {
            return $this->apiResponse->notFound(false, [], ['message' => 'User not exitst']);
        }
        // check cart item exitst
        $result = Cart::whereIn('id', explode(',', $id))->where('store_id', $user->store_id)->delete();
        if (empty($result)) {
            return $this->apiResponse->notFound(false, [], ['message' => 'Item not found']);
        }
        return $this->apiResponse->ok(true, [], ['message' => 'Deleted cart item']);
    }

    /**
     * Get margin rate
     *
     * @return void
     */
    public function getMarginRate()
    {
        // check user
        $user = User::where('access_token', $this->token)->first();
        if (empty($user)) {
            return $this->apiResponse->notFound(false, [], ['message' => 'User not exitst']);
        }

        // get store
        $store = AOSAPI::get($user->access_token, 'user/store/' . $user->store_id);

        $marginRates = $this->cartService->getMarginRate($user, $store->data);
        return $this->apiResponse->ok(true, $marginRates);
    }
}
