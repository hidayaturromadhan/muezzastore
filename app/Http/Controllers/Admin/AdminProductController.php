<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $q = Product::query();

        if ($request->filled('active')) {
            $q->where('is_active', (bool) $request->input('active'));
        }

        if ($request->filled('keyword')) {
            $kw = $request->string('keyword');
            $q->where(function ($w) use ($kw) {
                $w->where('product_name', 'like', "%{$kw}%")
                  ->orWhere('buyer_sku_code', 'like', "%{$kw}%")
                  ->orWhere('brand', 'like', "%{$kw}%");
            });
        }

        $products = $q->latest()->paginate(30)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'price' => ['required','integer','min:0'],
            'is_active' => ['required','in:0,1'],
            'image' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'], // max 2MB
        ]);

        // Update data dasar
        $product->price = (int) $data['price'];
        $product->is_active = (bool) ((int) $data['is_active']);

        // Upload image jika ada
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            // Pastikan folder ada
            $dir = public_path('images');
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            // Hapus file lama jika ada
            if ($product->image) {
                $oldPath = public_path($product->image);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            // Nama file aman & unik
            $ext = $file->getClientOriginalExtension();
            $safeName = 'prd_' . $product->id . '_' . now()->format('YmdHis') . '_' . uniqid() . '.' . $ext;

            // Simpan ke public/images
            $file->move($dir, $safeName);

            // Simpan path relatif biar gampang dipanggil di view
            $product->image = 'images/' . $safeName;
        }

        $product->save();

        return redirect()->route('admin.products.edit', $product)
            ->with('success', 'Produk berhasil diupdate.');
    }

    public function deleteImage(Product $product)
    {
        if ($product->image) {
            $path = public_path($product->image);
            if (File::exists($path)) {
                File::delete($path);
            }
            $product->image = null;
            $product->save();
        }

        return back()->with('success', 'Gambar produk dihapus.');
    }

    public function toggleActive(Product $product)
    {
        $product->is_active = !$product->is_active;
        $product->save();

        return back()->with('success', 'Status produk diubah.');
    }

    public function syncDigiflazz()
    {
        // biarkan sesuai implementasi kamu yang sudah jalan
        // contoh:
        // SyncDigiflazzProductsJob::dispatch();
        return back()->with('success', 'Sync Digiflazz berhasil diproses (cek queue/log).');
    }
}
