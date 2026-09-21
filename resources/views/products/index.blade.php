@extends('layouts.app')

@section('content')
<style>
    /* fonts dimuat di layout utama — hanya deklarasi var di sini */
    :root {
        --cyan:   #03AEC6;
        --cyan2:  #029bb5;
        --navy:   #01294D;
        --gray:   #64748b;
        --border: #e2e8f0;
    }

    *, *::before, *::after { box-sizing: border-box; }
    .store-wrap { font-family: 'Plus Jakarta Sans', sans-serif; color: var(--navy); }

    /* ── HERO ──────────────────────────────────────── */
    .hero {
        position: relative; border-radius: 20px; overflow: hidden;
        margin-bottom: 1.75rem; padding: 2.5rem 2.75rem;
        background: linear-gradient(130deg, var(--navy) 0%, #024a8a 55%, #016e8a 100%);
        will-change: auto;
    }
    .hero::before {
        content: ''; position: absolute; inset: 0; pointer-events: none;
        background:
            radial-gradient(ellipse at 85% 50%, rgba(3,174,198,0.35) 0%, transparent 55%),
            radial-gradient(ellipse at 15% 90%, rgba(1,20,50,0.45) 0%, transparent 50%);
    }
    .hero-deco {
        position: absolute; right: -70px; top: -70px;
        width: 280px; height: 280px; border-radius: 50%;
        border: 48px solid rgba(3,174,198,0.10); pointer-events: none;
    }
    .hero-inner { position: relative; z-index: 2; }
    .hero-pill {
        display: inline-flex; align-items: center; gap: .4rem;
        background: rgba(3,174,198,0.18); border: 1px solid rgba(3,174,198,0.35);
        border-radius: 999px; padding: .3rem .875rem;
        font-size: .75rem; font-weight: 700; color: #7ee8f6;
        letter-spacing: .05em; text-transform: uppercase; margin-bottom: .75rem;
    }
    .hero-title {
        font-family: 'Syne', sans-serif;
        font-size: clamp(1.625rem, 3.5vw, 2.5rem);
        font-weight: 800; color: #fff; line-height: 1.15; margin: 0 0 .4rem;
    }
    .hero-title span { color: var(--cyan); }
    .hero-sub { font-size: .9rem; font-weight: 500; color: rgba(255,255,255,0.58); margin: 0; }

    /* ── SEARCH ────────────────────────────────────── */
    .search-wrap {
        background: #fff; border-radius: 18px; padding: 1.25rem 1.75rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 16px rgba(1,41,77,0.06);
    }
    .search-inner { position: relative; max-width: 580px; margin: 0 auto; }
    .search-inner .ico-search {
        position: absolute; left: 1rem; top: 50%;
        transform: translateY(-50%); color: #94a3b8; font-size: .9rem; pointer-events: none;
    }
    .search-input {
        width: 100%; height: 50px; padding: 0 9rem 0 2.875rem;
        border: 2px solid var(--border); border-radius: 12px;
        font-family: 'Plus Jakarta Sans', sans-serif; font-size: .9375rem;
        color: var(--navy); background: #f8fafc;
        transition: border-color .2s, box-shadow .2s;
    }
    .search-input:focus {
        outline: none; border-color: var(--cyan);
        background: #fff; box-shadow: 0 0 0 3px rgba(3,174,198,0.1);
    }
    .search-input::placeholder { color: #94a3b8; }
    .btn-search {
        position: absolute; right: 5px; top: 50%; transform: translateY(-50%);
        height: 38px; padding: 0 1.25rem;
        background: linear-gradient(135deg, var(--cyan), var(--cyan2));
        color: #fff; border: none; border-radius: 9px;
        font-family: 'Plus Jakarta Sans', sans-serif; font-size: .8125rem; font-weight: 700;
        cursor: pointer; white-space: nowrap;
        transition: box-shadow .2s;
    }
    .btn-search:hover { box-shadow: 0 5px 14px rgba(3,174,198,0.35); }

    /* ── SECTION HEADER ────────────────────────────── */
    .sec-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
    .sec-title {
        font-family: 'Syne', sans-serif; font-size: 1.125rem; font-weight: 800;
        color: var(--navy); display: flex; align-items: center; gap: .5rem; margin: 0;
    }
    .sec-title::before {
        content: ''; display: block; width: 3px; height: 1.125rem; border-radius: 3px;
        background: linear-gradient(180deg, var(--cyan), var(--cyan2)); flex-shrink: 0;
    }
    .sec-count { font-size: .8125rem; color: var(--gray); font-weight: 600; }

    /* ── CATEGORIES ────────────────────────────────── */
    .categories-wrap { margin-bottom: 2.25rem; }
    .categories-grid { display: flex; flex-wrap: wrap; justify-content: center; gap: .875rem; }

    .cat-card {
        position: relative; width: 148px; height: 105px;
        border-radius: 14px; overflow: hidden; text-decoration: none;
        display: block; box-shadow: 0 2px 10px rgba(1,41,77,0.09);
        transition: transform .25s, box-shadow .25s; flex-shrink: 0;
        /* avoid repaints on non-visible cards */
        contain: layout paint;
    }
    .cat-card:hover { transform: translateY(-4px) scale(1.02); box-shadow: 0 10px 28px rgba(1,41,77,0.14); }

    .cat-bg {
        position: absolute; inset: 0; background-color: #0d3b6e;
        background-size: cover; background-position: center;
        transition: transform .35s ease;
    }
    .cat-card:hover .cat-bg { transform: scale(1.07); }

    .cat-icon {
        position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
        font-size: 2rem; color: rgba(3,174,198,0.45); z-index: 1; transition: opacity .25s;
    }
    .cat-has-img .cat-icon { opacity: 0; }

    .cat-overlay {
        position: absolute; inset: 0; z-index: 2;
        background: linear-gradient(to top, rgba(1,41,77,0.88) 0%, rgba(1,41,77,0.22) 55%, transparent 100%);
        transition: background .25s;
    }
    .cat-card:hover .cat-overlay,
    .cat-card.active .cat-overlay {
        background: linear-gradient(to top, rgba(3,174,198,0.80) 0%, rgba(1,41,77,0.35) 55%, transparent 100%);
    }

    .cat-body { position: absolute; bottom: 0; left: 0; right: 0; padding: .75rem .875rem; z-index: 3; }
    .cat-name { font-size: .8125rem; font-weight: 800; color: #fff; line-height: 1.2; text-shadow: 0 1px 4px rgba(0,0,0,0.3); margin: 0; }
    .cat-sub  { font-size: .675rem; font-weight: 600; color: rgba(255,255,255,0.6); margin: .15rem 0 0; }

    .cat-card.active::after {
        content: ''; position: absolute; inset: 0;
        border: 2.5px solid var(--cyan); border-radius: 14px; z-index: 4; pointer-events: none;
    }
    .cat-tick {
        position: absolute; top: 8px; right: 8px; width: 20px; height: 20px;
        border-radius: 50%; background: var(--cyan); display: none;
        align-items: center; justify-content: center;
        z-index: 5; color: #fff; font-size: .6rem;
        box-shadow: 0 2px 6px rgba(3,174,198,0.5);
    }
    .cat-card.active .cat-tick { display: flex; }

    /* ── PRODUCT GRID ──────────────────────────────── */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1.125rem;
    }

    .product-card {
        background: #fff; border-radius: 14px; overflow: hidden;
        display: flex; flex-direction: column;
        box-shadow: 0 1px 8px rgba(1,41,77,0.06); border: 1.5px solid transparent;
        transition: transform .25s, box-shadow .25s, border-color .25s;
        contain: layout;         /* limit reflow scope */
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 28px rgba(3,174,198,0.12);
        border-color: rgba(3,174,198,0.25);
    }

    /* fixed-ratio image box prevents layout shift */
    .prod-img-wrap {
        position: relative; width: 100%; padding-top: 68%;
        background: #f1f5f9; overflow: hidden; flex-shrink: 0;
    }
    .prod-img {
        position: absolute; inset: 0; width: 100%; height: 100%;
        object-fit: cover;
        transition: transform .3s;
    }
    .product-card:hover .prod-img { transform: scale(1.05); }
    .no-img {
        position: absolute; inset: 0; display: flex;
        align-items: center; justify-content: center;
        color: #cbd5e0; font-size: 2.25rem;
    }

    .b-cat {
        position: absolute; top: 8px; left: 8px;
        background: rgba(1,41,77,0.80); backdrop-filter: blur(3px);
        color: #fff; padding: .25rem .55rem; border-radius: 6px;
        font-size: .675rem; font-weight: 700; z-index: 2;
        /* avoid expensive backdrop-filter on many items */
        will-change: auto;
    }
    .b-brand {
        position: absolute; top: 8px; right: 8px;
        background: rgba(3,174,198,0.85); backdrop-filter: blur(3px);
        color: #fff; padding: .25rem .55rem; border-radius: 6px;
        font-size: .675rem; font-weight: 700; z-index: 2;
    }

    .prod-body { padding: 1rem; display: flex; flex-direction: column; flex-grow: 1; }

    .prod-name {
        font-size: .875rem; font-weight: 700; color: var(--navy);
        line-height: 1.4;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
        overflow: hidden; min-height: 2.45rem; margin: 0 0 auto;
    }

    .prod-price {
        font-family: 'Poppins', sans-serif;
        font-size: 1.125rem; font-weight: 700; color: var(--cyan);
        margin: .75rem 0 .875rem;
    }

    .btn-select {
        display: flex; align-items: center; justify-content: center; gap: .4rem;
        width: 100%; padding: .625rem;
        background: linear-gradient(135deg, var(--cyan), var(--cyan2));
        color: #fff; border: none; border-radius: 9px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: .8125rem; font-weight: 700;
        cursor: pointer; text-decoration: none;
        transition: box-shadow .2s, transform .2s;
    }
    .btn-select:hover { color: #fff; transform: translateY(-2px); box-shadow: 0 6px 16px rgba(3,174,198,0.32); }

    /* ── EMPTY STATE ───────────────────────────────── */
    .empty-state {
        text-align: center; padding: 4rem 2rem;
        background: #fff; border-radius: 18px;
        box-shadow: 0 2px 12px rgba(1,41,77,0.05);
    }
    .empty-icon { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #e0f7fa, #b2ebf2); display: flex; align-items: center; justify-content: center; font-size: 2.25rem; color: var(--cyan); margin: 0 auto 1.25rem; }
    .empty-title { font-family: 'Syne', sans-serif; font-size: 1.375rem; font-weight: 800; color: var(--navy); margin: 0 0 .4rem; }
    .empty-sub { color: var(--gray); margin: 0 0 1.25rem; font-size: .9rem; }
    .btn-reset { display: inline-flex; align-items: center; gap: .4rem; padding: .625rem 1.5rem; background: linear-gradient(135deg, var(--cyan), var(--cyan2)); color: #fff; border-radius: 10px; font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 700; font-size: .875rem; text-decoration: none; transition: box-shadow .2s, transform .2s; }
    .btn-reset:hover { color: #fff; transform: translateY(-2px); box-shadow: 0 6px 16px rgba(3,174,198,0.3); }

    /* ── PAGINATION ────────────────────────────────── */
    .pagi-wrap { margin-top: 2.5rem; display: flex; flex-direction: column; align-items: center; gap: .75rem; }
    .pagi-info { font-size: .8125rem; color: var(--gray); font-weight: 500; }
    .pagi-info strong { color: var(--navy); }
    .pagi-list { display: flex; align-items: center; flex-wrap: wrap; gap: .3rem; justify-content: center; list-style: none; padding: 0; margin: 0; }
    .pagi-list li a, .pagi-list li span { display: inline-flex; align-items: center; justify-content: center; min-width: 2.25rem; height: 2.25rem; padding: 0 .75rem; border-radius: 9px; font-family: 'Plus Jakarta Sans', sans-serif; font-size: .8125rem; font-weight: 600; text-decoration: none !important; border: 2px solid var(--border); background: #fff; color: var(--navy) !important; transition: all .2s; cursor: pointer; }
    .pagi-list li a:hover { border-color: var(--cyan); color: var(--cyan) !important; background: rgba(3,174,198,0.07); transform: translateY(-1px); }
    .pagi-list li.active span { background: linear-gradient(135deg, var(--cyan), var(--cyan2)); border-color: var(--cyan); color: #fff !important; box-shadow: 0 3px 10px rgba(3,174,198,0.35); cursor: default; }
    .pagi-list li.disabled span { color: #cbd5e0 !important; border-color: #f1f5f9 !important; background: #f8fafc !important; cursor: not-allowed; }
    .pagi-list li:first-child a, .pagi-list li:first-child span,
    .pagi-list li:last-child a,  .pagi-list li:last-child span { padding: 0 1rem; border-radius: 10px; }
    .pagi-list li:first-child a:hover, .pagi-list li:last-child a:hover { background: linear-gradient(135deg, var(--cyan), var(--cyan2)); color: #fff !important; border-color: var(--cyan); }

    /* ── RESPONSIVE ────────────────────────────────── */
    @media (max-width: 900px) {
        .cat-card { width: 130px; height: 96px; }
        .products-grid { grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); }
    }
    @media (max-width: 600px) {
        .hero { padding: 1.625rem 1.375rem; }
        .search-wrap { padding: 1rem 1.125rem; }
        .cat-card { width: 110px; height: 84px; }
        .cat-name { font-size: .75rem; }
        .products-grid { grid-template-columns: repeat(2, 1fr); gap: .75rem; }
        .prod-body { padding: .875rem; }
        .prod-name { font-size: .8125rem; }
        .prod-price { font-size: 1rem; }
    }
</style>

<div class="store-wrap">

    {{-- HERO --}}
    <div class="hero">
        <div class="hero-deco"></div>
        <div class="hero-inner">
            <div class="hero-pill"><i class="fas fa-bolt"></i> Topup Cepat & Aman</div>
            <h1 class="hero-title">Top Up Game<br><span>Favorit Kamu</span></h1>
            <p class="hero-sub">Ribuan produk digital &middot; Proses instan &middot; Harga terbaik</p>
        </div>
    </div>

    {{-- SEARCH --}}
    <div class="search-wrap">
        <form method="GET" action="{{ route('products.index') }}">
            <div class="search-inner">
                <i class="fas fa-search ico-search"></i>
                <input type="text" name="q" class="search-input"
                    placeholder="Cari game, voucher, atau produk..."
                    value="{{ $q ?? request('q') }}">
                @if(!empty($category))
                    <input type="hidden" name="category" value="{{ $category }}">
                @elseif(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <button type="submit" class="btn-search">
                    <i class="fas fa-search"></i> Cari
                </button>
            </div>
        </form>
    </div>

    {{-- CATEGORIES --}}
    @php
        $fixedCategories = [
            'Semua'   => ['img' => null,                            'icon' => 'fas fa-th-large',    'value' => null],
            'Pulsa'   => ['img' => 'images/categories/pulsa.jpg',  'icon' => 'fas fa-phone-alt',   'value' => 'Pulsa'],
            'PLN'     => ['img' => 'images/categories/pln.jpg',    'icon' => 'fas fa-bolt',        'value' => 'PLN'],
            'Data'    => ['img' => 'images/categories/data.jpg',   'icon' => 'fas fa-wifi',        'value' => 'Data'],
            'E-money' => ['img' => 'images/categories/emoney.jpg', 'icon' => 'fas fa-credit-card', 'value' => 'E-money'],
            'Games'   => ['img' => 'images/categories/games.jpg',  'icon' => 'fas fa-gamepad',     'value' => 'Games'],
        ];
    @endphp

    <div class="categories-wrap">
        <div class="sec-header">
            <h2 class="sec-title">Kategori</h2>
        </div>
        <div class="categories-grid">
            @foreach($fixedCategories as $label => $meta)
                @php
                    $isActive = ($category ?? request('category')) === $meta['value'];
                    $hasImg   = !empty($meta['img']);
                    $bgStyle  = $hasImg ? 'background-image:url(' . asset($meta['img']) . ');' : '';
                    $url      = $meta['value']
                        ? route('products.index', array_filter(['q' => $q ?? null, 'category' => $meta['value']]))
                        : route('products.index', array_filter(['q' => $q ?? null]));
                @endphp
                <a href="{{ $url }}"
                   class="cat-card {{ $isActive ? 'active' : '' }} {{ $hasImg ? 'cat-has-img' : '' }}">
                    <div class="cat-tick"><i class="fas fa-check"></i></div>
                    <div class="cat-bg" style="{{ $bgStyle }}"></div>
                    <div class="cat-icon"><i class="{{ $meta['icon'] }}"></i></div>
                    <div class="cat-overlay"></div>
                    <div class="cat-body">
                        <p class="cat-name">{{ $label }}</p>
                        @if($label === 'Semua' && method_exists($products, 'total'))
                            <p class="cat-sub">{{ $products->total() }} produk</p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    {{-- PRODUCTS --}}
    <div class="sec-header">
        <h2 class="sec-title">{{ !empty($category) ? $category : 'Semua Produk' }}</h2>
        @if(method_exists($products, 'total'))
            <span class="sec-count">{{ $products->total() }} item</span>
        @endif
    </div>

    @forelse ($products as $p)
        @if($loop->first)<div class="products-grid">@endif

        <div class="product-card">
            <div class="prod-img-wrap">
                <span class="b-cat">{{ $p->category }}</span>
                <span class="b-brand">{{ $p->brand }}</span>
                @php
                    $img = null;
                    if (!empty($p->image))                              { $img = asset($p->image); }
                    elseif (file_exists(public_path('images/placeholder.png'))) { $img = asset('images/placeholder.png'); }
                @endphp
                @if($img)
                    {{-- loading=lazy mencegah semua gambar dimuat sekaligus --}}
                    {{-- decoding=async agar tidak memblokir main thread       --}}
                    <img src="{{ $img }}"
                         alt="{{ $p->product_name }}"
                         class="prod-img"
                         loading="lazy"
                         decoding="async"
                         width="400" height="272">
                @else
                    <div class="no-img"><i class="fas fa-image"></i></div>
                @endif
            </div>

            <div class="prod-body">
                <h3 class="prod-name">{{ $p->product_name }}</h3>
                <div class="prod-price">Rp {{ number_format((int)$p->price, 0, ',', '.') }}</div>
                <a href="{{ route('products.show', $p->id) }}" class="btn-select">
                    <i class="fas fa-shopping-cart"></i> Pilih
                </a>
            </div>
        </div>

        @if($loop->last)</div>@endif
    @empty
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-search"></i></div>
            <h3 class="empty-title">Produk Tidak Ditemukan</h3>
            <p class="empty-sub">Coba ubah kata kunci atau pilih kategori lain</p>
            @if(!empty(request('q')) || !empty(request('category')))
                <a href="{{ route('products.index') }}" class="btn-reset">
                    <i class="fas fa-redo"></i> Lihat Semua Produk
                </a>
            @endif
        </div>
    @endforelse

    {{-- PAGINATION --}}
    @if(method_exists($products, 'links') && $products->hasPages())
        <div class="pagi-wrap">
            <p class="pagi-info">
                Menampilkan
                <strong>{{ $products->firstItem() }}–{{ $products->lastItem() }}</strong>
                dari <strong>{{ $products->total() }}</strong> produk
            </p>
            <ul class="pagi-list">
                <li class="{{ $products->onFirstPage() ? 'disabled' : '' }}">
                    @if($products->onFirstPage())
                        <span>&#8592; Prev</span>
                    @else
                        <a href="{{ $products->previousPageUrl() }}" rel="prev">&#8592; Prev</a>
                    @endif
                </li>
                @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                    <li class="{{ $page == $products->currentPage() ? 'active' : '' }}">
                        @if($page == $products->currentPage())
                            <span>{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    </li>
                @endforeach
                <li class="{{ !$products->hasMorePages() ? 'disabled' : '' }}">
                    @if($products->hasMorePages())
                        <a href="{{ $products->nextPageUrl() }}" rel="next">Next &#8594;</a>
                    @else
                        <span>Next &#8594;</span>
                    @endif
                </li>
            </ul>
        </div>
    @endif

</div>
@endsection