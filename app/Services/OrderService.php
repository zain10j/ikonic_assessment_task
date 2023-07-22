<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use ErrorException;
use Exception;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function __construct(
        protected AffiliateService $affiliateService
    ) {}

    /**
     * Process an order and log any commissions.
     * This should create a new affiliate if the customer_email is not already associated with one.
     * This method should also ignore duplicates based on order_id.
     *
     * @param  array{order_id: string, 
     * subtotal_price: float, 
     * merchant_domain: string, 
     * discount_code: string, 
     * customer_email: string,
     * customer_name: string} $data
     * @return void
     */
    public function processOrder(array $data)
    {
        try{
            $order = Order::find($data['order_id']);
            if($order->payout_status == Order::STATUS_PAID){
                return null;
            }
            Log::info('Commission against order id : '.$order->id, ['commission' => $order->commission_owed]);
            $order->update(['payout_status' => Order::STATUS_PAID]);

            $existingAffiliate = User::where('email',$data['customer_email'])->first();
            if(!$existingAffiliate){
                $merchant = User::find($order->merchant_id);
                $this->affiliateService->register($merchant, $data['customer_email'], $data['customer_name'], $order->commission_owed);
            }
            return null;
        }catch(Exception $e){
            throw new ErrorException($e->getMessage());
        }
    }
}
