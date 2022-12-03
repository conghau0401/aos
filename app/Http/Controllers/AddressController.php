<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    protected $token;
    protected $apiResponse;

    public function __construct(\App\Contracts\ApiResponseInterface $apiResponse)
    {
        $this->token = request()->bearerToken();
        $this->apiResponse = $apiResponse;
    }

    /**
     * Display a listing of address
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // check user
        $user = User::where('access_token', $this->token)->first();
        if (empty($user)) {
            return $this->apiResponse->ok(false, [], ['message' => 'User not exist']);
        }
        $addresses = Address::select(['id', 'address_name', 'full_name', 'address_basic', 'address_detail', 'phone', 'mobile', 'zip_code', 'is_default'])
            ->where('store_id', $user->store_id)->orderBy('is_default', 'desc')->orderBy('id', 'desc')->get();
        return $this->apiResponse->ok(true, $addresses);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a address
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'address_basic' => 'required',
            'phone' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse->ok(false, $validator->errors());
        }
        // check user
        $user = User::where('access_token', $this->token)->first();
        if (empty($user)) {
            return $this->apiResponse->ok(false, [], ['message' => 'User not exist']);
        }

        $result = Address::create([
            'mall_id' => config('aos.mall_id'),
            'store_id' => $user->store_id,
            'address_name' => $request->address_name,
            'full_name' => $request->full_name,
            'address_basic' => $request->address_basic,
            'address_detail' => $request->address_detail,
            'phone' => $request->phone,
            'mobile' => $request->mobile,
            'zip_code' => $request->zip_code,
            'is_default' => $request->is_default,
        ]);
        // update is_default if set address default
        if (!empty($result) && $request->is_default == 1) {
            Address::where(['mall_id' => config('aos.mall_id'), 'store_id' => $user->store_id])
                ->where('id', '!=', $result->id)
                ->update(['is_default' => 0]);
        }
        return $this->apiResponse->ok(true, $result);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // check user
        $user = User::where('access_token', $this->token)->first();
        if (empty($user)) {
            return $this->apiResponse->ok(false, [], ['message' => 'User not exist']);
        }
        // check id
        $address = Address::select(['id', 'address_name', 'full_name', 'address_basic', 'address_detail', 'phone', 'mobile', 'zip_code', 'is_default'])
            ->where(['id' => $id, 'mall_id' => config('aos.mall_id'), 'store_id' => $user->store_id])->first();
        if (empty($address)) {
            return $this->apiResponse->ok(false, [], ['message' => 'Not found address']);
        }
        return $this->apiResponse->ok(true, $address);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update address
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'address_basic' => 'required',
            'phone' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse->ok(false, $validator->errors());
        }
        // check user
        $user = User::where('access_token', $this->token)->first();
        if (empty($user)) {
            return $this->apiResponse->ok(false, [], ['message' => 'User not exist']);
        }

        $result = Address::where(['id' => $id, 'mall_id' => config('aos.mall_id'), 'store_id' => $user->store_id])
            ->update([
                'address_name' => $request->address_name,
                'full_name' => $request->full_name,
                'address_basic' => $request->address_basic,
                'address_detail' => $request->address_detail,
                'phone' => $request->phone,
                'mobile' => $request->mobile,
                'zip_code' => $request->zip_code,
                'is_default' => $request->is_default,
            ]);
        // update is_default if set address default
        if (!empty($result) && $request->is_default == 1) {
            Address::where(['mall_id' => config('aos.mall_id'), 'store_id' => $user->store_id])
                ->where('id', '!=', $id)
                ->update(['is_default' => 0]);
        }
        return $this->apiResponse->ok(true, $result);
    }

    /**
     * Remove address
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // check user
        $user = User::where('access_token', $this->token)->first();
        if (empty($user)) {
            return $this->apiResponse->ok(false, [], ['message' => 'User not exist']);
        }
        // check id
        $address = Address::where(['id' => $id, 'mall_id' => config('aos.mall_id'), 'store_id' => $user->store_id])->first();
        if (empty($address)) {
            return $this->apiResponse->ok(false, [], ['message' => 'Not found address']);
        }
        $result = Address::where('id', $id)->delete();
        return $this->apiResponse->ok(true, $result);
    }
}
