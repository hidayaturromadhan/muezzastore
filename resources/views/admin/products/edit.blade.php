@extends('layouts.admin')

@section('title', 'Edit Product - ' . $product->product_name)
@section('page_title', 'Edit Product')
@section('page_subtitle', 'Konfigurasi harga jual dan aset visual produk')

@section('header_actions')
<a href="{{ route('admin.products.index') }}" class="btn btn-ghost btn-sm">
    <i class="fas fa-arrow-left"></i> Kembali
</a>
@endsection

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
        @csrf
        
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; align-items: start;">
            
            {{-- Sisi Kiri: Preview Gambar --}}
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Media</h3>
                    </div>
                    <div class="card-body text-center">
                        <div id="image-preview-container" style="position: relative; width: 100%; aspect-ratio: 1/1; background: #f8fafc; border-radius: 12px; overflow: hidden; border: 2px dashed var(--border); display: flex; align-items: center; justify-content: center;">
                            @if($product->image)
                                <img src="{{ asset($product->image) }}" id="preview-img" style="width: 100%; height: 100%; object-fit: cover;" alt="product image">
                            @else
                                <div id="placeholder-text" class="text-muted" style="text-align: center;">
                                    <i class="fas fa-image fa-3x mb-2" style="opacity: 0.3;"></i>
                                    <div class="small">No Image</div>
                                </div>
                                <img src="" id="preview-img" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                            @endif
                        </div>

                        @if($product->image)
                            <button type="button" class="btn btn-danger btn-sm w-100 mt-3" onclick="confirmDeleteImage()">
                                <i class="fas fa-trash"></i> Hapus Gambar
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Info Status Provider --}}
                <div class="card">
                    <div class="card-body" style="padding: 1rem;">
                        <div class="text-muted small mb-1">Provider Status</div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge badge-green">Connected</span>
                            <span class="small fw-semibold">{{ $product->brand }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sisi Kanan: Form --}}
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Detail & Harga</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h4 style="font-size: 1.1rem; color: var(--navy); margin-bottom: 0.25rem;">{{ $product->product_name }}</h4>
                            <div class="text-muted small">
                                SKU: <code class="text-cyan">{{ $product->buyer_sku_code }}</code> | Category: {{ $product->category }}
                            </div>
                        </div>

                        <hr style="border: 0; border-top: 1px solid var(--border); margin: 1.5rem 0;">

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="mb-3">
                                <label class="form-label">Harga Modal (Provider)</label>
                                <div class="input-group" style="display: flex;">
                                    <span style="background: #f1f5f9; border: 2px solid var(--border); border-right: 0; padding: 0 0.75rem; display: flex; align-items: center; border-radius: 10px 0 0 10px; font-size: 0.8rem; font-weight: 700; color: var(--gray);">Rp</span>
                                    <input type="text" class="form-control" style="border-radius: 0 10px 10px 0; background: #f8fafc;" 
                                           value="{{ number_format((int)$product->digiflazz_price, 0, ',', '.') }}" disabled>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Harga Jual</label>
                                <div class="input-group" style="display: flex;">
                                    <span style="background: var(--cyan); border: 2px solid var(--cyan); border-right: 0; padding: 0 0.75rem; display: flex; align-items: center; border-radius: 10px 0 0 10px; font-size: 0.8rem; font-weight: 700; color: white;">Rp</span>
                                    <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" 
                                           style="border-radius: 0 10px 10px 0;"
                                           value="{{ old('price', (int)$product->price) }}" min="0" required>
                                </div>
                                @error('price') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status Visibilitas</label>
                            <select name="is_active" class="form-select">
                                <option value="1" {{ old('is_active', (int)$product->is_active) == 1 ? 'selected' : '' }}>Aktif (Tampil di Website)</option>
                                <option value="0" {{ old('is_active', (int)$product->is_active) == 0 ? 'selected' : '' }}>Nonaktif (Sembunyikan)</option>
                            </select>
                        </div>

                        <div class="mb-0">
                            <label class="form-label">Ganti Gambar Produk</label>
                            <input type="file" name="image" id="image-input" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                            <div class="text-muted mt-2" style="font-size: 0.75rem;">
                                <i class="fas fa-info-circle"></i> Gunakan rasio 1:1 untuk tampilan terbaik di katalog.
                            </div>
                            @error('image') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="card-body" style="background: #f8fafc; border-top: 1px solid var(--border); border-radius: 0 0 16px 16px; display: flex; gap: 0.75rem; justify-content: flex-end;">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-ghost">Batal</a>
                        <button type="submit" class="btn btn-cyan">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Hidden Form untuk Hapus Gambar --}}
@if($product->image)
<form id="delete-image-form" action="{{ route('admin.products.deleteImage', $product) }}" method="POST" style="display:none;">
    @csrf
</form>
@endif

@endsection

@push('scripts')
<script>
    // Preview Gambar saat upload
    const imageInput = document.getElementById('image-input');
    const previewImg = document.getElementById('preview-img');
    const placeholderText = document.getElementById('placeholder-text');

    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
                if (placeholderText) placeholderText.style.display = 'none';
            }
            reader.readAsDataURL(file);
        }
    });

    function confirmDeleteImage() {
        if (confirm('Apakah Anda yakin ingin menghapus gambar produk ini?')) {
            document.getElementById('delete-image-form').submit();
        }
    }
</script>
@endpush