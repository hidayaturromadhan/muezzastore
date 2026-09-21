<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\DigiflazzService;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function pay(Request $request, Product $product, MidtransService $midtrans, DigiflazzService $digiflazz)
    {
        abort_unless((bool) $product->is_active, 404);

        $user = $request->user();

        // VALIDASI target sesuai target_type
        if ($product->target_type === 'phone') {
            $data = $request->validate([
                'target' => ['required','string','min:8','max:20','regex:/^[0-9]+$/'],
            ], ['target.regex' => 'Nomor HP hanya boleh angka.']);

            $target = trim($data['target']);

        } elseif ($product->target_type === 'pln') {
            $data = $request->validate([
                'target' => ['required','string','min:6','max:30','regex:/^[0-9]+$/'],
            ], ['target.regex' => 'ID PLN hanya boleh angka.']);

            $target = trim($data['target']);

            // Anti-bypass: wajib ada session inquiry PLN
            $key = "pln_inquiry.{$product->id}.{$target}";
            $plnInquiry = $request->session()->get($key);
            if (!$plnInquiry) {
                return redirect()
                    ->route('products.show', $product)
                    ->with('error', 'Untuk PLN, kamu wajib klik "Cek PLN" dulu sebelum bayar.')
                    ->withInput(['target' => $target]);
            }

        } elseif ($product->target_type === 'ml') {
            // ML target format: user|server
            $data = $request->validate([
                'target' => ['required','string','regex:/^[0-9]{5,15}\|[0-9]{3,8}$/'],
            ], ['target.regex' => 'Format Mobile Legends salah. Gunakan UserID|ServerID.']);

            $target = trim($data['target']);

        } else {
            $data = $request->validate([
                'target' => ['required','string','min:6','max:30','regex:/^[0-9]+$/'],
            ], ['target.regex' => 'Input hanya boleh angka.']);

            $target = trim($data['target']);
        }

        // =============================
        // CEK SALDO DIGIFLAZZ DULU
        // =============================
        $providerPrice = (int) ($product->digiflazz_price ?? 0);

        // kalau belum ada harga provider, lebih aman blok
        if ($providerPrice <= 0) {
            return redirect()
                ->route('products.preview.get', $product)
                ->with('error', 'Produk belum punya harga provider. Hubungi admin.');
        }

        // cache 30 detik
        $balance = $digiflazz->getBalanceValueCached(30);

        // kalau saldo tidak bisa dibaca, block (fail-safe)
        if ($balance === null) {
            return redirect()
                ->route('products.preview.get', $product)
                ->with('error', 'Sedang tidak bisa memverifikasi saldo provider. Coba lagi sebentar.');
        }

        // saldo kurang → block
        if ($balance < $providerPrice) {
            return redirect()
                ->route('products.preview.get', $product)
                ->with('error', 'Maaf, saldo provider sedang tidak cukup untuk memproses produk ini. Silakan coba lagi nanti.');
        }

        // buat midtrans order id unik
        $midtransOrderId = 'ORD-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6));

        $order = Order::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'target' => $target,
            'gross_amount' => (int) $product->price,
            'status' => 'pending_payment',
            'midtrans_order_id' => $midtransOrderId,
        ]);

        $params = [
            'transaction_details' => [
                'order_id' => $order->midtrans_order_id,
                'gross_amount' => (int) $order->gross_amount,
            ],
            'customer_details' => [
                'first_name' => $user->username,
                'email' => $user->email,
            ],
            'item_details' => [
                [
                    'id' => $product->buyer_sku_code,
                    'price' => (int) $order->gross_amount,
                    'quantity' => 1,
                    'name' => $product->product_name,
                ],
            ],
            'callbacks' => [
                'finish' => route('payment.finish', $order),
            ],
        ];

        $snap = $midtrans->createSnapRedirect($params);

        if (empty($snap['redirect_url'])) {
            $order->status = 'failed';
            $order->midtrans_payload = $snap['raw'] ?? null;
            $order->save();

            return back()->withErrors(['payment' => 'Gagal membuat pembayaran Midtrans.']);
        }

        $order->midtrans_token = $snap['token'] ?? null;
        $order->midtrans_redirect_url = $snap['redirect_url'];
        $order->midtrans_payload = $snap['raw'] ?? null;
        $order->save();

        return redirect()->away($order->midtrans_redirect_url);
    }

    public function show(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        return view('orders.show', compact('order'));
    }

    public function finish(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        return view('payment.finish', compact('order'));
    }
}
