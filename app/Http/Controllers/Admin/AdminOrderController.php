<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\FulfillOrderJob;
use App\Models\Order;
use App\Services\DigiflazzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $q = Order::query()->with(['user','product']);

        if ($request->filled('status')) {
            $q->where('status', $request->string('status'));
        }

        if ($request->filled('keyword')) {
            $kw = (string) $request->string('keyword');
            $q->where(function ($w) use ($kw) {
                $w->where('midtrans_order_id', 'like', "%{$kw}%")
                  ->orWhere('target', 'like', "%{$kw}%")
                  ->orWhere('digiflazz_trx_id', 'like', "%{$kw}%");
            });
        }

        $orders = $q->latest()->paginate(20)->withQueryString();
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user','product']);
        return view('admin.orders.show', compact('order'));
    }

    public function retryFulfill(Order $order)
    {
        // hanya retry kalau pembayaran sudah paid/settlement (minimal paid di sistem)
        if (!in_array($order->status, ['paid','processing','failed'], true)) {
            return back()->with('error', 'Order tidak bisa di-retry dari status ini.');
        }

        DB::transaction(function () use ($order) {
            // reset digiflazz supaya bisa request ulang ref_id baru
            $order->digiflazz_trx_id = null;
            $order->digiflazz_response = null;
            $order->digiflazz_status = null;
            $order->digiflazz_rc = null;
            $order->digiflazz_message = null;
            $order->digiflazz_sn = null;

            if (isset($order->digiflazz_token)) {
                $order->digiflazz_token = null;
            }

            // set processing supaya admin tahu sedang diproses
            $order->status = 'processing';
            $order->save();
        });

        FulfillOrderJob::dispatch($order->id, true);

        return back()->with('success', 'Retry fulfill dikirim ke queue (ref_id baru).');
    }

    /**
     * Tombol "Check Digiflazz" untuk re-poll status terakhir (kalau webhook miss).
     * Ini HARUS pakai ref_id yang sama (tidak membuat transaksi baru).
     */
    public function checkDigiflazz(Order $order, DigiflazzService $digiflazz)
    {
        if (!$order->product || !$order->digiflazz_trx_id) {
            return back()->with('error', 'Tidak ada ref Digiflazz untuk dicek.');
        }

        $resp = $digiflazz->checkTransaction(
            refId: (string) $order->digiflazz_trx_id,
            buyerSkuCode: (string) $order->product->buyer_sku_code,
            customerNo: trim((string) $order->target),
            isPostpaid: false // FIX: camelCase sesuai signature
        );

        $dfStatus  = (string) data_get($resp, 'data.status', '');
        $dfRc      = (string) data_get($resp, 'data.rc', '');
        $dfMessage = (string) data_get($resp, 'data.message', '');
        $dfSn      = (string) data_get($resp, 'data.sn', '');

        DB::transaction(function () use ($order, $resp, $dfStatus, $dfRc, $dfMessage, $dfSn) {
            $order->digiflazz_response = $resp;
            $order->digiflazz_status  = $dfStatus ?: null;
            $order->digiflazz_rc      = $dfRc ?: null;
            $order->digiflazz_message = $dfMessage ?: null;

            if ($dfSn !== '') {
                $order->digiflazz_sn = $dfSn;

                if (isset($order->digiflazz_token)) {
                    $tokenOnly = trim(explode('/', $dfSn)[0] ?? '');
                    if ($tokenOnly !== '') $order->digiflazz_token = $tokenOnly;
                }
            }

            if (strcasecmp($dfStatus, 'Sukses') === 0) {
                $order->status = 'fulfilled';
            } elseif (strcasecmp($dfStatus, 'Gagal') === 0) {
                $order->status = 'failed';
            } else {
                $order->status = 'processing';
            }

            $order->save();
        });

        return back()->with('success', 'Status Digiflazz berhasil dicek.');
    }

    public function markFailed(Order $order)
    {
        $order->status = 'failed';
        $order->save();

        return back()->with('success', 'Order ditandai failed.');
    }
}
