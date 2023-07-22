<?php

namespace App\Http\Controllers;

use App\Services\AffiliateService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    /**
     * Pass the necessary data to the process order method
     * 
     * @param  Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $data = array();
        $data['order_id'] = 1; 
        $data['subtotal_price'] = 1;  
        $data['merchant_domain'] = 1;  
        $data['discount_code'] = 1; 
        $data['customer_email'] = "email@gmail.com";
        $data['customer_name'] = "Name here";    

        $this->orderService($data);
    }
}
