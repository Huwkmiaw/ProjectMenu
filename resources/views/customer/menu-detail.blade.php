@extends('layouts.customer')

@section('title', $menuItem->name . ' — Detail Menu')

@push('styles')
<style>
    .detail-page { padding: 40px 0 80px; }
    .detail-card {
        background: var(--color-surface);
        border-radius: var(--radius-xl);
        border: 1px solid var(--color-border);
        box-shadow: var(--shadow-md);
        overflow: hidden;
        max-width: 800px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 1fr;
    }
    .detail-img-wrap {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 320px;
        position: relative;
        color: #94a3b8;
    }
    .detail-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .detail-body {
        padding: 36px 32px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .detail-category {
        font-size: .8rem;
        font-weight: 700;
        color: var(--color-primary);
        text-transform: uppercase;
        letter-spacing: .08em;
        margin-bottom: 8px;
    }
    .detail-title {
        font-size: 1.6rem;
        font-weight: 900;
        color: var(--color-secondary);
        line-height: 1.2;
        margin-bottom: 12px;
    }
    .detail-price {
        font-size: 1.5rem;
        font-weight: 900;
        color: var(--color-primary);
        margin-bottom: 16px;
    }
    .detail-desc {
        color: var(--color-text-light);
        font-size: .95rem;
        line-height: 1.6;
        margin-bottom: 28px;
    }
    .detail-actions {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    @media (max-width: 768px) {
        .detail-card { grid-template-columns: 1fr; }
        .detail-img-wrap { min-height: 240px; }
        .detail-body { padding: 24px; }
    }
</style>
@endpush

@section('content')
<div class="detail-page">
    <div class="container">
        <div style="margin-bottom: 20px; max-width: 800px; margin-left: auto; margin-right: auto;">
            <a href="{{ route('menu.index') }}" class="btn btn-secondary btn-sm">← Kembali ke Menu</a>
        </div>

        <div class="detail-card">
            <div class="detail-img-wrap">
                @if($menuItem->image)
                    <img src="{{ $menuItem->imageUrl }}" alt="{{ $menuItem->name }}" class="detail-img">
                @else
                    <svg width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                @endif
            </div>

            <div class="detail-body">
                <div>
                    <div class="detail-category">{{ $menuItem->category->name }}</div>
                    <h1 class="detail-title">{{ $menuItem->name }}</h1>
                    <div class="detail-price">{{ $menuItem->formattedPrice }}</div>
                    <p class="detail-desc">
                        {{ $menuItem->description ?: 'Menu lezat yang disiapkan khusus dengan bahan-bahan berkualitas tinggi untuk memanjakan lidah Anda.' }}
                    </p>
                </div>

                <div>
                    <div class="detail-actions">
                        <div class="qty-control">
                            <button class="qty-btn" type="button" onclick="changeDetailQty(-1)">−</button>
                            <input class="qty-input" type="number" id="detailQty" value="1" min="1" max="99">
                            <button class="qty-btn" type="button" onclick="changeDetailQty(1)">+</button>
                        </div>
                        <button type="button" class="btn btn-primary btn-full" id="btnAddToCart" onclick="addCurrentItemToCart()">
                            Tambah ke Pesanan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="toast" id="toast" aria-live="polite"></div>
@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const itemId = {{ $menuItem->id }};
    const itemName = '{{ addslashes($menuItem->name) }}';

    function changeDetailQty(delta) {
        const input = document.getElementById('detailQty');
        let val = parseInt(input.value) || 1;
        val = Math.max(1, Math.min(99, val + delta));
        input.value = val;
    }

    function addCurrentItemToCart() {
        const qty = parseInt(document.getElementById('detailQty').value) || 1;
        const btn = document.getElementById('btnAddToCart');
        btn.disabled = true;

        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ menu_item_id: itemId, quantity: qty }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast(qty + 'x ' + itemName + ' dimasukkan ke pesanan!');
            } else {
                showToast(data.message || 'Gagal menambahkan.');
            }
        })
        .catch(() => showToast('Terjadi gangguan koneksi.'))
        .finally(() => { btn.disabled = false; });
    }

    function showToast(msg) {
        const toast = document.getElementById('toast');
        toast.textContent = msg;
        toast.classList.add('show');
        clearTimeout(window._toastTimer);
        window._toastTimer = setTimeout(() => toast.classList.remove('show'), 2500);
    }
</script>
@endpush
