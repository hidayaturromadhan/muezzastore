@extends('layouts.app')

@section('content')
<style>
    :root {
        --cyan:  #03AEC6;
        --cyan2: #029bb5;
        --navy:  #01294D;
        --gray:  #64748b;
        --border:#e2e8f0;
        --bg:    #f1f5f9;
    }
    *, *::before, *::after { box-sizing: border-box; }
    .show-wrap { font-family: 'Plus Jakarta Sans', sans-serif; color: var(--navy); }

    /* ── BREADCRUMB ──────────────────────────────────── */
    .breadcrumb {
        display: flex; align-items: center; gap: .5rem;
        font-size: .8125rem; color: var(--gray); margin-bottom: 1.5rem;
    }
    .breadcrumb a { color: var(--cyan); text-decoration: none; font-weight: 600; }
    .breadcrumb a:hover { text-decoration: underline; }
    .breadcrumb span { color: #cbd5e0; }

    /* ── MAIN GRID ───────────────────────────────────── */
    .show-grid {
        display: grid;
        grid-template-columns: 340px 1fr;
        gap: 1.5rem;
        align-items: start;
    }

    /* ── PRODUCT CARD (left) ─────────────────────────── */
    .prod-card {
        background: #fff; border-radius: 20px; overflow: hidden;
        box-shadow: 0 2px 16px rgba(1,41,77,0.08);
        border: 1.5px solid var(--border);
        position: sticky; top: 80px;
    }

    .prod-img-box {
        position: relative; width: 100%; padding-top: 75%;
        background: linear-gradient(135deg, #f0f4f8, #e2e8f0); overflow: hidden;
    }
    .prod-img-box img {
        position: absolute; inset: 0; width: 100%; height: 100%;
        object-fit: cover;
    }
    .prod-img-box .no-img {
        position: absolute; inset: 0; display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        color: #cbd5e0; font-size: 3rem; gap: .5rem;
    }
    .prod-img-box .no-img span { font-size: .8125rem; font-weight: 600; }

    /* badges on image */
    .img-badges {
        position: absolute; top: 12px; left: 12px; right: 12px;
        display: flex; justify-content: space-between; z-index: 2;
    }
    .img-badge {
        padding: .3rem .7rem; border-radius: 8px;
        font-size: .7rem; font-weight: 700;
        backdrop-filter: blur(6px);
    }
    .img-badge-cat   { background: rgba(1,41,77,0.82); color: #fff; }
    .img-badge-brand { background: rgba(3,174,198,0.88); color: #fff; }

    .prod-meta { padding: 1.375rem 1.5rem; }

    .prod-labels {
        display: flex; flex-wrap: wrap; gap: .375rem; margin-bottom: .875rem;
    }
    .prod-label {
        display: inline-flex; align-items: center; gap: .3rem;
        padding: .3rem .7rem; border-radius: 8px;
        font-size: .7rem; font-weight: 700;
    }
    .prod-label-cat   { background: rgba(1,41,77,0.07); color: var(--navy); }
    .prod-label-brand { background: rgba(3,174,198,0.10); color: var(--cyan2); }

    .prod-title {
        font-family: 'Syne', sans-serif;
        font-size: 1.125rem; font-weight: 800; color: var(--navy);
        line-height: 1.35; margin: 0 0 1rem;
    }

    .prod-divider { border: none; border-top: 1px solid var(--border); margin: 1rem 0; }

    .prod-price-row { display: flex; align-items: baseline; gap: .5rem; }
    .prod-price-label { font-size: .75rem; font-weight: 600; color: var(--gray); }
    .prod-price {
        font-family: 'Poppins', sans-serif;
        font-size: 1.625rem; font-weight: 800; color: var(--cyan);
        line-height: 1;
    }

    /* ── ORDER CARD (right) ──────────────────────────── */
    .order-card {
        background: #fff; border-radius: 20px;
        box-shadow: 0 2px 16px rgba(1,41,77,0.08);
        border: 1.5px solid var(--border);
        overflow: hidden;
    }

    .order-card-header {
        padding: 1.125rem 1.5rem;
        border-bottom: 1px solid var(--border);
        display: flex; align-items: center; gap: .625rem;
    }
    .order-card-header-icon {
        width: 36px; height: 36px; border-radius: 10px;
        background: rgba(3,174,198,0.12); color: var(--cyan);
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; flex-shrink: 0;
    }
    .order-card-title {
        font-family: 'Syne', sans-serif;
        font-size: .9375rem; font-weight: 800; color: var(--navy); margin: 0;
    }
    .order-card-sub { font-size: .75rem; color: var(--gray); margin: .1rem 0 0; }

    .order-card-body { padding: 1.5rem; }

    /* alerts */
    .alert-box {
        display: flex; align-items: flex-start; gap: .625rem;
        padding: .875rem 1rem; border-radius: 12px; margin-bottom: 1.25rem;
        font-size: .875rem; font-weight: 500; border-left: 3px solid;
    }
    .alert-danger  { background: #fef2f2; border-color: #dc2626; color: #dc2626; }
    .alert-info    { background: #eff6ff; border-color: #3b82f6; color: #1d4ed8; }
    .alert-success { background: #f0fdf4; border-color: #16a34a; color: #15803d; }
    .alert-pln     { background: rgba(3,174,198,0.07); border-color: var(--cyan); color: var(--cyan2); }

    /* form elements */
    .field { margin-bottom: 1.25rem; }
    .field-label {
        display: block; font-size: .8125rem; font-weight: 700; color: var(--navy);
        margin-bottom: .4rem;
    }
    .field-hint { font-size: .75rem; color: var(--gray); margin-top: .35rem; }

    .field-input {
        width: 100%; height: 48px; padding: 0 1rem;
        border: 2px solid var(--border); border-radius: 12px;
        font-family: 'Plus Jakarta Sans', sans-serif; font-size: .9375rem;
        color: var(--navy); background: #f8fafc;
        transition: border-color .2s, box-shadow .2s;
    }
    .field-input:focus {
        outline: none; border-color: var(--cyan);
        background: #fff; box-shadow: 0 0 0 3px rgba(3,174,198,0.1);
    }
    .field-input::placeholder { color: #94a3b8; }

    /* id+zone grid for ML */
    .ml-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .875rem; }

    /* pln result box */
    #plnResult {
        border-radius: 12px; padding: .875rem 1rem;
        font-size: .875rem; margin-bottom: 1.25rem;
    }
    #plnResult.d-none { display: none !important; }

    /* action buttons */
    .action-row { display: flex; gap: .75rem; flex-wrap: wrap; margin-top: 1.5rem; }

    .btn-lanjut {
        flex: 1; min-width: 140px; height: 48px; border: none; border-radius: 12px;
        background: linear-gradient(135deg, var(--cyan), var(--cyan2));
        color: #fff; font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: .9375rem; font-weight: 700; cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
        transition: box-shadow .2s, transform .2s, opacity .2s;
    }
    .btn-lanjut:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(3,174,198,0.35); }
    .btn-lanjut:disabled { opacity: .45; cursor: not-allowed; }

    .btn-cek {
        height: 48px; padding: 0 1.375rem; border-radius: 12px;
        border: 2px solid var(--cyan); background: transparent;
        color: var(--cyan); font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: .875rem; font-weight: 700; cursor: pointer;
        display: inline-flex; align-items: center; gap: .4rem;
        transition: background .2s, color .2s;
    }
    .btn-cek:hover:not(:disabled) { background: var(--cyan); color: #fff; }
    .btn-cek:disabled { opacity: .5; cursor: not-allowed; }

    .btn-back {
        height: 48px; padding: 0 1.25rem; border-radius: 12px;
        border: 2px solid var(--border); background: #fff;
        color: var(--gray); font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: .875rem; font-weight: 600; cursor: pointer;
        display: inline-flex; align-items: center; gap: .4rem;
        text-decoration: none; transition: border-color .2s, color .2s;
    }
    .btn-back:hover { border-color: var(--navy); color: var(--navy); }

    /* guest cta */
    .guest-cta {
        text-align: center; padding: 2rem 1rem;
    }
    .guest-icon {
        width: 64px; height: 64px; border-radius: 50%;
        background: rgba(3,174,198,0.10); color: var(--cyan);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.75rem; margin: 0 auto 1rem;
    }
    .guest-title { font-family: 'Syne', sans-serif; font-size: 1.125rem; font-weight: 800; color: var(--navy); margin: 0 0 .4rem; }
    .guest-sub { font-size: .875rem; color: var(--gray); margin: 0 0 1.375rem; }
    .btn-login {
        display: inline-flex; align-items: center; gap: .4rem;
        height: 46px; padding: 0 1.75rem; border-radius: 12px;
        background: linear-gradient(135deg, var(--cyan), var(--cyan2));
        color: #fff; font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: .9rem; font-weight: 700; text-decoration: none;
        transition: box-shadow .2s, transform .2s;
    }
    .btn-login:hover { color: #fff; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(3,174,198,0.35); }

    /* ── RESPONSIVE ──────────────────────────────────── */
    @media (max-width: 860px) {
        .show-grid { grid-template-columns: 1fr; }
        .prod-card { position: static; }
        .ml-grid   { grid-template-columns: 1fr; }
    }
    @media (max-width: 500px) {
        .action-row { flex-direction: column; }
        .btn-lanjut, .btn-cek, .btn-back { width: 100%; justify-content: center; }
    }
</style>

<div class="show-wrap">

    {{-- BREADCRUMB --}}
    <nav class="breadcrumb">
        <a href="{{ route('products.index') }}"><i class="fas fa-store"></i> Produk</a>
        <span>/</span>
        <span>{{ $product->category }}</span>
        <span>/</span>
        <span style="color:var(--navy);font-weight:600;">{{ Str::limit($product->product_name, 40) }}</span>
    </nav>

    <div class="show-grid">

        {{-- ── LEFT: PRODUCT CARD ───────────────────── --}}
        <div class="prod-card">
            <div class="prod-img-box">
                @php
                    $img = null;
                    if (!empty($product->image))                                    { $img = asset($product->image); }
                    elseif (file_exists(public_path('images/placeholder.png')))     { $img = asset('images/placeholder.png'); }
                @endphp

                @if($img)
                    <img src="{{ $img }}" alt="{{ $product->product_name }}" loading="eager" decoding="async">
                @else
                    <div class="no-img">
                        <i class="fas fa-image"></i>
                        <span>Tidak ada gambar</span>
                    </div>
                @endif

                <div class="img-badges">
                    <span class="img-badge img-badge-cat">
                        <i class="fas fa-tag" style="margin-right:.3rem;"></i>{{ $product->category }}
                    </span>
                    <span class="img-badge img-badge-brand">{{ $product->brand }}</span>
                </div>
            </div>

            <div class="prod-meta">
                <div class="prod-labels">
                    <span class="prod-label prod-label-cat">
                        <i class="fas fa-layer-group"></i> {{ $product->category }}
                    </span>
                    <span class="prod-label prod-label-brand">
                        <i class="fas fa-shield-alt"></i> {{ $product->brand }}
                    </span>
                </div>

                <h1 class="prod-title">{{ $product->product_name }}</h1>

                <hr class="prod-divider">

                <div class="prod-price-row">
                    <span class="prod-price-label">Harga</span>
                    <span class="prod-price">Rp {{ number_format((int)$product->price, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- ── RIGHT: ORDER CARD ────────────────────── --}}
        <div class="order-card">
            <div class="order-card-header">
                <div class="order-card-header-icon">
                    <i class="fas fa-edit"></i>
                </div>
                <div>
                    <p class="order-card-title">Input Tujuan</p>
                    <p class="order-card-sub">Isi data dengan benar sebelum melanjutkan</p>
                </div>
            </div>

            <div class="order-card-body">

                {{-- Session / Validation errors --}}
                @if(session('error'))
                    <div class="alert-box alert-danger">
                        <i class="fas fa-exclamation-circle" style="margin-top:2px;flex-shrink:0;"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert-box alert-danger">
                        <i class="fas fa-exclamation-circle" style="margin-top:2px;flex-shrink:0;"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                @auth
                    <form id="previewForm" method="POST" action="{{ route('products.preview', $product) }}">
                        @csrf

                        {{-- ── MOBILE LEGENDS ──────────────────── --}}
                        @if($product->target_type === 'ml')

                            <div class="alert-box alert-pln" style="margin-bottom:1.25rem;">
                                <i class="fas fa-info-circle" style="margin-top:2px;flex-shrink:0;"></i>
                                <span>Pastikan User ID dan Zone ID Mobile Legends kamu benar.</span>
                            </div>

                            <div class="ml-grid">
                                <div class="field">
                                    <label class="field-label" for="ml_user_id">
                                        <i class="fas fa-user" style="color:var(--cyan);margin-right:.3rem;"></i>User ID
                                    </label>
                                    <input
                                        id="ml_user_id"
                                        name="ml_user_id"
                                        type="text"
                                        inputmode="numeric"
                                        pattern="[0-9]*"
                                        class="field-input"
                                        placeholder="Contoh: 123456789"
                                        value="{{ old('ml_user_id') }}"
                                        required
                                    >
                                    <p class="field-hint"><i class="fas fa-circle-info"></i> Angka saja</p>
                                </div>

                                <div class="field">
                                    <label class="field-label" for="ml_server_id">
                                        <i class="fas fa-server" style="color:var(--cyan);margin-right:.3rem;"></i>Zone ID
                                    </label>
                                    <input
                                        id="ml_server_id"
                                        name="ml_server_id"
                                        type="text"
                                        inputmode="numeric"
                                        pattern="[0-9]*"
                                        class="field-input"
                                        placeholder="Contoh: 1234"
                                        value="{{ old('ml_server_id') }}"
                                        required
                                    >
                                    <p class="field-hint"><i class="fas fa-circle-info"></i> Angka saja</p>
                                </div>
                            </div>

                            <p class="field-hint" style="margin-bottom:1.25rem;">
                                <i class="fas fa-lock" style="color:var(--cyan);"></i>
                                Format tujuan tersimpan otomatis sebagai <code style="background:#f1f5f9;padding:.1rem .35rem;border-radius:4px;">UserID|ZoneID</code>
                            </p>

                            <div class="action-row">
                                <button type="submit" class="btn-lanjut">
                                    <i class="fas fa-arrow-right"></i> Lanjut Pesan
                                </button>
                                <a href="{{ route('home') }}" class="btn-back">
                                    <i class="fas fa-chevron-left"></i> Kembali
                                </a>
                            </div>

                        {{-- ── PLN ──────────────────────────────── --}}
                        @elseif($product->target_type === 'pln')

                            <div class="field">
                                <label class="field-label" for="targetInput">
                                    <i class="fas fa-bolt" style="color:var(--cyan);margin-right:.3rem;"></i>ID Pelanggan PLN
                                </label>
                                <input
                                    id="targetInput"
                                    name="target"
                                    type="text"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    class="field-input"
                                    placeholder="Contoh: 1234567890"
                                    value="{{ old('target') }}"
                                    required
                                >
                                <p class="field-hint">
                                    <i class="fas fa-circle-info"></i>
                                    Cek terlebih dahulu untuk memvalidasi nama pelanggan
                                </p>
                            </div>

                            {{-- PLN result box --}}
                            <div id="plnResult" class="d-none"></div>

                            <div class="action-row">
                                <button type="button" id="btnCekPln" class="btn-cek">
                                    <i class="fas fa-search"></i> Cek PLN
                                </button>
                                <button type="submit" id="btnLanjut" class="btn-lanjut" disabled>
                                    <i class="fas fa-arrow-right"></i> Lanjut Pesan
                                </button>
                                <a href="{{ route('home') }}" class="btn-back">
                                    <i class="fas fa-chevron-left"></i> Kembali
                                </a>
                            </div>

                        {{-- ── DEFAULT / PHONE ──────────────────── --}}
                        @else

                            <div class="field">
                                <label class="field-label" for="targetField">
                                    @if($product->target_type === 'phone')
                                        <i class="fas fa-phone" style="color:var(--cyan);margin-right:.3rem;"></i>Nomor HP Tujuan
                                    @else
                                        <i class="fas fa-id-card" style="color:var(--cyan);margin-right:.3rem;"></i>ID Pelanggan
                                    @endif
                                </label>
                                <input
                                    id="targetField"
                                    name="target"
                                    type="text"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    class="field-input"
                                    placeholder="{{ $product->target_type === 'phone' ? 'Contoh: 081234567890' : 'Contoh: 1234567890' }}"
                                    value="{{ old('target') }}"
                                    required
                                >
                                <p class="field-hint"><i class="fas fa-circle-info"></i> Angka saja</p>
                            </div>

                            <div class="action-row">
                                <button type="submit" class="btn-lanjut">
                                    <i class="fas fa-arrow-right"></i> Lanjut Pesan
                                </button>
                                <a href="{{ route('home') }}" class="btn-back">
                                    <i class="fas fa-chevron-left"></i> Kembali
                                </a>
                            </div>

                        @endif
                    </form>

                @else
                    {{-- Guest CTA --}}
                    <div class="guest-cta">
                        <div class="guest-icon"><i class="fas fa-lock"></i></div>
                        <h3 class="guest-title">Login untuk Memesan</h3>
                        <p class="guest-sub">Kamu bisa melihat produk tanpa login,<br>tapi pemesanan memerlukan akun.</p>
                        <a href="{{ route('login') }}" class="btn-login">
                            <i class="fas fa-sign-in-alt"></i> Login Sekarang
                        </a>
                        <br><br>
                        <a href="{{ route('home') }}" class="btn-back" style="display:inline-flex;margin:0 auto;">
                            <i class="fas fa-chevron-left"></i> Kembali ke Produk
                        </a>
                    </div>
                @endauth

            </div>
        </div>

    </div>{{-- /show-grid --}}
</div>{{-- /show-wrap --}}

{{-- ── PLN SCRIPT (unchanged logic) ───────────────────── --}}
@if(auth()->check() && $product->target_type === 'pln')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const targetInput = document.getElementById('targetInput');
    const btnCek      = document.getElementById('btnCekPln');
    const btnLanjut   = document.getElementById('btnLanjut');
    const box         = document.getElementById('plnResult');

    const showBox = (type, html) => {
        box.classList.remove('d-none', 'alert-box', 'alert-success', 'alert-danger', 'alert-pln');
        box.classList.add('alert-box', type === 'ok' ? 'alert-pln' : 'alert-danger');
        box.innerHTML = html;
    };

    const reset = () => {
        btnLanjut.disabled = true;
        box.classList.add('d-none');
        box.innerHTML = '';
    };

    targetInput?.addEventListener('input', reset);

    btnCek?.addEventListener('click', async () => {
        const customerNo = (targetInput.value || '').trim();
        if (!customerNo)              return alert('Isi ID PLN terlebih dahulu.');
        if (!/^[0-9]+$/.test(customerNo)) return alert('ID PLN harus angka saja.');

        btnCek.disabled   = true;
        btnCek.innerHTML  = '<i class="fas fa-spinner fa-spin"></i> Mengecek...';
        reset();

        try {
            const res = await fetch("{{ route('products.plnInquiry', $product) }}", {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                },
                body: (() => { const fd = new FormData(); fd.append('customer_no', customerNo); return fd; })()
            });

            const ct = (res.headers.get('content-type') || '').toLowerCase();
            if (!ct.includes('application/json')) {
                const text = await res.text();
                showBox('err', `<i class="fas fa-exclamation-circle"></i> Server tidak mengembalikan JSON (HTTP ${res.status}).<br><small>${text.slice(0,300)}</small>`);
                return;
            }

            const json = await res.json();

            if (!res.ok || !json.ok) {
                showBox('err', `<i class="fas fa-exclamation-circle"></i> ${json.message || 'Gagal cek PLN (HTTP ' + res.status + ')'}`);
                return;
            }

            const d = json.data || {};
            showBox('ok',
                `<div style="display:grid;gap:.3rem;">` +
                `<div><i class="fas fa-user" style="width:16px;margin-right:.4rem;"></i><b>Nama:</b> ${d.name ?? '-'}</div>` +
                `<div><i class="fas fa-bolt" style="width:16px;margin-right:.4rem;"></i><b>Daya:</b> ${d.segment_power ?? '-'}</div>` +
                `<div><i class="fas fa-hashtag" style="width:16px;margin-right:.4rem;"></i><b>ID:</b> ${d.customer_no ?? customerNo}</div>` +
                `</div>`
            );

            btnLanjut.disabled = false;

        } catch (e) {
            showBox('err', '<i class="fas fa-wifi-slash"></i> Gagal terhubung. Coba lagi.');
        } finally {
            btnCek.disabled  = false;
            btnCek.innerHTML = '<i class="fas fa-search"></i> Cek PLN';
        }
    });
});
</script>
@endif
@endsection