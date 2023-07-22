<?php

namespace App\Services;

use App\Jobs\PayoutOrderJob;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use ErrorException;
use Exception;

class MerchantService
{
    /**
     * Register a new user and associated merchant.
     * Hint: Use the password field to store the API key.
     * Hint: Be sure to set the correct user type according to the constants in the User model.
     *
     * @param array{domain: string, name: string, email: string, api_key: string} $data
     * @return Merchant
     */
    public function register(array $data): Merchant
    {
        try{
            $user = User::create(['name' => $data['name'],
                                'email' =>  $data['email'],
                                'password' => $data['api_key'],
                                'type' => User::TYPE_MERCHANT]);

            return $user;
        }catch(Exception $e){
            throw new ErrorException($e->getMessage());
        }
    }

    /**
     * Update the user
     *
     * @param array{domain: string, name: string, email: string, api_key: string} $data
     * @return void
     */
    public function updateMerchant(User $user, array $data)
    {
        try{
            User::where('id',$user->id)->update(['name' => $data['name'],
                                                'email' =>  $data['email'],
                                                'password' => $data['api_key'],
                                                'type' => User::TYPE_MERCHANT]);
        }catch(Exception $e){
            throw new ErrorException($e->getMessage());
        }
    }

    /**
     * Find a merchant by their email.
     * Hint: You'll need to look up the user first.
     *
     * @param string $email
     * @return Merchant|null
     */
    public function findMerchantByEmail(string $email): ?Merchant
    {
        try{
           $user = User::where('email',$email)->first();
           if($user){
            return $user;
           }else{
             return null;
           }
        }catch(Exception $e){
            throw new ErrorException($e->getMessage());
        }
    }

    /**
     * Pay out all of an affiliate's orders.
     * Hint: You'll need to dispatch the job for each unpaid order.
     *
     * @param Affiliate $affiliate
     * @return void
     */
    public function payout(Affiliate $affiliate)
    {
        try{
            $orders = Order::where('status', Order::STATUS_UNPAID)->get();
            foreach($orders as $order){
                PayoutOrderJob::dispatch($order);
            }
        }catch(Exception $e){
            throw new ErrorException($e->getMessage());
        }
    }
}
