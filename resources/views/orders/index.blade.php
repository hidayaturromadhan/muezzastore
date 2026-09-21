@extends('layouts.app')

@section('content')
<style>
    :root {
        --cyan:  #03AEC6;
        --cyan2: #029bb5;
        --navy:  #01294D;
        --gray:  #64748b;
        --border:#e2e8f0;
    }
    *, *::before, *::after { box-sizing: border-box; }
    .orders-wrap { font-family: 'Plus Jakarta Sans', sans-serif; color: var(--navy); max-width: 860px; margin: 0 auto; }

    /* ── PAGE HEADER ─────────────────────────────────── */
    .page-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 1.75rem; flex-wrap: wrap; gap: 1rem;
    }
    .page-title {
        font-family: 'Syne', sans-serif;
        font-size: 1.5rem; font-weight: 800; color: var(--navy);
        display: flex; align-items: center; gap: .625rem; margin: 0;
    }
    .page-title-icon {
        width: 42px; height: 42px; border-radius: 12px;
        background: rgba(3,174,198,0.12); color: var(--cyan);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.125rem; flex-shrink: 0;
    }
    .btn-back-shop {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .5rem 1.125rem; border-radius: 10px;
        border: 2px solid var(--border); background: #fff;
        color: var(--gray); font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: .8125rem; font-weight: 700; text-decoration: none;
        transition: border-color .2s, color .2s;
    }
    .btn-back-shop:hover { border-color: var(--cyan); color: var(--cyan); }

    /* ── ORDER CARD ──────────────────────────────────── */
    .order-card {
        background: #fff; border-radius: 16px;
        border: 1.5px solid var(--border);
        box-shadow: 0 1px 8px rgba(1,41,77,0.05);
        margin-bottom: 1rem; overflow: hidden;
        transition: box-shadow .25s, border-color .25s;
        contain: layout;
    }
    .order-card:hover {
        box-shadow: 0 6px 24px rgba(3,174,198,0.10);
        border-color: rgba(3,174,198,0.22);
    }

    /* top strip color per status */
    .order-card-strip { height: 3px; }
    .strip-fulfilled  { background: linear-gradient(90deg, #16a34a, #22c55e); }
    .strip-paid       { background: linear-gradient(90deg, var(--cyan), var(--cyan2)); }
    .strip-processing { background: linear-gradient(90deg, #d97706, #fbbf24); }
    .strip-failed     { background: linear-gradient(90deg, #dc2626, #f87171); }
    .strip-pending    { background: linear-gradient(90deg, #94a3b8, #cbd5e0); }

    .order-card-body {
        padding: 1.25rem 1.5rem;
        display: flex; align-items: flex-start;
        justify-content: space-between; gap: 1.5rem;
    }

    /* left side */
    .order-left { flex: 1; min-width: 0; }

    .order-top-row {
        display: flex; align-items: center; gap: .625rem;
        flex-wrap: wrap; margin-bottom: .625rem;
    }

    .order-product-name {
        font-size: .9375rem; font-weight: 700; color: var(--navy);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        max-width: 340px;
    }

    /* status badges */
    .badge {
        display: inline-flex; align-items: center; gap: .3rem;
        padding: .25rem .65rem; border-radius: 999px;
        font-size: .7rem; font-weight: 700; flex-shrink: 0;
    }
    .badge-fulfilled  { background: #f0fdf4; color: #16a34a; }
    .badge-paid       { background: rgba(3,174,198,0.10); color: var(--cyan2); }
    .badge-processing { background: #fffbeb; color: #d97706; }
    .badge-failed     { background: #fef2f2; color: #dc2626; }
    .badge-pending    { background: #f8fafc; color: #64748b; }

    /* meta info */
    .order-meta { display: flex; flex-direction: column; gap: .3rem; }
    .order-meta-row {
        display: flex; align-items: center; gap: .5rem;
        font-size: .8rem; color: var(--gray);
    }
    .order-meta-row i { width: 14px; text-align: center; color: #94a3b8; font-size: .75rem; }
    .order-meta-label { font-weight: 600; color: #94a3b8; }
    .order-meta-val   { font-weight: 500; color: var(--gray); }
    .order-meta-val code {
        background: #f1f5f9; padding: .1rem .35rem;
        border-radius: 4px; font-size: .75rem; color: #475569;
        font-family: 'Courier New', monospace;
    }

    /* right side */
    .order-right {
        display: flex; flex-direction: column;
        align-items: flex-end; gap: .75rem; flex-shrink: 0;
    }

    .order-amount {
        font-family: 'Poppins', sans-serif;
        font-size: 1.25rem; font-weight: 800; color: var(--cyan);
        line-height: 1;
    }

    .order-actions { display: flex; gap: .5rem; }

    .btn-detail {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .5rem 1rem; border-radius: 9px;
        background: linear-gradient(135deg, var(--cyan), var(--cyan2));
        color: #fff; font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: .8rem; font-weight: 700; text-decoration: none;
        transition: box-shadow .2s, transform .2s;
    }
    .btn-detail:hover { color: #fff; transform: translateY(-1px); box-shadow: 0 5px 14px rgba(3,174,198,0.32); }

    .btn-failed {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .5rem .875rem; border-radius: 9px;
        background: rgba(220,38,38,0.08); color: #dc2626;
        border: 1.5px solid rgba(220,38,38,0.2);
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: .8rem; font-weight: 700; text-decoration: none;
        transition: background .2s, color .2s;
    }
    .btn-failed:hover { background: #dc2626; color: #fff; }

    /* ── EMPTY STATE ─────────────────────────────────── */
    .empty-state {
        text-align: center; padding: 5rem 2rem;
        background: #fff; border-radius: 20px;
        box-shadow: 0 2px 12px rgba(1,41,77,0.05);
        border: 1.5px solid var(--border);
    }
    .empty-icon {
        width: 80px; height: 80px; border-radius: 50%;
        background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
        display: flex; align-items: center; justify-content: center;
        font-size: 2.25rem; color: var(--cyan); margin: 0 auto 1.25rem;
    }
    .empty-title { font-family: 'Syne', sans-serif; font-size: 1.25rem; font-weight: 800; color: var(--navy); margin: 0 0 .4rem; }
    .empty-sub   { font-size: .875rem; color: var(--gray); margin: 0 0 1.5rem; }
    .btn-shop {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .75rem 1.75rem; border-radius: 12px;
        background: linear-gradient(135deg, var(--cyan), var(--cyan2));
        color: #fff; font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 700; font-size: .9rem; text-decoration: none;
        transition: box-shadow .2s, transform .2s;
    }
    .btn-shop:hover { color: #fff; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(3,174,198,0.35); }

    /* ── PAGINATION ──────────────────────────────────── */
    .pagi-wrap { margin-top: 2rem; display: flex; flex-direction: column; align-items: center; gap: .75rem; }
    .pagi-list { display: flex; align-items: center; flex-wrap: wrap; gap: .3rem; justify-content: center; list-style: none; padding: 0; margin: 0; }
    .pagi-list li a, .pagi-list li span { display: inline-flex; align-items: center; justify-content: center; min-width: 2.25rem; height: 2.25rem; padding: 0 .75rem; border-radius: 9px; font-family: 'Plus Jakarta Sans', sans-serif; font-size: .8125rem; font-weight: 600; text-decoration: none !important; border: 2px solid var(--border); background: #fff; color: var(--navy) !important; transition: all .2s; cursor: pointer; }
    .pagi-list li a:hover { border-color: var(--cyan); color: var(--cyan) !important; background: rgba(3,174,198,0.07); transform: translateY(-1px); }
    .pagi-list li.active span { background: linear-gradient(135deg, var(--cyan), var(--cyan2)); border-color: var(--cyan); color: #fff !important; box-shadow: 0 3px 10px rgba(3,174,198,0.32); cursor: default; }
    .pagi-list li.disabled span { color: #cbd5e0 !important; border-color: #f1f5f9 !important; background: #f8fafc !important; cursor: not-allowed; }
    .pagi-list li:first-child a, .pagi-list li:first-child span,
    .pagi-list li:last-child a,  .pagi-list li:last-child span { padding: 0 1rem; border-radius: 10px; }
    .pagi-list li:first-child a:hover, .pagi-list li:last-child a:hover { background: linear-gradient(135deg, var(--cyan), var(--cyan2)); color: #fff !important; border-color: var(--cyan); }

    /* ── RESPONSIVE ──────────────────────────────────── */
    @media (max-width: 620px) {
        .order-card-body { flex-direction: column; gap: 1rem; }
        .order-right { align-items: flex-start; flex-direction: row; justify-content: space-between; width: 100%; }
        .order-product-name { max-width: 100%; white-space: normal; }
    }
</style>

<div class="orders-wrap">

    {{-- PAGE HEADER --}}
    <div class="page-header">
        <h1 class="page-title">
            <span class="page-title-icon"><i class="fas fa-receipt"></i></span>
            Riwayat Pesanan
        </h1>
        <a href="{{ route('home') }}" class="btn-back-shop">
            <i class="fas fa-store"></i> Lanjut Belanja
        </a>
    </div>

    @forelse($orders as $order)
        @php
            $status = $order->status;

            $stripClass = match($status) {
                'fulfilled'  => 'strip-fulfilled',
                'paid'       => 'strip-paid',
                'processing' => 'strip-processing',
                'failed'     => 'strip-failed',
                default      => 'strip-pending',
            };

            $badgeClass = match($status) {
                'fulfilled'  => 'badge-fulfilled',
                'paid'       => 'badge-paid',
                'processing' => 'badge-processing',
                'failed'     => 'badge-failed',
                default      => 'badge-pending',
            };

            $badgeIcon = match($status) {
                'fulfilled'  => 'fas fa-check-circle',
                'paid'       => 'fas fa-credit-card',
                'processing' => 'fas fa-spinner',
                'failed'     => 'fas fa-times-circle',
                default      => 'fas fa-clock',
            };

            $badgeLabel = match($status) {
                'fulfilled'  => 'Selesai',
                'paid'       => 'Dibayar',
                'processing' => 'Diproses',
                'failed'     => 'Gagal',
                default      => 'Pending',
            };
        @endphp

        <div class="order-card">
            <div class="order-card-strip {{ $stripClass }}"></div>
            <div class="order-card-body">

                {{-- LEFT --}}
                <div class="order-left">
                    <div class="order-top-row">
                        <span class="order-product-name">
                            {{ $order->product->product_name ?? 'Produk' }}
                        </span>
                        <span class="badge {{ $badgeClass }}">
                            <i class="{{ $badgeIcon }}"></i> {{ $badgeLabel }}
                        </span>
                    </div>

                    <div class="order-meta">
                        <div class="order-meta-row">
                            <i class="fas fa-hashtag"></i>
                            <span class="order-meta-label">Order ID</span>
                            <span class="order-meta-val"><code>{{ $order->midtrans_order_id }}</code></span>
                        </div>
                        <div class="order-meta-row">
                            <i class="fas fa-crosshairs"></i>
                            <span class="order-meta-label">Tujuan</span>
                            <span class="order-meta-val">{{ $order->target }}</span>
                        </div>
                        <div class="order-meta-row">
                            <i class="fas fa-calendar"></i>
                            <span class="order-meta-label">Tanggal</span>
                            <span class="order-meta-val">{{ $order->created_at->format('d M Y, H:i') }} WIB</span>
                        </div>
                    </div>
                </div>

                {{-- RIGHT --}}
                <div class="order-right">
                    <div class="order-amount">
                        Rp {{ number_format($order->gross_amount ?? $order->price ?? 0, 0, ',', '.') }}
                    </div>

                    <div class="order-actions">
                        <a href="{{ route('orders.show', $order->id) }}" class="btn-detail">
                            <i class="fas fa-eye"></i> Detail
                        </a>

                        @if($order->status === 'failed')
                            <a href="{{ route('orders.show', $order->id) }}" class="btn-failed">
                                <i class="fas fa-exclamation-circle"></i> Cek
                            </a>
                        @endif
                    </div>
                </div>

            </div>
        </div>

    @empty

        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-box-open"></i></div>
            <h3 class="empty-title">Belum Ada Pesanan</h3>
            <p class="empty-sub">Kamu belum pernah melakukan pembelian produk.</p>
            <a href="{{ route('home') }}" class="btn-shop">
                <i class="fas fa-store"></i> Mulai Belanja
            </a>
        </div>

    @endforelse

    {{-- PAGINATION --}}
    @if($orders->hasPages())
        <div class="pagi-wrap">
            <ul class="pagi-list">
                <li class="{{ $orders->onFirstPage() ? 'disabled' : '' }}">
                    @if($orders->onFirstPage())
                        <span>&#8592; Prev</span>
                    @else
                        <a href="{{ $orders->previousPageUrl() }}" rel="prev">&#8592; Prev</a>
                    @endif
                </li>
                @foreach($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                    <li class="{{ $page == $orders->currentPage() ? 'active' : '' }}">
                        @if($page == $orders->currentPage())
                            <span>{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    </li>
                @endforeach
                <li class="{{ !$orders->hasMorePages() ? 'disabled' : '' }}">
                    @if($orders->hasMorePages())
                        <a href="{{ $orders->nextPageUrl() }}" rel="next">Next &#8594;</a>
                    @else
                        <span>Next &#8594;</span>
                    @endif
                </li>
            </ul>
        </div>
    @endif

</div>
@endsection