<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facades\AOSAPI;

class CategoryController extends Controller
{
    protected $token;

    public function __construct()
    {
        $this->token = request()->bearerToken();
    }

    /**
     * Get menu product
     *
     * @return object
     */
    public function menu()
    {
        $response = AOSAPI::get($this->token, 'product/cf24-category');
        $result = buildTreeMenu($response->data, '*');
        return $result;
    }
}
