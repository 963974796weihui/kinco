<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Services\Admin\AuthCodeServicesController;
use Illuminate\Support\Facades\Cache;


class AuthCodeController extends Controller
{

    protected $AuthCodeServices;
    protected $id;

    public function __construct(AuthCodeServicesController $AuthCodeServicesController)
    {
        $this->AuthCodeServicesController = $AuthCodeServicesController;
        $this->id = Cache::get('loginId')[0];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 授权码信息
     */
    public function codeInfo(Request $request)
    {
        $sncode = $request->input('sncode');
        $limit = $request->input('limit');
        $result = $this->AuthCodeServicesController->codeInfo($limit, $sncode);
        return response()->json(['status' => 'S', 'code' => '200', 'message' => $result]);
    }
}