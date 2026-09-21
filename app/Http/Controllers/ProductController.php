<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\DigiflazzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // tetap support q (punyamu) + support search (biar view yang lama gak error)
        $q = trim((string) ($request->query('q', $request->query('search', ''))));

        // filter kategori utama
        $category = trim((string) $request->query('category', ''));

        // kategori utama (label => value DB)
        $mainCategories = [
            'Pulsa'   => 'Pulsa',
            'PLN'     => 'PLN',
            'E-Money' => 'E-Money',
            'Games'   => 'Games',
            'Data'    => 'Data',
        ];

        $products = Product::query()
            ->active()
            ->when($category !== '' && in_array($category, array_values($mainCategories), true), function ($query) use ($category) {
                $query->where('category', $category);
            })
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('product_name', 'like', "%{$q}%")
                        ->orWhere('brand', 'like', "%{$q}%")
                        ->orWhere('category', 'like', "%{$q}%")
                        ->orWhere('buyer_sku_code', 'like', "%{$q}%");
                });
            })
            ->orderBy('category')
            ->orderBy('brand')
            ->orderBy('product_name')
            ->paginate(8)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'q' => $q,
            'category' => $category,
            'mainCategories' => $mainCategories,
        ]);
    }

    public function show(Product $product)
    {
        abort_unless((bool) $product->is_active, 404);
        return view('products.show', compact('product'));
    }

    public function plnInquiry(Request $request, Product $product, DigiflazzService $digiflazz)
    {
        abort_unless((bool) $product->is_active, 404);
        abort_unless($product->target_type === 'pln', 404);

        $data = $request->validate([
            'customer_no' => ['required', 'string', 'regex:/^[0-9]+$/', 'min:6', 'max:30'],
        ], [
            'customer_no.regex' => 'ID PLN hanya boleh angka.',
        ]);

        $customerNo = trim((string) $data['customer_no']);

        try {
            $resp = $digiflazz->inquiryPln($customerNo);

            $rc           = (string) data_get($resp, 'data.rc', '');
            $status       = (string) data_get($resp, 'data.status', '');
            $message      = (string) data_get($resp, 'data.message', '');
            $name         = (string) data_get($resp, 'data.name', '');
            $segmentPower = data_get($resp, 'data.segment_power');

            if ($rc !== '00' || $name === '') {
                return response()->json([
                    'ok' => false,
                    'message' => $message !== '' ? $message : 'Gagal cek PLN. Pastikan ID benar.',
                    'raw' => $resp,
                ], 422);
            }

            $key = $this->plnSessionKey($product->id, $customerNo);

            session()->put($key, [
                'customer_no'   => $customerNo,
                'name'          => $name,
                'segment_power' => $segmentPower ? (string) $segmentPower : null,
                'rc'            => $rc,
                'status'        => $status,
                'message'       => $message,
                'saved_at'      => now()->toDateTimeString(),
            ]);

            return response()->json([
                'ok' => true,
                'data' => [
                    'customer_no'   => $customerNo,
                    'name'          => $name,
                    'segment_power' => $segmentPower ? (string) $segmentPower : null,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('PLN_INQUIRY_ERROR', [
                'product_id'  => $product->id,
                'customer_no' => $customerNo,
                'err'         => $e->getMessage(),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Gagal cek PLN. Coba lagi beberapa saat.',
            ], 500);
        }
    }

    public function previewStore(Request $request, Product $product)
    {
        abort_unless((bool) $product->is_active, 404);

        // PHONE
        if ($product->target_type === 'phone') {
            $data = $request->validate([
                'target' => ['required','string','min:8','max:20','regex:/^[0-9]+$/'],
            ], ['target.regex' => 'Input hanya boleh angka.']);

            $target = trim($data['target']);

            $request->session()->put($this->previewKey($product->id), [
                'target' => $target,
                'ml_user_id' => null,
                'ml_server_id' => null,
            ]);

            return redirect()->route('products.preview.get', $product);
        }

        // PLN
        if ($product->target_type === 'pln') {
            $data = $request->validate([
                'target' => ['required','string','min:6','max:30','regex:/^[0-9]+$/'],
            ], ['target.regex' => 'Input hanya boleh angka.']);

            $target = trim($data['target']);

            $plnKey = $this->plnSessionKey($product->id, $target);
            $plnInquiry = session()->get($plnKey);

            if (!$plnInquiry) {
                return redirect()
                    ->route('products.show', $product)
                    ->withInput(['target' => $target])
                    ->with('error', 'Untuk PLN Token, wajib klik "Cek PLN" dulu agar nama pelanggan tervalidasi.');
            }

            $request->session()->put($this->previewKey($product->id), [
                'target' => $target,
                'ml_user_id' => null,
                'ml_server_id' => null,
            ]);

            return redirect()->route('products.preview.get', $product);
        }

        // MOBILE LEGENDS
        if ($product->target_type === 'ml') {
            $data = $request->validate([
                'ml_user_id'   => ['required','string','min:5','max:15','regex:/^[0-9]+$/'],
                'ml_server_id' => ['required','string','min:3','max:8','regex:/^[0-9]+$/'],
            ], [
                'ml_user_id.regex' => 'User ID hanya boleh angka.',
                'ml_server_id.regex' => 'Server ID hanya boleh angka.',
            ]);

            $mlUserId = trim($data['ml_user_id']);
            $mlServerId = trim($data['ml_server_id']);
            $target = $mlUserId . '|' . $mlServerId;

            $request->session()->put($this->previewKey($product->id), [
                'target' => $target,
                'ml_user_id' => $mlUserId,
                'ml_server_id' => $mlServerId,
            ]);

            return redirect()->route('products.preview.get', $product);
        }

        // DEFAULT
        $data = $request->validate([
            'target' => ['required','string','min:6','max:30','regex:/^[0-9]+$/'],
        ], ['target.regex' => 'Input hanya boleh angka.']);

        $target = trim($data['target']);

        $request->session()->put($this->previewKey($product->id), [
            'target' => $target,
            'ml_user_id' => null,
            'ml_server_id' => null,
        ]);

        return redirect()->route('products.preview.get', $product);
    }

    public function previewPage(Request $request, Product $product, DigiflazzService $digiflazz)
    {
        abort_unless((bool) $product->is_active, 404);

        $preview = $request->session()->get($this->previewKey($product->id));
        if (!$preview || empty($preview['target'])) {
            return redirect()->route('products.show', $product)->with('error', 'Silakan input tujuan dulu.');
        }

        $target = (string) $preview['target'];

        $plnInquiry = null;
        if ($product->target_type === 'pln') {
            $plnInquiry = session()->get($this->plnSessionKey($product->id, $target));
        }

        // CEK SALDO DIGIFLAZZ (UNTUK UI)
        $providerPrice = (int) ($product->digiflazz_price ?? 0);
        $balance = null;
        $canBuy = true;

        try {
            $balance = $digiflazz->getBalanceValueCached(30);
            // fail-safe:
            $canBuy = ($balance !== null && $providerPrice > 0 && $balance >= $providerPrice);
        } catch (\Throwable $e) {
            $canBuy = false; // fail-safe
        }

        return view('products.preview', [
            'product' => $product,
            'target' => $target,
            'gross_amount' => (int) $product->price,
            'pln_inquiry' => $plnInquiry,
            'ml_user_id' => $preview['ml_user_id'] ?? null,
            'ml_server_id' => $preview['ml_server_id'] ?? null,

            // untuk UI
            'can_buy' => $canBuy,
            'provider_balance' => $balance,       // saldo provider
            'provider_price' => $providerPrice,   // harga provider
        ]);
    }


    private function previewKey(int $productId): string
    {
        return "preview.{$productId}";
    }

    private function plnSessionKey(int $productId, string $customerNo): string
    {
        return "pln_inquiry.{$productId}." . trim($customerNo);
    }
}
