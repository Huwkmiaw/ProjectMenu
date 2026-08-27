@extends('layouts.customer')

@section('title', 'Keranjang Pesanan')

@push('styles')
<style>
    .cart-page { padding: 28px 0 80px; }
    .cart-layout {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 24px;
        align-items: start;
    }

    /* ── Cart Items ── */
    .cart-item {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 18px 0;
        border-bottom: 1px solid var(--color-border);
        animation: slideIn .25s ease;
    }
    .cart-item:last-child { border-bottom: none; }
    .cart-item-img {
        width: 72px; height: 72px;
        border-radius: var(--radius-md);
        object-fit: cover;
        background: var(--color-bg);
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        border: 1px solid var(--color-border);
    }
    .cart-item-info { flex: 1; min-width: 0; }
    .cart-item-name { font-weight: 700; margin-bottom: 4px; }
    .cart-item-price { font-size: .875rem; color: var(--color-text-light); }
    .cart-item-subtotal {
        font-size: 1rem;
        font-weight: 700;
        color: var(--color-primary);
        white-space: nowrap;
    }
    .cart-item-remove {
        background: none;
        border: none;
        cursor: pointer;
        color: var(--color-muted);
        font-size: 1.2rem;
        padding: 4px;
        transition: var(--transition);
        flex-shrink: 0;
    }
    .cart-item-remove:hover { color: var(--color-danger); transform: scale(1.2); }

    /* ── Summary Sidebar ── */
    .cart-summary {
        background: var(--color-surface);
        border-radius: var(--radius-xl);
        border: 1px solid var(--color-border);
        box-shadow: var(--shadow-md);
        position: sticky;
        top: 120px;
        overflow: hidden;
    }
    .cart-summary-header {
        background: linear-gradient(135deg, var(--color-primary), #fb923c);
        padding: 20px 24px;
        color: #fff;
    }
    .cart-summary-header h2 { font-size: 1.1rem; font-weight: 700; }
    .cart-summary-body { padding: 24px; }
    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        font-size: .9rem;
    }
    .summary-row.total {
        border-top: 2px solid var(--color-border);
        padding-top: 16px;
        margin-top: 8px;
        font-size: 1.1rem;
        font-weight: 800;
    }
    .summary-row.total span:last-child { color: var(--color-primary); }

    /* ── Order Info Badge ── */
    .order-info-badge {
        display: flex;
        align-items: center;
        gap: 10px;
        background: var(--color-primary-light);
        border: 1.5px solid #fed7aa;
        border-radius: var(--radius-md);
        padding: 12px 16px;
        margin-bottom: 20px;
        font-size: .875rem;
    }
    .order-info-badge strong { font-weight: 700; }

    @media (max-width: 768px) {
        .cart-layout { grid-template-columns: 1fr; }
        .cart-summary { position: static; }
    }
</style>
@endpush

