<?php

namespace App\Services;

use App\Exceptions\AffiliateCreateException;
use App\Mail\AffiliateCreated;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use ErrorException;
use Exception;
use Illuminate\Support\Facades\Mail;

class AffiliateService
{
    public function __construct(
        protected ApiService $apiService
    ) {}

    /**
     * Create a new affiliate for the merchant with the given commission rate.
     *
     * @param  Merchant $merchant
     * @param  string $email
     * @param  string $name
     * @param  float $commissionRate
     * @return Affiliate
     */
    public function register(Merchant $merchant, string $email, string $name, float $commissionRate): Affiliate
    {
        try{
            $user = User::create(['name' => $name,
                                'email' => $email,
                                'type' => User::TYPE_AFFILIATE]);
            if($user){
                Affiliate::create(['userId' => $user->id,
                                    'merchant_id' => $merchant->id,
                                    'commission_rate' => $commissionRate,
                                    'discount_code' => 123]);
            }

            return $user;
        }catch(Exception $e){
            throw new ErrorException($e->getMessage());
        }
    }
}
