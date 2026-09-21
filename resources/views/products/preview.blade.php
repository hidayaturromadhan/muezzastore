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
    .preview-wrap {
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: var(--navy);
        max-width: 560px;
        margin: 0 auto;
    }

    /* ── STEPS ───────────────────────────────────────── */
    .steps {
        display: flex; align-items: center;
        justify-content: center;
        margin-bottom: 1.75rem;
    }
    .step {
        display: flex; flex-direction: column;
        align-items: center; gap: .3rem;
        flex: 1; max-width: 110px;
    }
    .step-dot {
        width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: .8rem; font-weight: 700;
    }
    .step.done   .step-dot { background: var(--cyan); color: #fff; }
    .step.active .step-dot { background: var(--navy); color: #fff; box-shadow: 0 0 0 4px rgba(3,174,198,0.2); }
    .step.idle   .step-dot { background: #f1f5f9; color: #94a3b8; border: 2px solid var(--border); }
    .step-label { font-size: .65rem; font-weight: 700; color: var(--gray); text-align: center; }
    .step.active .step-label { color: var(--navy); }
    .step.done   .step-label { color: var(--cyan2); }
    .step-line { flex: 1; height: 2px; background: var(--border); margin-bottom: 1.4rem; max-width: 60px; }
    .step-line.done { background: var(--cyan); }

    /* ── PRODUCT SUMMARY (top) ───────────────────────── */
    .product-summary {
        background: #fff; border-radius: 16px;
        border: 1.5px solid var(--border);
        box-shadow: 0 2px 12px rgba(1,41,77,0.06);
        padding: 1.125rem 1.25rem;
        display: flex; align-items: center; gap: 1rem;
        margin-bottom: 1rem;
    }
    .product-summary-icon {
        width: 48px; height: 48px; border-radius: 12px;
        background: rgba(3,174,198,0.10); color: var(--cyan);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem; flex-shrink: 0;
    }
    .product-summary-name {
        font-size: .9375rem; font-weight: 700; color: var(--navy);
        margin: 0 0 .3rem; line-height: 1.3;
    }
    .product-summary-badges { display: flex; gap: .375rem; flex-wrap: wrap; }
    .badge-pill {
        display: inline-flex; align-items: center; gap: .25rem;
        padding: .2rem .55rem; border-radius: 99px;
        font-size: .675rem; font-weight: 700;
    }
    .badge-cat   { background: rgba(1,41,77,0.07); color: var(--navy); }
    .badge-brand { background: rgba(3,174,198,0.10); color: var(--cyan2); }

    /* ── ORDER DETAIL CARD ───────────────────────────── */
    .detail-card {
        background: #fff; border-radius: 16px;
        border: 1.5px solid var(--border);
        box-shadow: 0 2px 12px rgba(1,41,77,0.06);
        overflow: hidden; margin-bottom: 1rem;
    }
    .detail-card-title {
        font-size: .7rem; font-weight: 700; color: #94a3b8;
        text-transform: uppercase; letter-spacing: .06em;
        padding: .875rem 1.25rem .5rem;
    }

    .detail-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: .75rem 1.25rem; border-top: 1px solid #f8fafc; gap: .75rem;
    }
    .detail-row:first-of-type { border-top: none; }
    .detail-key { font-size: .8125rem; font-weight: 600; color: var(--gray); flex-shrink: 0; }
    .detail-val { font-size: .8125rem; font-weight: 700; color: var(--navy); text-align: right; word-break: break-word; }
    .detail-val.cyan  { color: var(--cyan); font-family: 'Poppins', sans-serif; font-size: 1rem; font-weight: 800; }
    .detail-val.green { color: #16a34a; font-weight: 700; }

    /* ── PAY SECTION ─────────────────────────────────── */
    .pay-section {
        background: #fff; border-radius: 16px;
        border: 1.5px solid var(--border);
        box-shadow: 0 2px 12px rgba(1,41,77,0.06);
        padding: 1.25rem;
        margin-bottom: 1rem;
    }

    .total-row {
        display: flex; align-items: center; justify-content: space-between;
        background: rgba(3,174,198,0.06); border: 1.5px solid rgba(3,174,198,0.18);
        border-radius: 12px; padding: 1rem 1.125rem;
        margin-bottom: 1rem;
    }
    .total-row-label { font-size: .8rem; font-weight: 700; color: var(--gray); }
    .total-row-amount { font-family: 'Poppins', sans-serif; font-size: 1.5rem; font-weight: 800; color: var(--cyan); }

    .pay-notice {
        display: flex; align-items: flex-start; gap: .5rem;
        background: #f8fafc; border-radius: 10px; padding: .75rem .875rem;
        font-size: .775rem; color: var(--gray); font-weight: 500; line-height: 1.5;
        margin-bottom: 1rem;
    }
    .pay-notice i { color: var(--cyan); flex-shrink: 0; margin-top: 2px; font-size: .825rem; }

    .btn-pay {
        width: 100%; height: 52px;
        background: linear-gradient(135deg, var(--cyan), var(--cyan2));
        color: #fff; border: none; border-radius: 13px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1rem; font-weight: 700; cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: .5rem;
        transition: box-shadow .2s, transform .2s; margin-bottom: .75rem;
    }
    .btn-pay:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(3,174,198,0.38); }
    .btn-pay:active { transform: none; }

    .btn-row { display: grid; grid-template-columns: 1fr 1fr; gap: .625rem; }

    .btn-secondary {
        height: 44px; border-radius: 11px;
        border: 2px solid var(--border); background: #fff;
        color: var(--navy); font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: .8125rem; font-weight: 700;
        display: inline-flex; align-items: center; justify-content: center; gap: .4rem;
        text-decoration: none; transition: border-color .2s, color .2s; cursor: pointer;
    }
    .btn-secondary:hover { border-color: var(--cyan); color: var(--cyan); }

    .btn-ghost {
        height: 44px; border-radius: 11px;
        border: 2px solid var(--border); background: #fff;
        color: var(--gray); font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: .8125rem; font-weight: 600;
        display: inline-flex; align-items: center; justify-content: center; gap: .4rem;
        text-decoration: none; transition: border-color .2s, color .2s;
    }
    .btn-ghost:hover { border-color: var(--gray); color: var(--navy); }

    /* guarantee */
    .guarantee-row {
        display: flex; justify-content: center; gap: 1.25rem;
        flex-wrap: wrap; padding-top: .875rem;
        border-top: 1px solid var(--border); margin-top: .875rem;
    }
    .g-item { display: flex; align-items: center; gap: .3rem; font-size: .7rem; font-weight: 600; color: var(--gray); }
    .g-item i { color: var(--cyan); font-size: .75rem; }

    /* alert */
    .alert-box {
        display: flex; align-items: flex-start; gap: .625rem;
        padding: .875rem 1rem; border-radius: 12px; margin-bottom: 1rem;
        font-size: .8125rem; font-weight: 500;
        background: #fef2f2; border-left: 3px solid #dc2626; color: #dc2626;
    }
</style>

<div class="preview-wrap">

    {{-- STEPS --}}
    <div class="steps">
        <div class="step done">
            <div class="step-dot"><i class="fas fa-check"></i></div>
            <span class="step-label">Produk</span>
        </div>
        <div class="step-line done"></div>
        <div class="step done">
            <div class="step-dot"><i class="fas fa-check"></i></div>
            <span class="step-label">Data</span>
        </div>
        <div class="step-line done"></div>
        <div class="step active">
            <div class="step-dot"><i class="fas fa-clipboard-check"></i></div>
            <span class="step-label">Konfirmasi</span>
        </div>
        <div class="step-line"></div>
        <div class="step idle">
            <div class="step-dot"><i class="fas fa-credit-card"></i></div>
            <span class="step-label">Bayar</span>
        </div>
    </div>

    {{-- ALERT --}}
    @if(session('error'))
        <div class="alert-box">
            <i class="fas fa-exclamation-circle" style="margin-top:2px;flex-shrink:0;"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- PRODUCT SUMMARY --}}
    <div class="product-summary">
        <div class="product-summary-icon">
            @php
                $catIcon = match(strtolower($product->category ?? '')) {
                    'pulsa'   => 'fas fa-phone-alt',
                    'pln'     => 'fas fa-bolt',
                    'data'    => 'fas fa-wifi',
                    'e-money' => 'fas fa-credit-card',
                    'games'   => 'fas fa-gamepad',
                    default   => 'fas fa-shopping-bag',
                };
            @endphp
            <i class="{{ $catIcon }}"></i>
        </div>
        <div style="min-width:0;">
            <p class="product-summary-name">{{ $product->product_name }}</p>
            <div class="product-summary-badges">
                <span class="badge-pill badge-cat">{{ $product->category }}</span>
                <span class="badge-pill badge-brand">{{ $product->brand }}</span>
            </div>
        </div>
    </div>

    {{-- ORDER DETAIL --}}
    <div class="detail-card">
        <div class="detail-card-title">Detail Pesanan</div>

        @if($product->target_type === 'pln')
            <div class="detail-row">
                <span class="detail-key">Nama Pelanggan</span>
                <span class="detail-val green">{{ data_get($pln_inquiry, 'name', '-') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-key">Daya</span>
                <span class="detail-val">{{ data_get($pln_inquiry, 'segment_power', '-') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-key">ID Pelanggan PLN</span>
                <span class="detail-val">{{ $target }}</span>
            </div>

        @elseif($product->target_type === 'ml')
            <div class="detail-row">
                <span class="detail-key">User ID</span>
                <span class="detail-val">{{ $ml_user_id ?? '-' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-key">Zone ID</span>
                <span class="detail-val">{{ $ml_server_id ?? '-' }}</span>
            </div>

        @else
            <div class="detail-row">
                <span class="detail-key">
                    {{ $product->target_type === 'phone' ? 'Nomor HP' : 'ID Pelanggan' }}
                </span>
                <span class="detail-val">{{ $target }}</span>
            </div>
        @endif
    </div>

    {{-- PAY SECTION --}}
    <div class="pay-section">

        {{-- Info Provider (NO SALDO SPILL) --}}
        @php
            $showProviderBlock = isset($provider_price) && (int)$provider_price > 0;
            $providerOk = isset($can_buy) ? (bool)$can_buy : true;
        @endphp

        @if($showProviderBlock)
            @if(!$providerOk)
                <div class="alert-box" style="background:#fff7ed;border-left-color:#f97316;color:#9a3412;">
                    <i class="fas fa-triangle-exclamation" style="margin-top:2px;flex-shrink:0;"></i>
                    <div>
                        <div style="font-weight:800;margin-bottom:2px;">Produk sedang tidak tersedia</div>
                        <div style="font-size:.78rem;line-height:1.45;">
                            Saat ini sistem belum bisa memproses pembelian untuk produk ini.
                            Silakan coba lagi beberapa saat. Kalau masih sama, hubungi admin.
                        </div>
                    </div>
                </div>
            @endif
        @endif

        {{-- Total --}}
        <div class="total-row">
            <span class="total-row-label">Total Bayar</span>
            <span class="total-row-amount">Rp {{ number_format((int)$gross_amount, 0, ',', '.') }}</span>
        </div>

        {{-- Notice --}}
        <div class="pay-notice">
            <i class="fas fa-circle-info"></i>
            Kamu akan diarahkan ke halaman pembayaran <strong>Midtrans</strong> yang aman setelah klik tombol di bawah.
        </div>

        {{-- Pay button --}}
        <form method="POST" action="{{ route('products.pay', $product) }}">
            @csrf
            <input type="hidden" name="target" value="{{ $target }}">

            <button type="submit"
                    class="btn-pay"
                    {{ ($showProviderBlock && !$providerOk) ? 'disabled' : '' }}
                    style="{{ ($showProviderBlock && !$providerOk) ? 'opacity:.55;cursor:not-allowed;transform:none;box-shadow:none;' : '' }}">
                <i class="fas fa-lock"></i>
                {{ ($showProviderBlock && !$providerOk) ? 'Tidak Bisa Diproses Saat Ini' : 'Bayar Sekarang' }}
            </button>
        </form>

        {{-- Secondary actions --}}
        <div class="btn-row">
            <a href="{{ route('products.show', $product) }}" class="btn-secondary">
                <i class="fas fa-pen"></i> Ubah Data
            </a>
            <a href="{{ route('home') }}" class="btn-ghost">
                <i class="fas fa-chevron-left"></i> Kembali
            </a>
        </div>

        {{-- Guarantee --}}
        <div class="guarantee-row">
            <div class="g-item"><i class="fas fa-shield-halved"></i> Transaksi Aman</div>
            <div class="g-item"><i class="fas fa-bolt"></i> Proses Instan</div>
            <div class="g-item"><i class="fas fa-headset"></i> 24/7 Support</div>
        </div>
    </div>
</div>
@endsection
