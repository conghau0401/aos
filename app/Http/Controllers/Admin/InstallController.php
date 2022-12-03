<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class InstallController extends Controller
{
    protected $mallService;
    public function __construct(\App\Services\MallService $mallService)
    {
        $this->mallService = $mallService;
    }


    /**
     * Install app and redirect to main
     *
     * @param Request $request
     * @return void
     */
    public function install(Request $request)
    {
        $this->mallService->installApp($request);
        return redirect('admin/products');
    }
}
