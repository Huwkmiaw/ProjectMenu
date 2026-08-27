@extends('layouts.customer')

@section('title', 'Pesanan Berhasil — ' . $order->order_code)

@push('styles')
<style>
    .confirmation-page {
        min-height: calc(100vh - 64px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 24px;
    }
    .confirmation-card {
        background: var(--color-surface);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--color-border);
        max-width: 520px;
        width: 100%;
        overflow: hidden;
        animation: fadeUp .5s ease;
    }
    .confirmation-header {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        padding: 36px 32px;
        text-align: center;
        color: #fff;
    }
    .success-icon {
        width: 80px; height: 80px;
        background: rgba(255,255,255,.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin: 0 auto 16px;
        animation: popIn .5s .2s cubic-bezier(.34,1.56,.64,1) both;
    }
    @keyframes popIn {
        from { transform: scale(0); opacity: 0; }
        to   { transform: scale(1); opacity: 1; }
    }
    .confirmation-header h1 { font-size: 1.5rem; font-weight: 800; margin-bottom: 6px; }
    .confirmation-header p  { opacity: .85; font-size: .95rem; }
    .order-code-display {
        background: rgba(255,255,255,.2);
        border-radius: var(--radius-md);
        padding: 10px 20px;
        display: inline-block;
        margin-top: 14px;
        font-size: 1.3rem;
        font-weight: 900;
        letter-spacing: .08em;
    }
    .confirmation-body { padding: 28px 32px; }
    .order-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 24px;
    }
    .meta-item { text-align: center; }
    .meta-label { font-size: .75rem; color: var(--color-muted); font-weight: 600; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 4px; }
    .meta-value { font-size: 1rem; font-weight: 700; color: var(--color-secondary); }
    .item-list { margin-bottom: 24px; }
    .item-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: .9rem; border-bottom: 1px dashed var(--color-border); }
    .item-row:last-child { border-bottom: none; }
    .total-row { display: flex; justify-content: space-between; font-size: 1.1rem; font-weight: 800; color: var(--color-primary); padding-top: 12px; }
    .cta-actions { display: flex; gap: 12px; }

    @media (max-width: 480px) {
        .confirmation-body { padding: 24px 20px; }
        .order-meta { grid-template-columns: 1fr 1fr; }
        .cta-actions { flex-direction: column; }
    }
</style>
@endpush

@section('content')
<div class="confirmation-page">
    <div class="confirmation-card">
        <div class="confirmation-header">
            <div class="success-icon">✅</div>
            <h1>Pesanan Diterima!</h1>
            <p>Simpan kode ini untuk melacak pesanan Anda</p>
            <div class="order-code-display">{{ $order->order_code }}</div>
        </div>

        <div class="confirmation-body">
            {{-- Meta --}}
            <div class="order-meta">
                <div class="meta-item">
                    <div class="meta-label">Nama</div>
                    <div class="meta-value">{{ $order->customer_name }}</div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Tipe Pesanan</div>
                    <div class="meta-value">
                        {{ $order->order_type === 'dine_in' ? '🍽️ Dine In' : '🥡 Take Away' }}
                    </div>
                </div>
                @if($order->isDineIn())
                    <div class="meta-item">
                        <div class="meta-label">Nomor Meja</div>
                        <div class="meta-value">{{ $order->table_number }}</div>
                    </div>
                @endif
                <div class="meta-item">
                    <div class="meta-label">Status</div>
                    <div class="meta-value">
                        <span class="badge badge-pending">⏳ Menunggu</span>
                    </div>
                </div>
            </div>

            <hr class="divider">

            {{-- Items --}}
            <div class="item-list">
                @foreach($order->items as $item)
                    <div class="item-row">
                        <span>{{ $item->menu_item_name }} <span style="color:var(--color-muted)">×{{ $item->quantity }}</span></span>
                        <span style="font-weight:600">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>

            @if($order->customer_note)
                <div style="background:var(--color-bg); border-radius:var(--radius-md); padding:12px 16px; font-size:.875rem; margin-bottom:20px;">
                    📝 <strong>Catatan:</strong> {{ $order->customer_note }}
                </div>
            @endif

            <div class="total-row">
                <span>Total Bayar</span>
                <span>{{ $order->formattedTotal }}</span>
            </div>

            <hr class="divider">

            <div class="cta-actions">
                <a href="{{ route('order.tracking', $order->order_code) }}" class="btn btn-primary btn-full">
                    📡 Lacak Status Pesanan
                </a>
                <a href="{{ route('menu.index') }}" class="btn btn-secondary btn-full">
                    🍜 Pesan Lagi
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
