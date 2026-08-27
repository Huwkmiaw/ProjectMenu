@extends('layouts.customer')

@section('title', 'Pilih Menu')

@push('styles')
<style>
    /* Touchscreen friendly global adjustments */
    * {
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
    }

    .pos-page {
        padding: 16px 0 60px;
    }

    .pos-layout {
        display: grid;
        grid-template-columns: 1fr 410px;
        gap: 24px;
        align-items: start;
    }

    /* ── LEFT: MENU SECTION ── */
    .menu-main {
        min-width: 0;
    }

    .filter-bar-wrap {
        background: var(--color-surface);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        padding: 16px 20px;
        margin-bottom: 20px;
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .search-input-wrap {
        position: relative;
        width: 100%;
    }
    .search-input-wrap input {
        padding-left: 48px;
        height: 50px;
        border-radius: var(--radius-full);
        font-size: 1rem;
        background: #f8fafc;
        border: 2px solid var(--color-border);
        font-weight: 500;
    }
    .search-input-wrap input:focus {
        border-color: var(--color-primary);
        box-shadow: 0 0 0 3px rgba(249,115,22,.15);
    }
    .search-input-wrap .search-icon {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--color-muted);
        pointer-events: none;
        display: flex;
        align-items: center;
    }

    .category-pills {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding-bottom: 6px;
        scrollbar-width: thin;
    }
    .category-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 22px;
        min-height: 48px;
        border-radius: var(--radius-full);
        border: 2px solid var(--color-border);
        background: #fff;
        font-size: .95rem;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition);
        white-space: nowrap;
        color: var(--color-text);
        flex-shrink: 0;
        user-select: none;
    }
    .category-pill:hover { border-color: var(--color-primary); color: var(--color-primary); }
    .category-pill:active { transform: scale(0.96); }
    .category-pill.active {
        background: var(--color-primary);
        border-color: var(--color-primary);
        color: #fff;
        box-shadow: 0 4px 14px rgba(249,115,22,.3);
    }

    /* ── MENU GRID ── */
    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
        gap: 18px;
    }

    .menu-card {
        background: var(--color-surface);
        border-radius: var(--radius-lg);
        border: 2px solid var(--color-border);
        overflow: hidden;
        transition: var(--transition);
        display: flex;
        flex-direction: column;
        box-shadow: var(--shadow-sm);
        position: relative;
        cursor: pointer;
        user-select: none;
    }
    .menu-card.hidden {
        display: none !important;
    }
    .menu-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-md);
        border-color: var(--color-primary);
    }
    .menu-card:active {
        transform: scale(0.97);
        border-color: var(--color-primary);
    }

    .menu-card-img-wrap {
        width: 100%;
        aspect-ratio: 4/3;
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        color: #94a3b8;
    }
    .menu-card-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .menu-card-content {
        padding: 16px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .menu-card-category {
        font-size: .75rem;
        font-weight: 700;
        color: var(--color-primary);
        text-transform: uppercase;
        letter-spacing: .06em;
        margin-bottom: 4px;
    }
    .menu-card-name {
        font-size: 1.05rem;
        font-weight: 800;
        line-height: 1.3;
        margin-bottom: 4px;
    }
    .menu-card-desc {
        font-size: .82rem;
        color: var(--color-text-light);
        line-height: 1.4;
        margin-bottom: 14px;
        flex: 1;
    }

    .menu-card-bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 12px;
        border-top: 1.5px solid var(--color-border);
        margin-top: auto;
    }
    .menu-card-price {
        font-size: 1.1rem;
        font-weight: 900;
        color: var(--color-secondary);
    }
    .btn-add-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--color-primary);
        color: #fff;
        border-radius: var(--radius-full);
        padding: 6px 14px;
        font-size: .88rem;
        font-weight: 800;
        box-shadow: 0 2px 8px rgba(249,115,22,.3);
        transition: transform .15s;
    }
    .menu-card:hover .btn-add-badge {
        background: var(--color-primary-dark);
        transform: scale(1.06);
    }

    /* ── RIGHT: PERSISTENT CART SIDEBAR ── */
    .cart-sidebar {
        background: var(--color-surface);
        border-radius: var(--radius-xl);
        border: 2px solid var(--color-border);
        box-shadow: var(--shadow-md);
        position: sticky;
        top: 80px;
        display: flex;
        flex-direction: column;
        max-height: calc(100vh - 100px);
        overflow: hidden;
    }

    .cart-header {
        padding: 18px 20px;
        background: #fff;
        border-bottom: 2px solid var(--color-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .cart-header h2 {
        font-size: 1.15rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .service-tag {
        font-size: .8rem;
        font-weight: 700;
        background: #fff7ed;
        color: #c2410c;
        padding: 6px 12px;
        border-radius: var(--radius-full);
        border: 1px solid #fed7aa;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .cart-items-scroll {
        padding: 16px 20px;
        overflow-y: auto;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .cart-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px dashed var(--color-border);
        animation: fadeIn .2s ease;
    }
    .cart-row:last-child { border-bottom: none; }
    .cart-row-info { flex: 1; min-width: 0; }
    .cart-row-name { font-weight: 800; font-size: .95rem; line-height: 1.25; margin-bottom: 3px; }
    .cart-row-desc { font-size: .78rem; color: var(--color-text-light); line-height: 1.35; margin-bottom: 4px; }
    .cart-row-price { font-size: .8rem; color: var(--color-muted); font-weight: 600; }

    /* Quantity Controls */
    .cart-qty-wrap {
        display: flex;
        align-items: center;
        border: 2px solid var(--color-border);
        border-radius: var(--radius-md);
        overflow: hidden;
        background: #f8fafc;
        margin-top: 4px;
    }
    .cart-qty-btn {
        width: 38px;
        height: 38px;
        border: none;
        background: transparent;
        font-size: 1.1rem;
        font-weight: 800;
        cursor: pointer;
        color: var(--color-text);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background .15s;
    }
    .cart-qty-btn:hover { background: #e2e8f0; }
    .cart-qty-btn:active { background: #cbd5e1; }
    .cart-qty-num {
        width: 34px;
        text-align: center;
        font-size: .95rem;
        font-weight: 800;
    }

    .cart-row-subtotal {
        font-weight: 900;
        font-size: .95rem;
        color: var(--color-primary);
        min-width: 78px;
        text-align: right;
        margin-top: 8px;
    }
    .cart-row-del {
        background: none;
        border: none;
        color: var(--color-muted);
        cursor: pointer;
        font-size: 1.2rem;
        padding: 6px 8px;
        min-width: 36px;
        min-height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: all .2s;
        margin-top: 4px;
    }
    .cart-row-del:hover { color: var(--color-danger); background: #fee2e2; }

    .cart-empty-state {
        text-align: center;
        padding: 48px 16px;
        color: var(--color-muted);
    }
    .cart-empty-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 12px;
        color: #cbd5e1;
    }

    .cart-footer {
        padding: 20px 22px;
        background: #f8fafc;
        border-top: 2px solid var(--color-border);
    }
    .cart-total-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }
    .cart-total-label { font-size: 1rem; font-weight: 700; color: var(--color-text-light); }
    .cart-total-val { font-size: 1.45rem; font-weight: 900; color: var(--color-secondary); }

    .touch-input {
        min-height: 48px;
        padding: 12px 16px;
        font-size: .98rem;
        border: 2px solid var(--color-border);
        border-radius: var(--radius-md);
        font-weight: 500;
    }

    /* Green Checkout Button */
    .btn-checkout-green {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        color: #fff;
        border: none;
        border-radius: var(--radius-md);
        padding: 16px 24px;
        min-height: 56px;
        font-size: 1.1rem;
        font-weight: 900;
        width: 100%;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: 0 6px 20px rgba(34, 197, 94, 0.4);
    }
    .btn-checkout-green:hover {
        background: linear-gradient(135deg, #16a34a, #15803d);
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(34, 197, 94, 0.5);
    }
    .btn-checkout-green:active { transform: translateY(0) scale(0.98); }
    .btn-checkout-green:disabled {
        background: #94a3b8;
        box-shadow: none;
        cursor: not-allowed;
        transform: none;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(4px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Toast */
    .toast {
        position: fixed;
        bottom: 24px; left: 24px;
        background: var(--color-secondary);
        color: #fff;
        padding: 14px 24px;
        border-radius: var(--radius-md);
        font-size: .95rem;
        font-weight: 700;
        box-shadow: var(--shadow-lg);
        z-index: 9999;
        transform: translateY(80px);
        opacity: 0;
        transition: all .3s cubic-bezier(.34,1.56,.64,1);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .toast.show { transform: translateY(0); opacity: 1; }

    @media (max-width: 960px) {
        .pos-layout { grid-template-columns: 1fr; }
        .cart-sidebar { position: static; max-height: none; }
    }
</style>
@endpush

@section('content')
<div class="pos-page">
    <div class="container">
        <div class="pos-layout">

            {{-- ── LEFT: MENU ITEMS ── --}}
            <section class="menu-main">
                {{-- Search & Category Bar (Client-Side Instant Filter) --}}
                <div class="filter-bar-wrap">
                    <div class="search-input-wrap">
                        <span class="search-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input
                            type="text"
                            id="liveSearchInput"
                            class="form-control touch-input"
                            placeholder="Cari makanan atau minuman..."
                            oninput="filterMenuCards()"
                            autocomplete="off"
                        >
                    </div>

                    <div class="category-pills" id="categoryPillsList">
                        <button type="button"
                                class="category-pill active"
                                data-cat="all"
                                onclick="selectCategory('all', this)">
                            Semua Menu
                        </button>

                        @foreach($categories as $cat)
                            <button type="button"
                                    class="category-pill"
                                    data-cat="{{ $cat->slug }}"
                                    onclick="selectCategory('{{ $cat->slug }}', this)">
                                {{ $cat->name }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Empty state when no cards match filter --}}
                <div class="empty-state" id="noFilterMatch" style="display:none; background:#fff; border-radius:var(--radius-lg); border:2px solid var(--color-border); padding:48px 24px; text-align:center;">
                    <div style="width:64px; height:64px; margin:0 auto 12px; color:var(--muted)">
                        <svg width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3>Menu tidak ditemukan</h3>
                    <p style="color:var(--color-muted); margin-top:4px;">Coba kata kunci lain atau pilih kategori di atas.</p>
                    <button type="button" class="btn btn-primary btn-lg mt-2" onclick="resetFilter()">Lihat Semua Menu</button>
                </div>

                {{-- Menu List Grid (Whole Card Clickable) --}}
                <div class="menu-grid" id="menuCardsGrid">
                    @foreach($menuItems as $item)
                        <div class="menu-card"
                             data-category="{{ $item->category->slug ?? '' }}"
                             data-name="{{ strtolower($item->name) }}"
                             data-desc="{{ strtolower($item->description ?? '') }}"
                             onclick="addItem({{ $item->id }}, '{{ addslashes($item->name) }}', this)"
                             title="Sentuh untuk masukkan ke pesanan">
                            <div class="menu-card-img-wrap">
                                @if($item->image)
                                    <img src="{{ $item->imageUrl }}" alt="{{ $item->name }}" class="menu-card-img" loading="lazy">
                                @else
                                    <svg width="44" height="44" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                @endif
                            </div>

                            <div class="menu-card-content">
                                <span class="menu-card-category">{{ $item->category->name }}</span>
                                <h3 class="menu-card-name">{{ $item->name }}</h3>
                                @if($item->description)
                                    <p class="menu-card-desc">{{ Str::limit($item->description, 55) }}</p>
                                @endif

                                <div class="menu-card-bottom">
                                    <span class="menu-card-price">{{ $item->formattedPrice }}</span>
                                    <span class="btn-add-badge">+ Tambah</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- ── RIGHT: PERSISTENT OPEN CART SIDEBAR ── --}}
            <aside class="cart-sidebar" id="cartSidebar">
                <div class="cart-header">
                    <h2>
                        Pesanan Anda
                    </h2>
                    <span class="service-tag">
                        @if(session('order_type') === 'dine_in')
                            <svg fill="currentColor" viewBox="0 0 73.602 46.542" style="width:18px; height:12px; vertical-align:middle; display:inline-block;"><g><path d="M10.429,4.148c-0.743,0-1.344,0.598-1.344,1.338v8.018c0,1.084-0.807,1.988-1.854,2.14V5.937 c0-0.735-0.602-1.338-1.345-1.338c-0.744,0-1.345,0.603-1.345,1.338v9.707c-1.043-0.152-1.854-1.056-1.854-2.14V5.486 c0-0.74-0.601-1.338-1.343-1.338C0.604,4.148,0,4.746,0,5.486v8.018c0,2.563,2.012,4.669,4.543,4.835v21.749 c0,0.744,0.601,1.342,1.345,1.342c0.743,0,1.345-0.598,1.345-1.342V18.339c2.532-0.166,4.543-2.273,4.543-4.835V5.486 C11.775,4.746,11.175,4.148,10.429,4.148z"></path><path d="M67.94,4.113c-2.329,0-2.154,4.669-2.154,10.426c0,3.801-0.076,7.126,0.542,8.948v16.601c0,0.744,0.601,1.342,1.344,1.342 c0.745,0,1.346-0.598,1.346-1.342V24.675c2.158-1.1,4.584-5.219,4.584-10.136C73.601,8.782,70.272,4.113,67.94,4.113z"></path><path d="M38.17,0C25.276,0,14.817,10.419,14.817,23.272c0,12.853,10.459,23.271,23.353,23.271 c12.901,0,23.359-10.418,23.359-23.271C61.529,10.419,51.071,0,38.17,0z"></path></g></svg>
                            Dine In
                        @else
                            <svg fill="currentColor" viewBox="0 0 463 463" style="width:14px; height:14px; vertical-align:middle; display:inline-block;"><g><path d="M367.372,142.726c-0.413-8.257-7.213-14.726-15.481-14.726H298.12l-12.974-82.169C280.953,19.274,258.396,0,231.49,0 c-26.885,0-49.442,19.274-53.635,45.831L164.881,128H111.11c-8.268,0-15.068,6.469-15.481,14.726l-15.2,304 c-0.211,4.22,1.338,8.396,4.25,11.457S91.685,463,95.911,463h271.18c4.225,0,8.318-1.756,11.23-4.817s4.461-7.236,4.25-11.457 L367.372,142.726z M192.672,48.171C195.708,28.95,212.032,15,231.511,15c19.458,0,35.784,13.95,38.819,33.171L282.935,128 H180.068L192.672,48.171z M367.453,447.845C367.305,448,367.149,448,367.091,448H95.911c-0.059,0-0.214,0-0.362-0.155 c-0.148-0.155-0.14-0.311-0.137-0.369l15.2-304c0.013-0.267,0.233-0.476,0.5-0.476h51.402l-2.421,15.33 c-0.646,4.092,2.147,7.932,6.238,8.578c0.396,0.063,0.79,0.093,1.179,0.093c3.626,0,6.815-2.636,7.399-6.331l2.79-17.67h107.604 l2.79,17.67c0.646,4.091,4.483,6.88,8.578,6.238c4.091-0.646,6.884-4.486,6.238-8.578l-2.42-15.33h51.402 c0.267,0,0.486,0.209,0.5,0.476l15.2,304C367.593,447.534,367.601,447.689,367.453,447.845z"></path><path d="M231.501,192c-4.142,0-7.5,3.357-7.5,7.5V240h-9v-40.5c0-4.143-3.358-7.5-7.5-7.5c-4.142,0-7.5,3.357-7.5,7.5V240h-9 v-40.5c0-4.143-3.358-7.5-7.5-7.5c-4.142,0-7.5,3.357-7.5,7.5v56c0,12.958,10.542,23.5,23.5,23.5h0.5v128.5 c0,4.143,3.358,7.5,7.5,7.5c4.142,0,7.5-3.357,7.5-7.5V279h0.5c12.958,0,23.5-10.542,23.5-23.5v-56 C239.001,195.357,235.643,192,231.501,192z M224.001,255.5c0,4.687-3.813,8.5-8.5,8.5h-16c-4.687,0-8.5-3.813-8.5-8.5V255h33 V255.5z"></path><path d="M287.719,206.754c-0.816-8.162-7.617-14.317-15.82-14.317c-8.767,0-15.899,7.133-15.899,15.899V407.5 c0,4.143,3.358,7.5,7.5,7.5s7.5-3.357,7.5-7.5V359h6.717c6.653,0,13.02-2.837,17.47-7.782s6.6-11.576,5.9-18.191L287.719,206.754 z M284.036,341.186C282.403,343,280.159,344,277.718,344H271V208.336c0-0.496,0.403-0.899,0.899-0.899 c0.464,0,0.849,0.348,0.899,0.853l13.372,126.316C286.427,337.033,285.669,339.37,284.036,341.186z"></path></g></svg>
                            Take Away
                        @endif
                        <a href="{{ route('welcome') }}" style="margin-left:8px; color:var(--color-primary); font-weight:800; text-decoration:underline;" title="Ganti Tipe Layanan">Ganti</a>
                    </span>
                </div>

                {{-- Dynamic Items Container --}}
                <div class="cart-items-scroll" id="cartItemsList">
                    @if(empty($cart))
                        <div class="cart-empty-state" id="cartEmptyState">
                            <div class="cart-empty-icon">
                                <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            </div>
                            <div style="font-weight:700; font-size:1rem; color:var(--color-text); margin-bottom:6px">Pesanan Masih Kosong</div>
                            <div style="font-size:.85rem; line-height:1.4;">Sentuh kartu menu di samping untuk langsung menambahkan ke pesanan Anda.</div>
                        </div>
                    @else
                        @foreach($cart as $id => $item)
                            <div class="cart-row" id="cart-row-{{ $id }}">
                                <div class="cart-row-info">
                                    <div class="cart-row-name">{{ $item['name'] }}</div>
                                    @if(!empty($item['description']))
                                        <div class="cart-row-desc">{{ Str::limit($item['description'], 50) }}</div>
                                    @endif
                                    <div class="cart-row-price">Rp {{ number_format($item['price'], 0, ',', '.') }}</div>
                                </div>
                                <div class="cart-qty-wrap">
                                    <button type="button" class="cart-qty-btn" onclick="updateItemQty({{ $id }}, -1)">−</button>
                                    <span class="cart-qty-num" id="qty-num({{ $id }})">{{ $item['quantity'] }}</span>
                                    <button type="button" class="cart-qty-btn" onclick="updateItemQty({{ $id }}, 1)">+</button>
                                </div>
                                <div class="cart-row-subtotal" id="subtotal-val-{{ $id }}">
                                    Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                </div>
                                <button type="button" class="cart-row-del" onclick="deleteItem({{ $id }})" title="Hapus">✕</button>
                            </div>
                        @endforeach
                    @endif
                </div>

                {{-- Cart Footer & Checkout Form --}}
                <div class="cart-footer" id="cartFooter" style="{{ empty($cart) ? 'display:none;' : '' }}">
                    <div class="cart-total-row">
                        <span class="cart-total-label">Total Pembayaran:</span>
                        <span class="cart-total-val" id="cartTotalDisplay">Rp {{ number_format($cartTotal, 0, ',', '.') }}</span>
                    </div>

                    <form action="{{ route('order.store') }}" method="POST" id="checkoutForm">
                        @csrf
                        <div style="margin-bottom:12px;">
                            <input
                                type="text"
                                name="customer_name"
                                id="customer_name"
                                class="form-control touch-input"
                                placeholder="Nama Pemesan (Wajib)"
                                required
                                maxlength="100"
                                oninput="persistFormInput('kiosk_cust_name', this.value)"
                            >
                        </div>

                        <div style="margin-bottom:16px;">
                            <input
                                type="text"
                                name="customer_note"
                                id="customer_note"
                                class="form-control touch-input"
                                placeholder="Catatan (cth: tidak pedas, dll)"
                                maxlength="255"
                                oninput="persistFormInput('kiosk_cust_note', this.value)"
                            >
                        </div>

                        <button type="submit" class="btn-checkout-green" id="btnOrderSubmit">
                            Pesan Sekarang
                        </button>
                    </form>
                </div>
            </aside>

        </div>
    </div>
</div>

<div class="toast" id="toast" aria-live="polite"></div>
@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let selectedCat = 'all';

    // ── PERSIST & RESTORE FORM INPUTS ──
    function persistFormInput(key, val) {
        try { sessionStorage.setItem(key, val); } catch (e) {}
    }

    document.addEventListener('DOMContentLoaded', () => {
        try {
            const savedName = sessionStorage.getItem('kiosk_cust_name');
            const savedNote = sessionStorage.getItem('kiosk_cust_note');
            if (savedName && document.getElementById('customer_name')) {
                document.getElementById('customer_name').value = savedName;
            }
            if (savedNote && document.getElementById('customer_note')) {
                document.getElementById('customer_note').value = savedNote;
            }
        } catch (e) {}
    });

    // ── CLIENT-SIDE INSTANT FILTERING ──
    function selectCategory(catSlug, btn) {
        selectedCat = catSlug;
        document.querySelectorAll('.category-pill').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');
        filterMenuCards();
    }

    function filterMenuCards() {
        const query = (document.getElementById('liveSearchInput')?.value || '').toLowerCase().trim();
        const cards = document.querySelectorAll('#menuCardsGrid .menu-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const cardCat  = card.dataset.category || '';
            const cardName = card.dataset.name || '';
            const cardDesc = card.dataset.desc || '';

            const matchesCat = (selectedCat === 'all' || cardCat === selectedCat);
            const matchesQuery = (!query || cardName.includes(query) || cardDesc.includes(query));

            if (matchesCat && matchesQuery) {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        const noMatch = document.getElementById('noFilterMatch');
        if (noMatch) {
            noMatch.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    function resetFilter() {
        document.getElementById('liveSearchInput').value = '';
        selectCategory('all', document.querySelector('.category-pill[data-cat="all"]'));
    }

    // ── CART LOGIC ──
    function formatRupiah(num) {
        return 'Rp ' + Math.round(num).toLocaleString('id-ID');
    }

    function renderCart(cartItems, formattedTotal, count) {
        const container = document.getElementById('cartItemsList');
        const footer    = document.getElementById('cartFooter');
        const totalDisp = document.getElementById('cartTotalDisplay');

        if (!cartItems || cartItems.length === 0) {
            container.innerHTML = `
                <div class="cart-empty-state" id="cartEmptyState">
                    <div class="cart-empty-icon">
                        <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                    <div style="font-weight:700; font-size:1rem; color:var(--color-text); margin-bottom:6px">Pesanan Masih Kosong</div>
                    <div style="font-size:.85rem; line-height:1.4;">Sentuh kartu menu di samping untuk langsung menambahkan ke pesanan Anda.</div>
                </div>
            `;
            footer.style.display = 'none';
            return;
        }

        footer.style.display = 'block';
        totalDisp.textContent = formattedTotal;

        let html = '';
        cartItems.forEach(item => {
            const descHtml = item.description ? `<div class="cart-row-desc">${item.description}</div>` : '';
            html += `
                <div class="cart-row" id="cart-row-${item.id}">
                    <div class="cart-row-info">
                        <div class="cart-row-name">${item.name}</div>
                        ${descHtml}
                        <div class="cart-row-price">${formatRupiah(item.price)}</div>
                    </div>
                    <div class="cart-qty-wrap">
                        <button type="button" class="cart-qty-btn" onclick="updateItemQty(${item.id}, -1)">−</button>
                        <span class="cart-qty-num" id="qty-num-${item.id}">${item.quantity}</span>
                        <button type="button" class="cart-qty-btn" onclick="updateItemQty(${item.id}, 1)">+</button>
                    </div>
                    <div class="cart-row-subtotal" id="subtotal-val-${item.id}">
                        ${formatRupiah(item.subtotal)}
                    </div>
                    <button type="button" class="cart-row-del" onclick="deleteItem(${item.id})" title="Hapus">✕</button>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    let addingLock = false;
    function addItem(id, name, cardEl) {
        if (addingLock) return;
        addingLock = true;

        if (cardEl) {
            cardEl.style.transform = 'scale(0.97)';
            setTimeout(() => { cardEl.style.transform = ''; }, 150);
        }

        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ menu_item_id: id, quantity: 1 }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                renderCart(data.cart, data.formatted_total, data.cart_count);
                showToast(name + ' masuk ke pesanan!');
            }
        })
        .catch(() => showToast('Terjadi kendala saat menambahkan.'))
        .finally(() => { addingLock = false; });
    }

    function updateItemQty(id, delta) {
        const qtyNum = document.getElementById('qty-num-' + id);
        let curr = parseInt(qtyNum ? qtyNum.textContent : '1') || 1;
        let next = curr + delta;

        if (next <= 0) {
            deleteItem(id);
            return;
        }

        fetch(`/cart/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ quantity: next }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                renderCart(data.cart, data.formatted_total, data.cart_count);
            }
        });
    }

    function deleteItem(id) {
        fetch(`/cart/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                renderCart(data.cart, data.formatted_total, data.cart_count);
            }
        });
    }

    function showToast(msg) {
        const toast = document.getElementById('toast');
        toast.textContent = msg;
        toast.classList.add('show');
        clearTimeout(window._toastTimer);
        window._toastTimer = setTimeout(() => toast.classList.remove('show'), 1800);
    }

    // Submit checkout
    document.getElementById('checkoutForm')?.addEventListener('submit', function(e) {
        const btn = document.getElementById('btnOrderSubmit');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span> Mengirim Pesanan...';
        try {
            sessionStorage.removeItem('kiosk_cust_name');
            sessionStorage.removeItem('kiosk_cust_note');
        } catch (e) {}
    });
</script>
@endpush
