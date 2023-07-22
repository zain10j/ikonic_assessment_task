<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\Order;
use App\Services\MerchantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MerchantController extends Controller
{
    public function __construct(
        MerchantService $merchantService
    ) {}

    /**
     * Useful order statistics for the merchant API.
     * 
     * @param Request $request Will include a from and to date
     * @return JsonResponse Should be in the form {
     * count: total number of orders in range, 
     * commission_owed: amount of unpaid commissions for orders with an affiliate, 
     * revenue: sum order subtotals}
     */
    public function orderStats(Request $request): JsonResponse
    {
        $query = Order::whereBetween('created_at',[$request->fromDate,$request->toDate]);

        $totalNumOfOrders = $query->count();
        $totalCommissionOwed = $query->where('payout_status', Order::STATUS_UNPAID)->sum('commission_owed');
        $totalRevenue =  $query->sum('subtotal');

        return response()->json(['response' => ['status' => true, 'data' => [
            'count' => $totalNumOfOrders,
            'commission_owed' => $totalCommissionOwed,
            'revenue' => $totalRevenue,
            ]]], JsonResponse::HTTP_OK);
    }
}
