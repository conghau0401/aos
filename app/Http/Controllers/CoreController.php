<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facades\AOSAPI;

class CoreController extends Controller
{
    protected $token;

    public function __construct()
    {
        $this->token = request()->bearerToken();
    }

    /**
     * Get list menu
     *
     * @param Request $request
     * @return object
     */
    public function commonCode(Request $request)
    {
        $token = $request->header('Authorization');
        return AOSAPI::post($token, 'core/common-code', $request->all());
    }

    /**
     * Get by code type
     *
     * @param Request $request
     * @return object
     */
    public function getByCodeType(Request $request)
    {
        return AOSAPI::post($this->token, 'core/common-code/type', $request->all());
    }

    /**
     * Check token expired
     *
     * @return void
     */
    public function check()
    {
        return AOSAPI::post($this->token, 'account/api');
    }

    /**
     * Center info
     *
     * @return void
     */
    public function centerInfo()
    {
        return AOSAPI::get($this->token, 'core/center-info');
    }

    /**
     * Center option
     * @param string $centerCode
     *
     * @return void
     */
    public function centerOption($centerCode)
    {
        return AOSAPI::get($this->token, 'core/center-option/' . $centerCode);
    }
}
