@extends('layouts.admin')

@section('title', 'Orders')
@section('page_title', 'Orders')
@section('page_subtitle', 'Daftar & kelola semua transaksi')

@section('content')
<style>
    /* ── FILTER BAR ──────────────────────────────────── */
    .filter-bar {
        background: #fff; border: 1px solid var(--border);
        border-radius: 14px; padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 1px 4px rgba(1,41,77,0.04);
        display: flex; align-items: flex-end; gap: .75rem; flex-wrap: wrap;
    }
    .filter-group { display: flex; flex-direction: column; gap: .3rem; }
    .filter-group label { font-size: .75rem; font-weight: 700; color: var(--gray); text-transform: uppercase; letter-spacing: .04em; }

    .filter-select {
        height: 38px; padding: 0 .875rem;
        border: 1.5px solid var(--border); border-radius: 9px;
        font-family: 'Plus Jakarta Sans', sans-serif; font-size: .8125rem;
        color: var(--navy); background: #f8fafc;
        transition: border-color .2s, box-shadow .2s; min-width: 160px;
    }
    .filter-select:focus { outline: none; border-color: var(--cyan); box-shadow: 0 0 0 3px rgba(3,174,198,0.10); }

    .search-wrap { position: relative; flex: 1; min-width: 220px; }
    .search-wrap i { position: absolute; left: .875rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: .825rem; pointer-events: none; }
    .search-input {
        width: 100%; height: 38px; padding: 0 1rem 0 2.5rem;
        border: 1.5px solid var(--border); border-radius: 9px;
        font-family: 'Plus Jakarta Sans', sans-serif; font-size: .8125rem;
        color: var(--navy); background: #f8fafc;
        transition: border-color .2s, box-shadow .2s;
    }
    .search-input:focus { outline: none; border-color: var(--cyan); box-shadow: 0 0 0 3px rgba(3,174,198,0.10); background: #fff; }
    .search-input::placeholder { color: #94a3b8; }

    .btn-filter {
        height: 38px; padding: 0 1.125rem; border-radius: 9px;
        background: linear-gradient(135deg, var(--cyan), var(--cyan2));
        color: #fff; border: none; font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: .8rem; font-weight: 700; cursor: pointer;
        display: inline-flex; align-items: center; gap: .375rem;
        transition: box-shadow .2s; white-space: nowrap;
    }
    .btn-filter:hover { box-shadow: 0 4px 12px rgba(3,174,198,0.30); }

    .btn-reset-filter {
        height: 38px; padding: 0 1rem; border-radius: 9px;
        border: 1.5px solid var(--border); background: #fff;
        color: var(--gray); font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: .8rem; font-weight: 600; cursor: pointer;
        display: inline-flex; align-items: center; gap: .375rem;
        text-decoration: none; transition: border-color .2s, color .2s; white-space: nowrap;
    }
    .btn-reset-filter:hover { border-color: var(--navy); color: var(--navy); }

    /* ── RESULT COUNT ────────────────────────────────── */
    .result-meta {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: .875rem; flex-wrap: wrap; gap: .5rem;
    }
    .result-count { font-size: .8125rem; color: var(--gray); font-weight: 500; }
    .result-count strong { color: var(--navy); }

    /* active filter badge */
    .active-filter {
        display: inline-flex; align-items: center; gap: .3rem;
        background: rgba(3,174,198,0.10); border: 1px solid rgba(3,174,198,0.25);
        color: var(--cyan2); border-radius: 999px; padding: .2rem .625rem;
        font-size: .7rem; font-weight: 700;
    }

    /* ── ORDER CARDS ─────────────────────────────────── */
    .order-card {
        background: #fff; border-radius: 12px;
        border: 1px solid var(--border);
        box-shadow: 0 1px 6px rgba(1,41,77,0.05);
        margin-bottom: .75rem; overflow: hidden;
        transition: box-shadow .2s, border-color .2s;
    }
    .order-card:hover { box-shadow: 0 4px 16px rgba(3,174,198,0.09); border-color: rgba(3,174,198,0.2); }

    .order-strip { height: 3px; }
    .strip-fulfilled      { background: linear-gradient(90deg,#16a34a,#22c55e); }
    .strip-paid           { background: linear-gradient(90deg,var(--cyan),var(--cyan2)); }
    .strip-processing     { background: linear-gradient(90deg,#d97706,#fbbf24); }
    .strip-failed         { background: linear-gradient(90deg,#dc2626,#f87171); }
    .strip-pending_payment{ background: linear-gradient(90deg,#94a3b8,#cbd5e0); }
    .strip-pending        { background: linear-gradient(90deg,#94a3b8,#cbd5e0); }

    .order-body {
        padding: .875rem 1.125rem;
        display: grid;
        grid-template-columns: minmax(0,1fr) minmax(0,1fr) minmax(0,1fr) auto;
        gap: 1rem; align-items: center;
    }

    /* col blocks */
    .col-order {}
    .col-user {}
    .col-product {}
    .col-actions { display: flex; flex-direction: column; align-items: flex-end; gap: .5rem; flex-shrink: 0; }

    .order-id { font-size: .75rem; font-weight: 700; color: var(--navy); font-family: 'Courier New', monospace; margin-bottom: .15rem; }
    .order-meta-line { font-size: .7rem; color: #94a3b8; font-weight: 500; }

    .user-name  { font-size: .8125rem; font-weight: 700; color: var(--navy); margin-bottom: .15rem; }
    .user-email { font-size: .7rem; color: var(--gray); }

    .prod-name   { font-size: .8125rem; font-weight: 700; color: var(--navy); margin-bottom: .25rem;
                   display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
    .prod-target { font-size: .7rem; color: var(--gray); }

    .order-amount {
        font-family: 'Poppins', sans-serif;
        font-size: 1rem; font-weight: 800; color: var(--cyan);
    }

    /* badges */
    .badge {
        display: inline-flex; align-items: center; gap: .25rem;
        padding: .2rem .55rem; border-radius: 999px;
        font-size: .675rem; font-weight: 700;
    }
    .badge-fulfilled      { background: #f0fdf4; color: #16a34a; }
    .badge-paid           { background: rgba(3,174,198,0.10); color: var(--cyan2); }
    .badge-processing     { background: #fffbeb; color: #d97706; }
    .badge-failed         { background: #fef2f2; color: #dc2626; }
    .badge-pending        { background: #f8fafc; color: #94a3b8; }
    .badge-pending_payment{ background: #f8fafc; color: #94a3b8; }

    .order-date { font-size: .7rem; color: var(--gray); font-weight: 500; text-align: right; }

    /* action btn */
    .btn-detail {
        display: inline-flex; align-items: center; gap: .3rem;
        padding: .375rem .875rem; border-radius: 8px;
        background: linear-gradient(135deg, var(--cyan), var(--cyan2));
        color: #fff; font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: .75rem; font-weight: 700; text-decoration: none;
        transition: box-shadow .2s; white-space: nowrap;
    }
    .btn-detail:hover { color: #fff; box-shadow: 0 4px 12px rgba(3,174,198,0.3); }

    /* ── EMPTY STATE ─────────────────────────────────── */
    .empty-state {
        text-align: center; padding: 4rem 2rem;
        background: #fff; border-radius: 14px;
        border: 1px solid var(--border);
    }
    .empty-icon {
        width: 72px; height: 72px; border-radius: 50%;
        background: #f8fafc; color: #94a3b8;
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; margin: 0 auto 1rem;
    }
    .empty-title { font-family: 'Syne', sans-serif; font-size: 1.125rem; font-weight: 800; color: var(--navy); margin: 0 0 .3rem; }
    .empty-sub { font-size: .875rem; color: var(--gray); }

    /* ── PAGINATION ──────────────────────────────────── */
    .pagi-wrap {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: .75rem; margin-top: 1.25rem;
        padding-top: 1.125rem; border-top: 1px solid var(--border);
    }
    .pagi-info { font-size: .8125rem; color: var(--gray); font-weight: 500; }
    .pagi-info strong { color: var(--navy); }

    .pagi-list {
        display: flex; align-items: center; gap: .3rem;
        list-style: none; padding: 0; margin: 0; flex-wrap: wrap;
    }
    .pagi-list li a,
    .pagi-list li span {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 2.25rem; height: 2.25rem; padding: 0 .625rem;
        border-radius: 8px; font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: .8rem; font-weight: 600; text-decoration: none !important;
        border: 1.5px solid var(--border); background: #fff;
        color: var(--navy) !important; transition: all .2s; cursor: pointer;
    }
    .pagi-list li a:hover {
        border-color: var(--cyan); color: var(--cyan) !important;
        background: rgba(3,174,198,0.06);
    }
    .pagi-list li.active span {
        background: linear-gradient(135deg, var(--cyan), var(--cyan2));
        border-color: var(--cyan); color: #fff !important;
        box-shadow: 0 3px 8px rgba(3,174,198,0.28); cursor: default;
    }
    .pagi-list li.disabled span {
        color: #cbd5e0 !important; border-color: #f1f5f9 !important;
        background: #f8fafc !important; cursor: not-allowed;
    }

    /* ── RESPONSIVE ──────────────────────────────────── */
    @media (max-width: 900px) {
        .order-body { grid-template-columns: 1fr 1fr; }
        .col-product { display: none; }
    }
    @media (max-width: 560px) {
        .order-body { grid-template-columns: 1fr auto; }
        .col-user { display: none; }
        .filter-bar { flex-direction: column; align-items: stretch; }
        .filter-group, .search-wrap { width: 100%; }
        .filter-group .filter-select { width: 100%; }
    }
</style>

{{-- ── FILTER BAR ──────────────────────────────────── --}}
<form method="GET" action="{{ route('admin.orders.index') }}">
    <div class="filter-bar">
        <div class="filter-group">
            <label>Status</label>
            <select name="status" class="filter-select">
                <option value="">Semua Status</option>
                @foreach ([
                    'pending_payment' => 'Pending Payment',
                    'paid'            => 'Paid',
                    'processing'      => 'Processing',
                    'fulfilled'       => 'Fulfilled',
                    'failed'          => 'Failed',
                ] as $val => $label)
                    <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group" style="flex:1;">
            <label>Cari</label>
            <div class="search-wrap">
                <i class="fas fa-search"></i>
                <input type="text" name="keyword" class="search-input"
                       placeholder="Order ID, username, target, produk..."
                       value="{{ request('keyword') }}">
            </div>
        </div>

        <div style="display:flex;gap:.5rem;align-self:flex-end;">
            <button type="submit" class="btn-filter">
                <i class="fas fa-filter"></i> Filter
            </button>
            <a href="{{ route('admin.orders.index') }}" class="btn-reset-filter">
                <i class="fas fa-rotate-right"></i> Reset
            </a>
        </div>
    </div>
</form>

{{-- ── RESULT META ─────────────────────────────────── --}}
<div class="result-meta">
    <span class="result-count">
        Menampilkan <strong>{{ $orders->firstItem() ?? 0 }}–{{ $orders->lastItem() ?? 0 }}</strong>
        dari <strong>{{ $orders->total() }}</strong> order
    </span>
    @if(request('status') || request('keyword'))
        <div style="display:flex;align-items:center;gap:.5rem;">
            @if(request('status'))
                <span class="active-filter"><i class="fas fa-tag"></i> {{ request('status') }}</span>
            @endif
            @if(request('keyword'))
                <span class="active-filter"><i class="fas fa-search"></i> "{{ Str::limit(request('keyword'), 20) }}"</span>
            @endif
        </div>
    @endif
</div>

{{-- ── ORDER LIST ───────────────────────────────────── --}}
@forelse($orders as $order)
    @php
        $status = $order->status;
        $stripClass = 'strip-' . $status;
        $badgeClass = 'badge-' . $status;
        $badgeIcon  = match($status) {
            'fulfilled'       => 'fas fa-check-circle',
            'paid'            => 'fas fa-credit-card',
            'processing'      => 'fas fa-spinner',
            'failed'          => 'fas fa-times-circle',
            default           => 'fas fa-clock',
        };
        $badgeLabel = match($status) {
            'fulfilled'       => 'Fulfilled',
            'paid'            => 'Paid',
            'processing'      => 'Processing',
            'failed'          => 'Failed',
            'pending_payment' => 'Pending',
            default           => ucfirst($status),
        };
    @endphp

    <div class="order-card">
        <div class="order-strip {{ $stripClass }}"></div>
        <div class="order-body">

            {{-- Col 1: Order info --}}
            <div class="col-order">
                <div class="order-id">#{{ $order->midtrans_order_id }}</div>
                <div class="order-meta-line" style="margin-bottom:.375rem;">
                    ID: {{ $order->id }}
                    @if($order->digiflazz_trx_id)
                        · DGF: {{ $order->digiflazz_trx_id }}
                    @endif
                </div>
                <span class="badge {{ $badgeClass }}">
                    <i class="{{ $badgeIcon }}"></i> {{ $badgeLabel }}
                </span>
            </div>

            {{-- Col 2: User --}}
            <div class="col-user">
                <div class="user-name">{{ $order->user?->username ?? '—' }}</div>
                <div class="user-email">{{ $order->user?->email ?? '—' }}</div>
            </div>

            {{-- Col 3: Product + target --}}
            <div class="col-product">
                <div class="prod-name">{{ $order->product?->product_name ?? '—' }}</div>
                <div class="prod-target"><i class="fas fa-crosshairs" style="color:#94a3b8;font-size:.65rem;margin-right:.25rem;"></i>{{ $order->target }}</div>
            </div>

            {{-- Col 4: Actions --}}
            <div class="col-actions">
                <div class="order-amount">Rp {{ number_format((int)$order->gross_amount, 0, ',', '.') }}</div>
                <div class="order-date">
                    {{ $order->created_at->format('d M Y') }}<br>
                    <span style="color:#94a3b8;">{{ $order->created_at->format('H:i') }} WIB</span>
                </div>
                <a href="{{ route('admin.orders.show', $order) }}" class="btn-detail">
                    <i class="fas fa-eye"></i> Detail
                </a>
            </div>

        </div>
    </div>
@empty
    <div class="empty-state">
        <div class="empty-icon"><i class="fas fa-receipt"></i></div>
        <h3 class="empty-title">Tidak Ada Order</h3>
        <p class="empty-sub">
            @if(request('status') || request('keyword'))
                Tidak ada order yang cocok dengan filter yang dipilih.
            @else
                Belum ada transaksi masuk.
            @endif
        </p>
    </div>
@endforelse

{{-- ── PAGINATION (custom, tanpa default Tailwind/Bootstrap) --}}
@if($orders->hasPages())
    <div class="pagi-wrap">
        <span class="pagi-info">
            Halaman <strong>{{ $orders->currentPage() }}</strong>
            dari <strong>{{ $orders->lastPage() }}</strong>
        </span>

        <ul class="pagi-list">
            {{-- Prev --}}
            <li class="{{ $orders->onFirstPage() ? 'disabled' : '' }}">
                @if($orders->onFirstPage())
                    <span title="Halaman pertama">
                        <i class="fas fa-chevron-left" style="font-size:.65rem;"></i>
                    </span>
                @else
                    <a href="{{ $orders->previousPageUrl() }}" rel="prev" title="Sebelumnya">
                        <i class="fas fa-chevron-left" style="font-size:.65rem;"></i>
                    </a>
                @endif
            </li>

            {{-- Page numbers (max tampil 5 di tengah) --}}
            @php
                $current  = $orders->currentPage();
                $last     = $orders->lastPage();
                $start    = max(1, $current - 2);
                $end      = min($last, $current + 2);
                // Selalu tampilkan minimal 5 halaman jika tersedia
                if ($end - $start < 4) {
                    if ($start === 1) $end = min($last, 5);
                    else $start = max(1, $end - 4);
                }
            @endphp

            @if($start > 1)
                <li>
                    <a href="{{ $orders->url(1) }}">1</a>
                </li>
                @if($start > 2)
                    <li><span style="border:none;background:none;color:#94a3b8;min-width:auto;padding:0 .25rem;">…</span></li>
                @endif
            @endif

            @for($p = $start; $p <= $end; $p++)
                <li class="{{ $p === $current ? 'active' : '' }}">
                    @if($p === $current)
                        <span>{{ $p }}</span>
                    @else
                        <a href="{{ $orders->url($p) }}">{{ $p }}</a>
                    @endif
                </li>
            @endfor

            @if($end < $last)
                @if($end < $last - 1)
                    <li><span style="border:none;background:none;color:#94a3b8;min-width:auto;padding:0 .25rem;">…</span></li>
                @endif
                <li>
                    <a href="{{ $orders->url($last) }}">{{ $last }}</a>
                </li>
            @endif

            {{-- Next --}}
            <li class="{{ !$orders->hasMorePages() ? 'disabled' : '' }}">
                @if($orders->hasMorePages())
                    <a href="{{ $orders->nextPageUrl() }}" rel="next" title="Selanjutnya">
                        <i class="fas fa-chevron-right" style="font-size:.65rem;"></i>
                    </a>
                @else
                    <span title="Halaman terakhir">
                        <i class="fas fa-chevron-right" style="font-size:.65rem;"></i>
                    </span>
                @endif
            </li>
        </ul>
    </div>
@endif

@endsection