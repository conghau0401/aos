<?php

namespace App\Http\Controllers;

use App\Facades\AOSAPI;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected $token;
    protected $apiResponse;

    public function __construct(\App\Contracts\ApiResponseInterface $apiResponse)
    {
        $this->token = request()->bearerToken();
        $this->apiResponse = $apiResponse;
    }

    /**
     * Get store informations
     *
     * @return object
     */
    public function index()
    {
        // check user
        $user = User::where('access_token', $this->token)->first();
        if (empty($user)) {
            return $this->apiResponse->notFound(false, [], ['message' => 'User not exist']);
        }

        // get store
        return AOSAPI::get($user->access_token, 'user/store/' . $user->store_id);
    }

    /**
     * Get store informations
     *
     * @return object
     */
    public function storeSettings()
    {
        return AOSAPI::get($this->token, 'product/m1043_7/AOS');
    }

    /**
     * Get credit store
     *
     * @return object
     */
    public function credit()
    {
        // check user
        $user = User::where('access_token', $this->token)->first();
        if (empty($user)) {
            return $this->apiResponse->notFound(false, [], ['message' => 'User not exist']);
        }
        // get credit
        return AOSAPI::get($this->token, 'product/cf24-product/credit/' . $user->store_id);
    }

    /**
     * Update store informations
     *
     * @return object
     */
    public function updateStore(Request $request)
    {
        // check user
        $user = User::where('access_token', $this->token)->first();
        if (empty($user)) {
            return $this->apiResponse->notFound(false, [], ['message' => 'User not exist']);
        }
        $params = $request->all();
        $params['storeNo'] = $user->store_id;
        $params['useflag'] = true;
        Log::info('params:', $params);
        // get store
        return AOSAPI::put($user->access_token, 'user/store', $params);
    }

    /**
     * Get mypage informations
     *
     * @return object
     */
    public function mypage()
    {
        // get mypage
        return AOSAPI::get($this->token, 'user/my-page');
    }

    /**
     * Get detail user informations
     *
     * @return object
     */
    public function detail($id)
    {
        // get user detail
        return AOSAPI::get($this->token, 'user/user/' . $id);
    }

    /**
     * Get detail user informations
     *
     * @return object
     */
    public function updateInfo(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->ok(false, $validator->errors());
        }
        $params = [
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'phone' => $request->phone,
        ];
        if (!empty($request->avatar)) {
            $params['avatar'] = $request->avatar;
        }

        // get user detail
        $result = AOSAPI::put($this->token, 'user/my-page', $params);
        Log::info('Update user info: ' . jEncode($result) . jEncode($request->all()));
        return $result;
    }

    /**
     * Get store deposit deduction
     *
     * @return object
     */
    public function getDepositDeduction()
    {
        // check user
        $user = User::where('access_token', $this->token)->first();
        if (empty($user)) {
            return $this->apiResponse->notFound(false, [], ['message' => 'User not exist']);
        }
        $params = [
            'storeIds' => [$user->store_id],
            'type' => "1", // search deposit
        ];
        $response = AOSAPI::post($user->access_token, 'user/store-deposit-deduction', $params);
        return $response;
    }

    /**
     * Create deposit
     *
     * @return object
     */
    public function createDeposit(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'depositAmount' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse->validationError(false, $validator->errors());
        }

        // check user
        $user = User::where('access_token', $this->token)->first();
        $store = AOSAPI::get($user->access_token, 'user/store/' . $user->store_id);
        if (empty($store)) {
            return $this->apiResponse->notFound(false, [], ['message' => 'Store is not exist']);
        }
        $params = [
            'depositDate' => date('Y-m-d'),
            'lineId' => null,
            'storeId' => $store->data->storeNo,
            'storeName' => $store->data->storeNm,
            'depositAmount' => $request->depositAmount,
            'depositTypeCode' => '2',
            'depositTypeName' => 'AOS Deposit',
            'paymentMethodCode' => 'B',
            'paymentMethodName' => 'Credit card deposit',
            'bankCode' => null,
            'bankName' => '',
            'depositAccountNo' => null,
            'depositDepositor' => null,
            'depositNote' => 'AOS deposit',
            'storeNameSearch' => '',
            'depositTypeNameSearch' => '',
            'bankNameSearch' => '',
            'paymentMethodNameSearch' => ''
        ];
        $response = AOSAPI::put($user->access_token, 'user/store-deposit', $params);
        Log::channel('user')->info('Create deposit: ' . jEncode($response));
        return $response;
    }

    /**
     * Create deduction
     *
     * @return object
     */
    public function createDeduction(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'deductionAmount' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse->validationError(false, $validator->errors());
        }

        // check user
        $user = User::where('access_token', $this->token)->first();
        $store = AOSAPI::get($user->access_token, 'user/store/' . $user->store_id);
        if (empty($store)) {
            return $this->apiResponse->notFound(false, [], ['message' => 'Store is not exist']);
        }
        $params = [
            'deductionDate' => date('Y-m-d'),
            'lineId' => null,
            'storeId' => $store->data->storeNo,
            'storeName' => $store->data->storeNm,
            'deductionAmount' => $request->deductionAmount,
            'deductionTypeCode' => 'M',
            'deductionTypeName' => 'Deduct marketing promotion fees',
            'deductionNote' => 'AOS deduction',
            'deductionTypeNameSearch' => '',
            'storeNameSearch' => '',
        ];
        $response = AOSAPI::put($user->access_token, 'user/store-deduction', $params);
        Log::channel('user')->info('Create deduction: ' . jEncode($response));
        return $response;
    }

    /**
     * Update language function
     *
     * @param Request $request
     * @return void
     */
    public function updateLanguage(Request $request)
    {
        $params = [
            "timezoneName" => "",
            "language" => $request->languange,
            "fontName" => "Nanum Gothic",
            "fontSize" => "medium",
            "theme" => "softblue"
        ];
        $response = AOSAPI::put($this->token, 'user/my-page/preferences', $params);
        return $response;
    }

    /**
     * Get Store certificate
     *
     * @param Request $request
     * @return void
     */
    public function storeCertificate()
    {
        // check user
        $user = User::where('access_token', $this->token)->first();
        return AOSAPI::get($user->access_token, 'product/store-certificate/' . $user->store_id);
    }

    /**
     * Get list certificate
     *
     * @param Request $request
     * @return void
     */
    public function listCertificate(Request $request)
    {
        // check user
        $user = User::where('access_token', $this->token)->first();
        $params = [
            'storeId' => $user->store_id,
            'limit' => $request->limit ?? 10,
        ];
        return AOSAPI::get($user->access_token, 'product/store-certificate', $params);
    }
}
