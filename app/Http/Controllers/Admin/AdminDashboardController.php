<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\DigiflazzService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminDashboardController extends Controller
{
    public function index(DigiflazzService $digiflazz)
    {
        $today = now()->toDateString();

        $todayOrders = Order::whereDate('created_at', $today)->count();
        $pending     = Order::where('status', 'pending_payment')->count();
        $processing  = Order::where('status', 'processing')->count();
        $paid        = Order::where('status', 'paid')->count();
        $fulfilled   = Order::where('status', 'fulfilled')->count();
        $failed      = Order::where('status', 'failed')->count();

        $profit = (int) Order::query()
            ->leftJoin('products', 'products.id', '=', 'orders.product_id')
            ->whereIn('orders.status', ['paid', 'fulfilled'])
            ->sum(DB::raw('orders.gross_amount - IFNULL(products.digiflazz_price, 0)'));

        // ===== Digiflazz Balance (cache 60 detik biar gak spam API) =====
        $balanceValue = null;
        $balanceUpdatedAt = null;
        $balanceError = null;

        try {
            $balancePayload = Cache::remember('digiflazz.balance', 60, function () use ($digiflazz) {
                return $digiflazz->checkBalance();
            });

            $balanceValue = data_get($balancePayload, 'data.deposit');
            $balanceValue = is_numeric($balanceValue) ? (int) $balanceValue : null;

            $balanceUpdatedAt = now()->toDateTimeString();

            if ($balanceValue === null) {
                $balanceError = 'format saldo tidak valid';
            }

        } catch (\Throwable $e) {
            $balanceError = 'gagal ambil saldo';
            Log::error('DIGIFLAZZ_BALANCE_ERROR', [
                'err' => $e->getMessage(),
            ]);
        }

        return view('admin.dashboard', compact(
            'todayOrders', 'pending', 'processing', 'paid', 'fulfilled', 'failed', 'profit',
            'balanceValue', 'balanceUpdatedAt', 'balanceError'
        ));
    }
}