@section('content')
<div class="cart-page">
    <div class="container">
        <div class="flex items-center gap-2 mb-3">
            <a href="{{ route('menu.index') }}" class="btn btn-secondary btn-sm">← Lanjut Belanja</a>
            <h1 style="font-size:1.4rem; font-weight:800;">🛒 Keranjang</h1>
        </div>

        @if(empty($cart))
            <div class="empty-state">
                <div class="empty-state-icon">🛒</div>
                <h3>Keranjang masih kosong</h3>
                <p>Yuk tambahkan menu favoritmu!</p>
                <a href="{{ route('menu.index') }}" class="btn btn-primary mt-3">Lihat Menu</a>
            </div>
        @else
            <div class="cart-layout">
                {{-- CART ITEMS --}}
                <div class="card">
                    <div class="card-body" id="cartItemsContainer">
                        @foreach($cart as $id => $item)
                            <div class="cart-item" id="cart-item-{{ $id }}">
                                <div class="cart-item-img">🍽️</div>
                                <div class="cart-item-info">
                                    <div class="cart-item-name">{{ $item['name'] }}</div>
                                    <div class="cart-item-price">Rp {{ number_format($item['price'], 0, ',', '.') }} / item</div>
                                </div>
                                <div class="qty-control">
                                    <button class="qty-btn" onclick="changeQty({{ $id }}, -1)">−</button>
                                    <input class="qty-input" type="number" value="{{ $item['quantity'] }}"
                                        min="1" max="99" id="qty-{{ $id }}"
                                        onchange="setQty({{ $id }}, this.value)">
                                    <button class="qty-btn" onclick="changeQty({{ $id }}, 1)">+</button>
                                </div>
                                <span class="cart-item-subtotal" id="subtotal-{{ $id }}">
                                    Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                </span>
                                <button class="cart-item-remove" onclick="removeItem({{ $id }})" title="Hapus">✕</button>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- SUMMARY & CHECKOUT --}}
                <div class="cart-summary">
                    <div class="cart-summary-header">
                        <h2>Ringkasan Pesanan</h2>
                    </div>
                    <div class="cart-summary-body">

                        {{-- Order Type Badge --}}
                        <div class="order-info-badge">
                            <span style="font-size:1.4rem">
                                {{ session('order_type') === 'dine_in' ? '🍽️' : '🥡' }}
                            </span>
                            <div>
                                <strong>{{ session('order_type') === 'dine_in' ? 'Dine In' : 'Take Away' }}</strong>
                                @if(session('order_type') === 'dine_in')
                                    <div style="font-size:.8rem; color:var(--color-text-light)">Meja: {{ session('table_number') }}</div>
                                @endif
                            </div>
                        </div>

                        {{-- Items Count --}}
                        <div class="summary-row">
                            <span>Jumlah item</span>
                            <span id="summary-item-count">{{ collect($cart)->sum('quantity') }} item</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total</span>
                            <span id="cart-grand-total">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>

                        <hr class="divider">

                        {{-- Checkout Form --}}
                        <form action="{{ route('order.store') }}" method="POST" id="checkoutForm">
                            @csrf
                            <div class="form-group">
                                <label class="form-label" for="customer_name">👤 Nama Anda <span style="color:var(--color-danger)">*</span></label>
                                <input type="text" name="customer_name" id="customer_name"
                                    class="form-control @error('customer_name') is-invalid @enderror"
                                    placeholder="cth: Budi Santoso"
                                    value="{{ old('customer_name') }}"
                                    required maxlength="100">
                                @error('customer_name')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="customer_note">📝 Catatan (opsional)</label>
                                <textarea name="customer_note" id="customer_note"
                                    class="form-control"
                                    placeholder="cth: tidak pakai pedas, kuah terpisah..."
                                    rows="3" maxlength="255">{{ old('customer_note') }}</textarea>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger">{{ $errors->first() }}</div>
                            @endif

                            <button type="submit" class="btn btn-primary btn-full btn-lg" id="checkoutBtn">
                                🚀 Pesan Sekarang
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    function formatRupiah(num) {
        return 'Rp ' + Math.round(num).toLocaleString('id-ID');
    }

    function changeQty(id, delta) {
        const input = document.getElementById('qty-' + id);
        const newQty = Math.max(1, Math.min(99, parseInt(input.value) + delta));
        input.value = newQty;
        setQty(id, newQty);
    }

    function setQty(id, qty) {
        qty = Math.max(1, Math.min(99, parseInt(qty) || 1));
        document.getElementById('qty-' + id).value = qty;

        fetch(`/cart/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ quantity: qty }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('subtotal-' + id).textContent = formatRupiah(data.item_subtotal);
                document.getElementById('cart-grand-total').textContent = formatRupiah(data.cart_total);
                document.getElementById('summary-item-count').textContent = data.cart_count + ' item';
                updateCartBadge(data.cart_count);
            }
        });
    }

    function removeItem(id) {
        if (!confirm('Hapus item ini dari keranjang?')) return;
        fetch(`/cart/${id}`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const el = document.getElementById('cart-item-' + id);
                el.style.opacity = '0';
                el.style.transform = 'translateX(40px)';
                el.style.transition = 'all .25s ease';
                setTimeout(() => {
                    el.remove();
                    document.getElementById('cart-grand-total').textContent = formatRupiah(data.cart_total);
                    document.getElementById('summary-item-count').textContent = data.cart_count + ' item';
                    updateCartBadge(data.cart_count);
                    if (data.cart_count === 0) location.reload();
                }, 250);
            }
        });
    }

    // Prevent double submit
    document.getElementById('checkoutForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('checkoutBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span> Memproses...';
    });
</script>
@endpush
