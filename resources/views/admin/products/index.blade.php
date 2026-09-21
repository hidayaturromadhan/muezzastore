@extends('layouts.admin')

@section('title', 'Daftar Produk')
@section('page_title', 'Products')
@section('page_subtitle', 'Manajemen harga jual dan sinkronisasi provider')

@section('header_actions')
<form action="{{ route('admin.products.sync') }}" method="POST" onsubmit="return confirm('Sinkronisasi mungkin memakan waktu. Lanjutkan?')">
    @csrf
    <button class="btn btn-cyan btn-sm" type="submit">
        <i class="fas fa-sync-alt"></i> Sync Digiflazz
    </button>
</form>
@endsection

@section('content')

{{-- Filter Card --}}
<div class="card mb-4">
    <div class="card-body">
        <form class="row g-3" method="GET" action="{{ route('admin.products.index') }}">
            <div class="col-12 col-md-3">
                <label class="form-label">Status Produk</label>
                <select name="active" class="form-select">
                    <option value="">-- Semua Status --</option>
                    <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label">Cari Produk</label>
                <div class="input-group" style="display: flex; gap: 0;">
                    <input type="text" name="keyword" class="form-control"
                           value="{{ request('keyword') }}"
                           placeholder="Ketik nama produk, SKU, atau brand..."
                           style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                    <button class="btn btn-cyan" type="submit" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <div class="col-12 col-md-3 d-flex align-items-end gap-2">
                <a href="{{ route('admin.products.index') }}" class="btn btn-ghost w-100">
                    <i class="fas fa-undo"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Table Card --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h3 class="card-title mb-0">Database Produk</h3>

        <div class="text-muted small">
            Total: <span class="fw-semibold">{{ $products->total() }}</span>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-wrap table-responsive">
            <table class="admin-table admin-table-fixed">
                <thead>
                    <tr>
                        <th class="col-id text-center">ID</th>
                        <th class="col-info">Info Produk</th>
                        <th class="col-sku">SKU & Brand</th>
                        <th class="col-price text-end">Harga Provider</th>
                        <th class="col-price text-end">Harga Jual</th>
                        <th class="col-status text-center">Status</th>
                        <th class="col-action text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $p)
                    <tr>
                        <td class="text-center text-muted small align-middle">{{ $p->id }}</td>

                        {{-- Info Produk --}}
                        <td class="align-middle">
                            <div class="d-flex gap-3 align-items-center">
                                <div class="thumb-wrap">
                                    @if($p->image)
                                        <img src="{{ asset($p->image) }}"
                                             class="thumb-img"
                                             alt="img">
                                    @else
                                        <div class="thumb-noimg">
                                            No Img
                                        </div>
                                    @endif
                                </div>

                                <div class="min-w-0">
                                    <div class="fw-bold text-navy text-truncate-2">{{ $p->product_name }}</div>
                                    <div class="mt-1">
                                        <span class="badge badge-gray badge-pill">{{ $p->category }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- SKU & Brand --}}
                        <td class="align-middle">
                            <div class="sku-wrap">
                                <code class="sku-code">{{ $p->buyer_sku_code }}</code>
                                <div class="small text-muted mt-1 text-truncate-1">
                                    <i class="fas fa-tag me-1"></i>{{ $p->brand }}
                                </div>
                            </div>
                        </td>

                        {{-- Harga Provider --}}
                        <td class="text-end text-muted small align-middle">
                            Rp {{ number_format((int)$p->digiflazz_price, 0, ',', '.') }}
                        </td>

                        {{-- Harga Jual --}}
                        <td class="text-end align-middle">
                            <span class="fw-bold text-cyan" style="font-size: 0.95rem;">
                                Rp {{ number_format((int)$p->price, 0, ',', '.') }}
                            </span>
                            @php $margin = (int)$p->price - (int)$p->digiflazz_price; @endphp
                            <div class="{{ $margin >= 0 ? 'text-success' : 'text-danger' }} small" style="font-size: 0.75rem;">
                                {{ $margin >= 0 ? '+' : '-' }} Rp {{ number_format(abs($margin), 0, ',', '.') }}
                            </div>
                        </td>

                        {{-- Status --}}
                        <td class="text-center align-middle">
                            @if($p->is_active)
                                <span class="badge badge-green badge-pill">
                                    <i class="fas fa-check-circle me-1"></i> Aktif
                                </span>
                            @else
                                <span class="badge badge-red badge-pill">
                                    <i class="fas fa-times-circle me-1"></i> Off
                                </span>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="text-end align-middle">
                            <div class="d-flex justify-content-end gap-2 action-wrap">
                                <a href="{{ route('admin.products.edit', $p) }}"
                                   class="btn btn-ghost btn-sm"
                                   title="Edit Harga/Gambar">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('admin.products.toggle', $p) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm {{ $p->is_active ? 'btn-danger' : 'btn-outline-cyan' }}"
                                            type="submit"
                                            title="{{ $p->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="fas {{ $p->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center p-5">
                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png"
                                 style="width: 80px; opacity: 0.2;" alt="empty">
                            <div class="mt-3 text-muted">Data produk tidak ditemukan atau database kosong.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4 d-flex justify-content-center">
    <div class="pagi-container">
        {{ $products->appends(request()->query())->links('pagination::bootstrap-4') }}
    </div>
</div>

@endsection

@push('styles')
<style>
    /* ── TABLE FIX: biar kolom rapi & tidak ngembang ───────────── */
    .admin-table-fixed {
        width: 100%;
        table-layout: fixed; /* KUNCI biar grid table stabil */
    }
    .admin-table-fixed th,
    .admin-table-fixed td {
        vertical-align: middle;
        word-wrap: break-word;
        overflow-wrap: anywhere;
    }

    /* Lebarkan table untuk desktop tapi tetap bisa scroll di mobile */
    .table-wrap.table-responsive {
        overflow-x: auto;
    }
    .admin-table-fixed {
        min-width: 980px; /* biar tidak gepeng di desktop, mobile auto scroll */
    }

    /* Column widths (stabil) */
    .admin-table-fixed .col-id { width: 60px; }
    .admin-table-fixed .col-info { width: 380px; }
    .admin-table-fixed .col-sku { width: 200px; }
    .admin-table-fixed .col-price { width: 160px; }
    .admin-table-fixed .col-status { width: 130px; }
    .admin-table-fixed .col-action { width: 120px; }

    /* Thumbnail */
    .thumb-wrap { flex-shrink: 0; }
    .thumb-img {
        width: 52px; height: 52px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid rgba(0,0,0,.08);
        box-shadow: 0 2px 8px rgba(0,0,0,.06);
        background: #fff;
    }
    .thumb-noimg {
        width: 52px; height: 52px;
        border-radius: 12px;
        border: 1px dashed rgba(0,0,0,.18);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        color: rgba(0,0,0,.45);
        background: rgba(0,0,0,.02);
    }

    /* Clamp text */
    .min-w-0 { min-width: 0; }
    .text-truncate-1 {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Badge pill (biar seragam) */
    .badge-pill { border-radius: 999px; }

    /* SKU wrap */
    .sku-code {
        display: inline-block;
        max-width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Actions */
    .action-wrap .btn { min-width: 38px; } /* biar tombol aksi gak bikin kolom melebar */

    /* Pagination */
    .pagi-container .pagination {
        display: flex;
        gap: 6px;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .pagi-container .page-item .page-link {
        border-radius: 10px !important;
        border: 1px solid var(--border);
        color: var(--navy);
        padding: 8px 14px;
        line-height: 1;
    }
    .pagi-container .page-item.active .page-link {
        background: var(--cyan);
        border-color: var(--cyan);
        color: white;
    }
</style>
@endpush
