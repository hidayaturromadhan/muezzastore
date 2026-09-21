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
    .show-wrap { font-family: 'Plus Jakarta Sans', sans-serif; color: var(--navy); max-width: 720px; margin: 0 auto; }

    /* ── BREADCRUMB ──────────────────────────────────── */
    .breadcrumb { display: flex; align-items: center; gap: .5rem; font-size: .8125rem; color: var(--gray); margin-bottom: 1.5rem; }
    .breadcrumb a { color: var(--cyan); text-decoration: none; font-weight: 600; }
    .breadcrumb a:hover { text-decoration: underline; }
    .breadcrumb span { color: #cbd5e0; }

    /* ── STATUS HERO ─────────────────────────────────── */
    .status-hero {
        border-radius: 20px; padding: 1.75rem 2rem;
        margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1.25rem; overflow: hidden;
    }
    .status-hero.fulfilled  { background: linear-gradient(135deg, #f0fdf4, #dcfce7); border: 1.5px solid #bbf7d0; }
    .status-hero.paid       { background: linear-gradient(135deg, rgba(3,174,198,0.07), rgba(3,174,198,0.03)); border: 1.5px solid rgba(3,174,198,0.22); }
    .status-hero.processing { background: linear-gradient(135deg, #fffbeb, #fef9c3); border: 1.5px solid #fde68a; }
    .status-hero.failed     { background: linear-gradient(135deg, #fef2f2, #fee2e2); border: 1.5px solid #fecaca; }
    .status-hero.pending    { background: linear-gradient(135deg, #f8fafc, #f1f5f9); border: 1.5px solid var(--border); }

    .status-icon { width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; }
    .status-hero.fulfilled  .status-icon { background: #dcfce7; color: #16a34a; }
    .status-hero.paid       .status-icon { background: rgba(3,174,198,0.12); color: var(--cyan); }
    .status-hero.processing .status-icon { background: #fef9c3; color: #d97706; }
    .status-hero.failed     .status-icon { background: #fee2e2; color: #dc2626; }
    .status-hero.pending    .status-icon { background: #f1f5f9; color: #94a3b8; }

    .status-info { flex: 1; min-width: 0; }
    .status-label { font-size: .7rem; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; margin-bottom: .25rem; }
    .status-hero.fulfilled  .status-label { color: #16a34a; }
    .status-hero.paid       .status-label { color: var(--cyan2); }
    .status-hero.processing .status-label { color: #d97706; }
    .status-hero.failed     .status-label { color: #dc2626; }
    .status-hero.pending    .status-label { color: #94a3b8; }

    .status-title { font-family: 'Syne', sans-serif; font-size: 1.25rem; font-weight: 800; color: var(--navy); margin: 0 0 .2rem; }
    .status-sub   { font-size: .8rem; color: var(--gray); margin: 0; line-height: 1.5; }

    .btn-refresh {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .5rem 1rem; border-radius: 9px;
        border: 1.5px solid var(--border); background: #fff;
        color: var(--gray); font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: .8rem; font-weight: 700; text-decoration: none;
        transition: border-color .2s, color .2s; flex-shrink: 0;
    }
    .btn-refresh:hover { border-color: var(--cyan); color: var(--cyan); }

    /* ── SN CARD (Token / Nomor Seri hasil topup) ────── */
    .sn-card {
        border-radius: 18px; padding: 1.5rem; margin-bottom: 1.25rem;
    }
    .sn-card.pln   { background: linear-gradient(135deg,#f0fdf4,#dcfce7); border: 1.5px solid #bbf7d0; }
    .sn-card.other { background: linear-gradient(135deg, rgba(3,174,198,0.07), rgba(3,174,198,0.03)); border: 1.5px solid rgba(3,174,198,0.22); }

    .sn-header { display: flex; align-items: center; gap: .625rem; margin-bottom: 1rem; }
    .sn-header-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: .9rem; flex-shrink: 0; }
    .sn-card.pln   .sn-header-icon { background: #dcfce7; color: #16a34a; }
    .sn-card.other .sn-header-icon { background: rgba(3,174,198,0.12); color: var(--cyan); }
    .sn-header-title { font-family: 'Syne', sans-serif; font-size: .9375rem; font-weight: 800; margin: 0; }
    .sn-card.pln   .sn-header-title { color: #15803d; }
    .sn-card.other .sn-header-title { color: var(--navy); }
    .sn-header-sub { font-size: .75rem; margin: .1rem 0 0; }
    .sn-card.pln   .sn-header-sub { color: #16a34a; }
    .sn-card.other .sn-header-sub { color: var(--cyan2); }

    .sn-display {
        background: #fff; border-radius: 12px;
        padding: 1rem 1.25rem;
        display: flex; align-items: center; justify-content: space-between; gap: 1rem;
        margin-bottom: .75rem;
    }
    .sn-card.pln   .sn-display { border: 1.5px solid #bbf7d0; }
    .sn-card.other .sn-display { border: 1.5px solid rgba(3,174,198,0.2); }

    .sn-value { font-family: 'Courier New', monospace; font-size: 1.125rem; font-weight: 700; letter-spacing: .08em; word-break: break-all; }
    .sn-card.pln   .sn-value { color: #15803d; }
    .sn-card.other .sn-value { color: var(--navy); }

    .btn-copy { display: inline-flex; align-items: center; gap: .35rem; padding: .5rem .875rem; border-radius: 8px; color: #fff; border: none; font-family: 'Plus Jakarta Sans', sans-serif; font-size: .8rem; font-weight: 700; cursor: pointer; white-space: nowrap; transition: background .2s; flex-shrink: 0; }
    .sn-card.pln   .btn-copy { background: #16a34a; }
    .sn-card.pln   .btn-copy:hover { background: #15803d; }
    .sn-card.pln   .btn-copy.copied { background: #0f766e; }
    .sn-card.other .btn-copy { background: var(--cyan); }
    .sn-card.other .btn-copy:hover { background: var(--cyan2); }
    .sn-card.other .btn-copy.copied { background: #0f766e; }

    .sn-note { font-size: .75rem; display: flex; align-items: flex-start; gap: .4rem; line-height: 1.5; }
    .sn-card.pln   .sn-note { color: #16a34a; }
    .sn-card.other .sn-note { color: var(--cyan2); }
    .sn-note i { margin-top: 2px; flex-shrink: 0; }

    /* ── MAIN CARD ───────────────────────────────────── */
    .main-card { background: #fff; border-radius: 18px; box-shadow: 0 2px 16px rgba(1,41,77,0.07); border: 1.5px solid var(--border); overflow: hidden; margin-bottom: 1.25rem; }
    .card-head { padding: 1rem 1.5rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: .625rem; }
    .card-head-icon { width: 34px; height: 34px; border-radius: 9px; background: rgba(3,174,198,0.10); color: var(--cyan); display: flex; align-items: center; justify-content: center; font-size: .875rem; flex-shrink: 0; }
    .card-head-title { font-family: 'Syne', sans-serif; font-size: .9375rem; font-weight: 800; color: var(--navy); margin: 0; }
    .card-body { padding: .25rem 0; }

    .detail-row { display: flex; align-items: flex-start; padding: .8125rem 1.5rem; gap: 1rem; border-bottom: 1px solid #f8fafc; transition: background .15s; }
    .detail-row:last-child { border-bottom: none; }
    .detail-row:hover { background: #fafbfc; }

    .detail-key { width: 145px; flex-shrink: 0; font-size: .75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .05em; padding-top: .15rem; }
    .detail-val { flex: 1; font-size: .875rem; font-weight: 600; color: var(--navy); line-height: 1.5; word-break: break-word; }
    .detail-val.muted { color: var(--gray); font-weight: 500; font-style: italic; }

    /* status badge */
    .badge { display: inline-flex; align-items: center; gap: .3rem; padding: .28rem .7rem; border-radius: 999px; font-size: .7rem; font-weight: 700; }
    .badge-fulfilled  { background: #f0fdf4; color: #16a34a; }
    .badge-paid       { background: rgba(3,174,198,0.10); color: var(--cyan2); }
    .badge-processing { background: #fffbeb; color: #d97706; }
    .badge-failed     { background: #fef2f2; color: #dc2626; }
    .badge-pending    { background: #f8fafc; color: #94a3b8; }

    /* ── NOTICE / INFO BOX ───────────────────────────── */
    .notice-box { display: flex; align-items: flex-start; gap: .75rem; padding: 1rem 1.25rem; border-radius: 14px; font-size: .8125rem; font-weight: 500; line-height: 1.55; margin-bottom: 1.25rem; }
    .notice-box i { flex-shrink: 0; margin-top: 2px; }
    .notice-info    { background: rgba(3,174,198,0.07); border: 1px solid rgba(3,174,198,0.2); color: var(--cyan2); }
    .notice-warning { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }

    /* ── ACTIONS ─────────────────────────────────────── */
    .action-row { display: flex; align-items: center; gap: .75rem; flex-wrap: wrap; }
    .btn-home { display: inline-flex; align-items: center; gap: .4rem; padding: .625rem 1.375rem; border-radius: 10px; background: linear-gradient(135deg, var(--cyan), var(--cyan2)); color: #fff; font-family: 'Plus Jakarta Sans', sans-serif; font-size: .875rem; font-weight: 700; text-decoration: none; transition: box-shadow .2s, transform .2s; }
    .btn-home:hover { color: #fff; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(3,174,198,0.32); }
    .btn-history { display: inline-flex; align-items: center; gap: .4rem; padding: .625rem 1.25rem; border-radius: 10px; border: 2px solid var(--border); background: #fff; color: var(--gray); font-family: 'Plus Jakarta Sans', sans-serif; font-size: .875rem; font-weight: 600; text-decoration: none; transition: border-color .2s, color .2s; }
    .btn-history:hover { border-color: var(--navy); color: var(--navy); }

    @media (max-width: 500px) {
        .status-hero { flex-direction: column; text-align: center; padding: 1.375rem; }
        .btn-refresh { width: 100%; justify-content: center; }
        .detail-key { width: 110px; font-size: .7rem; }
        .sn-display { flex-direction: column; align-items: flex-start; }
        .btn-copy { width: 100%; justify-content: center; }
        .action-row { flex-direction: column; }
        .btn-home, .btn-history { width: 100%; justify-content: center; }
    }
</style>

@php
    $status    = $order->status;
    $heroClass = in_array($status, ['fulfilled','paid','processing','failed']) ? $status : 'pending';
    $targetType = $order->product->target_type ?? 'other';

    /* ── Teks status ramah user ── */
    $statusConfig = [
        'fulfilled'  => [
            'icon'  => 'fas fa-check-circle',
            'label' => 'Berhasil',
            'title' => 'Pesanan Selesai! 🎉',
            'sub'   => 'Produk sudah berhasil dikirim ke tujuan kamu.',
        ],
        'paid' => [
            'icon'  => 'fas fa-hourglass-half',
            'label' => 'Diproses',
            'title' => 'Pembayaran Diterima',
            'sub'   => 'Pesanan sedang kami proses, sebentar lagi selesai.',
        ],
        'processing' => [
            'icon'  => 'fas fa-spinner',
            'label' => 'Sedang Diproses',
            'title' => 'Pesanan Sedang Diproses',
            'sub'   => 'Mohon tunggu, produk sedang dikirimkan ke tujuan.',
        ],
        'failed' => [
            'icon'  => 'fas fa-times-circle',
            'label' => 'Gagal',
            'title' => 'Pesanan Tidak Berhasil',
            'sub'   => 'Terjadi kesalahan. Hubungi kami jika pembayaran sudah terpotong.',
        ],
        'pending' => [
            'icon'  => 'fas fa-clock',
            'label' => 'Menunggu Pembayaran',
            'title' => 'Belum Dibayar',
            'sub'   => 'Selesaikan pembayaran agar pesanan diproses.',
        ],
    ];
    $cfg = $statusConfig[$heroClass];

    $badgeClass = match($status) {
        'fulfilled'  => 'badge-fulfilled',
        'paid'       => 'badge-paid',
        'processing' => 'badge-processing',
        'failed'     => 'badge-failed',
        default      => 'badge-pending',
    };
    $badgeLabel = match($status) {
        'fulfilled'  => 'Berhasil',
        'paid'       => 'Diproses',
        'processing' => 'Diproses',
        'failed'     => 'Gagal',
        default      => 'Menunggu',
    };

    /* ── Label tujuan sesuai jenis produk ── */
    $targetLabel = match($targetType) {
        'phone' => 'Nomor HP',
        'pln'   => 'ID Pelanggan PLN',
        'ml'    => 'User ID & Zone ID',
        default => 'ID Tujuan',
    };

    /* ── Label & deskripsi SN sesuai jenis produk ── */
    $snConfig = match(true) {
        $targetType === 'pln' => [
            'cardClass' => 'pln',
            'icon'      => 'fas fa-bolt',
            'title'     => 'Token Listrik',
            'sub'       => 'Masukkan token ini ke meteran listrik kamu',
            'note'      => 'Ketuk angka token satu per satu ke keypad meteran listrik. Jika ada tanda titik/strip, abaikan saja.',
        ],
        str_contains(strtolower($order->product->category ?? ''), 'game') || $targetType === 'ml' => [
            'cardClass' => 'other',
            'icon'      => 'fas fa-gamepad',
            'title'     => 'Kode / Item Game',
            'sub'       => 'Konfirmasi pemrosesan dari provider',
            'note'      => 'Item akan masuk otomatis ke akun game kamu. Cek dalam beberapa saat.',
        ],
        default => [
            'cardClass' => 'other',
            'icon'      => 'fas fa-receipt',
            'title'     => 'Nomor Referensi',
            'sub'       => 'Bukti transaksi dari provider',
            'note'      => 'Simpan nomor ini sebagai bukti bahwa transaksi kamu berhasil diproses.',
        ],
    };

    /* ── Pesan gagal yang ramah ── */
    $failMessage = null;
    if ($status === 'failed' && !empty($order->digiflazz_message)) {
        $rawMsg = $order->digiflazz_message;
        // Terjemahkan pesan teknis ke bahasa user
        $failMessage = match(true) {
            str_contains($rawMsg, 'Insufficient')          => 'Saldo provider sedang tidak mencukupi. Silakan coba lagi nanti.',
            str_contains($rawMsg, 'timeout')               => 'Koneksi ke provider timeout. Silakan coba lagi.',
            str_contains($rawMsg, 'Invalid')               => 'Data tujuan tidak valid. Pastikan nomor/ID sudah benar.',
            str_contains($rawMsg, 'number not registered') => 'Nomor tujuan tidak terdaftar.',
            str_contains($rawMsg, 'Sukses')                => null, // fulfilled tapi masuk failed — jangan tampil
            default                                         => $rawMsg,
        };
    }
@endphp

<div class="show-wrap">

    {{-- BREADCRUMB --}}
    <nav class="breadcrumb">
        <a href="{{ route('home') }}"><i class="fas fa-store"></i> Beranda</a>
        <span>/</span>
        <a href="{{ route('orders.index') }}">Pesanan Saya</a>
        <span>/</span>
        <span style="color:var(--navy);font-weight:600;">Detail</span>
    </nav>

    {{-- STATUS HERO --}}
    <div class="status-hero {{ $heroClass }}">
        <div class="status-icon">
            <i class="{{ $cfg['icon'] }}"></i>
        </div>
        <div class="status-info">
            <div class="status-label">{{ $cfg['label'] }}</div>
            <h1 class="status-title">{{ $cfg['title'] }}</h1>
            <p class="status-sub">{{ $cfg['sub'] }}</p>
        </div>
        <a href="{{ request()->fullUrl() }}" class="btn-refresh">
            <i class="fas fa-rotate-right"></i> Refresh
        </a>
    </div>

    {{-- PESAN GAGAL (ramah user) --}}
    @if($failMessage)
        <div class="notice-box notice-warning">
            <i class="fas fa-triangle-exclamation"></i>
            <span>{{ $failMessage }}</span>
        </div>
    @endif

    {{-- SN / TOKEN (jika ada) --}}
    @if(!empty($order->digiflazz_sn))
        @php
            $rawSN = $order->digiflazz_sn;

            /**
             * Format SN PLN dari Digiflazz:
             * "0198-7723-9412-2680-5052/NAMA /R1/2200VA/..."
             *  ↑ Token 20 digit (5×4, dipisah strip) ada di awal, sebelum "/"
             *
             * Kita cari pola: digit & strip di awal string, maksimal sebelum "/" atau spasi
             */
            $plnToken = null;
            if ($targetType === 'pln') {
                // Pola 1: XX…XX-XX…XX-XX…XX-XX…XX-XX…XX (bisa lebih dari 4 digit per grup)
                if (preg_match('/^([\d][\d\-]{10,24}[\d])(?:\/|\s|$)/', trim($rawSN), $m)) {
                    $raw = preg_replace('/[^0-9]/', '', $m[1]); // ambil digit saja
                    if (strlen($raw) === 20) {
                        // Format ulang jadi XXXX-XXXX-XXXX-XXXX-XXXX
                        $plnToken = implode('-', str_split($raw, 4));
                    } else {
                        $plnToken = $m[1]; // pakai apa adanya jika digit bukan 20
                    }
                }
                // Pola 2: 20 digit beruntun di awal
                if (!$plnToken && preg_match('/^(\d{20})(?:\/|\s|$)/', trim($rawSN), $m)) {
                    $plnToken = implode('-', str_split($m[1], 4));
                }
                // Pola 3: cari di mana saja dalam string (fallback)
                if (!$plnToken && preg_match('/(\d{4}-\d{4}-\d{4}-\d{4}-\d{4})/', $rawSN, $m)) {
                    $plnToken = $m[1];
                }
            }
        @endphp

        <div class="sn-card {{ $snConfig['cardClass'] }}">
            <div class="sn-header">
                <div class="sn-header-icon"><i class="{{ $snConfig['icon'] }}"></i></div>
                <div>
                    <p class="sn-header-title">{{ $snConfig['title'] }}</p>
                    <p class="sn-header-sub">{{ $snConfig['sub'] }}</p>
                </div>
            </div>

            {{-- PLN: tampilkan token bersih di atas, raw di bawah --}}
            @if($targetType === 'pln' && $plnToken)
                {{-- TOKEN UTAMA (bersih, siap diinput) --}}
                <div style="margin-bottom:.875rem;">
                    <div style="font-size:.7rem;font-weight:700;color:#16a34a;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.625rem;">
                        <i class="fas fa-bolt"></i> Angka Token — masukkan ke meteran listrik
                    </div>
                    <div class="sn-display" style="border:2px solid #16a34a;background:#fff;padding:1.25rem;flex-wrap:wrap;gap:.75rem;align-items:center;">
                        {{-- tampilkan tiap grup pisah agar mudah dibaca --}}
                        <div style="display:flex;align-items:center;flex-wrap:wrap;gap:.375rem;flex:1;min-width:0;" id="snToken">
                            @foreach(explode('-', $plnToken) as $group)
                                <span style="font-family:'Courier New',monospace;font-size:1.5rem;font-weight:800;color:#15803d;letter-spacing:.05em;background:#f0fdf4;padding:.2rem .5rem;border-radius:8px;border:1px solid #bbf7d0;">{{ $group }}</span>
                                @if(!$loop->last)
                                    <span style="color:#86efac;font-size:1.125rem;font-weight:300;">–</span>
                                @endif
                            @endforeach
                        </div>
                        <button class="btn-copy" id="copyTokenBtn" onclick="copyPLNToken('{{ str_replace('-','',$plnToken) }}','copyTokenBtn')"
                            style="flex-shrink:0;align-self:center;">
                            <i class="fas fa-copy"></i> Salin
                        </button>
                    </div>
                    <div class="sn-note" style="margin-top:.5rem;">
                        <i class="fas fa-circle-info"></i>
                        Ketuk angka token satu per satu ke keypad meteran. Token terdiri dari <strong>20 digit</strong>.
                    </div>
                </div>

                {{-- INFO TAMBAHAN (collapsed) --}}
                <div style="border-top:1px solid #bbf7d0;padding-top:.875rem;margin-top:.25rem;">
                    <button onclick="toggleDetail()" id="toggleBtn"
                        style="background:none;border:none;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif;font-size:.75rem;font-weight:700;color:#16a34a;display:flex;align-items:center;gap:.375rem;padding:0;margin-bottom:.625rem;width:100%;text-align:left;">
                        <i class="fas fa-chevron-down" id="toggleIcon"></i>
                        <span>Lihat informasi lengkap dari PLN</span>
                    </button>
                    <div id="snDetail" style="display:none;">
                        <div style="background:#fff;border:1px solid #bbf7d0;border-radius:10px;padding:.875rem;">
                            <span style="font-family:'Courier New',monospace;font-size:.75rem;color:#475569;word-break:break-all;overflow-wrap:anywhere;line-height:1.7;display:block;margin-bottom:.75rem;" id="snRaw">{{ $rawSN }}</span>
                            <button class="btn-copy" id="copyRawBtn" onclick="copyText('snRaw','copyRawBtn')"
                                style="font-size:.75rem;padding:.45rem .875rem;width:100%;justify-content:center;">
                                <i class="fas fa-copy"></i> Salin Semua
                            </button>
                        </div>
                        <p style="font-size:.7rem;color:#6b7280;margin:.5rem 0 0;line-height:1.5;">
                            Informasi di atas dikirim langsung dari PLN dan mencakup data pelanggan, nominal, referensi, dan lainnya.
                        </p>
                    </div>
                </div>

            @else
                {{-- Non-PLN: tampilkan SN biasa --}}
                <div class="sn-display">
                    <span class="sn-value" id="snValue">{{ $rawSN }}</span>
                    <button class="btn-copy" id="copyBtn" onclick="copyText('snValue','copyBtn')">
                        <i class="fas fa-copy"></i> Salin
                    </button>
                </div>
                <div class="sn-note">
                    <i class="fas fa-circle-info"></i>
                    {{ $snConfig['note'] }}
                </div>
            @endif
        </div>
    @endif

    {{-- DETAIL PESANAN --}}
    <div class="main-card">
        <div class="card-head">
            <div class="card-head-icon"><i class="fas fa-bag-shopping"></i></div>
            <p class="card-head-title">Detail Pesanan</p>
        </div>
        <div class="card-body">

            <div class="detail-row">
                <span class="detail-key">Produk</span>
                <span class="detail-val">{{ $order->product->product_name ?? '—' }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-key">{{ $targetLabel }}</span>
                <span class="detail-val">
                    @if($targetType === 'ml' && str_contains($order->target, '|'))
                        @php [$uid, $sid] = explode('|', $order->target, 2); @endphp
                        User ID: <strong>{{ $uid }}</strong> &nbsp;·&nbsp; Zone: <strong>{{ $sid }}</strong>
                    @else
                        {{ $order->target }}
                    @endif
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-key">Status</span>
                <span class="detail-val">
                    <span class="badge {{ $badgeClass }}">
                        <i class="{{ $cfg['icon'] }}"></i> {{ $badgeLabel }}
                    </span>
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-key">Total Bayar</span>
                <span class="detail-val" style="font-family:'Poppins',sans-serif;font-size:1rem;font-weight:800;color:var(--cyan);">
                    Rp {{ number_format((int)$order->gross_amount, 0, ',', '.') }}
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-key">Tanggal</span>
                <span class="detail-val">{{ $order->created_at->format('d M Y, H:i') }} WIB</span>
            </div>

        </div>
    </div>

    {{-- INFO BOX --}}
    @if(in_array($status, ['pending', 'paid', 'processing']))
        <div class="notice-box notice-info">
            <i class="fas fa-circle-info"></i>
            <span>
                @if($status === 'pending')
                    Segera selesaikan pembayaran. Pesanan akan otomatis dibatalkan jika tidak dibayar dalam waktu yang ditentukan.
                @else
                    Pesanan sedang diproses. Refresh halaman ini beberapa saat lagi untuk melihat pembaruan status.
                @endif
            </span>
        </div>
    @endif

    {{-- ACTIONS --}}
    <div class="action-row">
        <a href="{{ route('home') }}" class="btn-home">
            <i class="fas fa-store"></i> Belanja Lagi
        </a>
        <a href="{{ route('orders.index') }}" class="btn-history">
            <i class="fas fa-list"></i> Semua Pesanan
        </a>
    </div>

</div>

<script>
function copyText(elId, btnId) {
    const val = document.getElementById(elId)?.innerText?.trim();
    if (!val) return;
    _doCopy(val, btnId);
}

// Khusus PLN: salin angka saja (tanpa strip), karena meteran tidak butuh strip
function copyPLNToken(digits, btnId) {
    _doCopy(digits, btnId);
}

function _doCopy(text, btnId) {
    const btn = document.getElementById(btnId);
    const origHTML = btn ? btn.innerHTML : '';

    const doFallback = () => {
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.style.cssText = 'position:fixed;opacity:0;top:0;left:0;pointer-events:none';
        document.body.appendChild(ta); ta.select();
        try { document.execCommand('copy'); } catch(e) {}
        document.body.removeChild(ta);
    };

    const onSuccess = () => {
        if (!btn) return;
        btn.innerHTML = '<i class="fas fa-check"></i> Disalin!';
        btn.classList.add('copied');
        setTimeout(() => {
            btn.innerHTML = origHTML;
            btn.classList.remove('copied');
        }, 2500);
    };

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(onSuccess).catch(() => { doFallback(); onSuccess(); });
    } else {
        doFallback();
        onSuccess();
    }
}

function toggleDetail() {
    const detail = document.getElementById('snDetail');
    const icon   = document.getElementById('toggleIcon');
    const btn    = document.getElementById('toggleBtn');
    if (!detail) return;
    const isOpen = detail.style.display !== 'none';
    detail.style.display = isOpen ? 'none' : 'block';
    icon.className = isOpen ? 'fas fa-chevron-down' : 'fas fa-chevron-up';
    const textNode = [...btn.childNodes].find(n => n.nodeType === 3 && n.textContent.trim());
    if (textNode) textNode.textContent = isOpen
        ? ' Lihat informasi lengkap dari PLN'
        : ' Sembunyikan informasi lengkap';
}
</script>
@endsection