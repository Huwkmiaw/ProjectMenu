<div class="order-card" id="order-card-{{ $order->id }}">
    <div class="order-card-header">
        <div>
            <div class="order-code">{{ $order->order_code }}</div>
            <div class="order-time">{{ $order->created_at->format('H:i') }}</div>
        </div>
        <div style="display:flex; gap:6px; flex-wrap:wrap; justify-content:flex-end">
            @if($order->isDineIn())
                <span class="badge badge-dine-in">Dine In</span>
            @else
                <span class="badge badge-take-away">Take Away</span>
            @endif
            <span class="badge badge-pending">Menunggu</span>
        </div>
    </div>

    <div style="font-weight:700; margin-bottom:4px; font-size:.95rem;">{{ $order->customer_name }}</div>

    <div class="order-items-list">
        {{ $order->items->map(fn($i) => $i->menu_item_name . ' ×' . $i->quantity)->join(', ') }}
    </div>

    @if($order->customer_note)
        <div style="font-size:.8rem; color:var(--text-light); margin-top:4px; background:#f8fafc; padding:6px 10px; border-radius:6px;">
            Catatan: {{ $order->customer_note }}
        </div>
    @endif

    <div class="flex justify-between items-center mt-2" style="border-top: 1px dashed var(--border); padding-top: 10px;">
        <span class="order-total">{{ $order->formattedTotal }}</span>
        <span style="font-size:.78rem; color:var(--muted)">{{ $order->items->count() }} item</span>
    </div>

    <div class="order-actions" style="margin-top:12px;">
        <button
            type="button"
            class="btn btn-success btn-full btn-sm"
            style="font-size:.9rem; font-weight:800; padding:10px;"
            onclick="openPaymentModal({{ $order->id }}, '{{ $order->order_code }}', '{{ addslashes($order->customer_name) }}', '{{ $order->isDineIn() ? 'Dine In' : 'Take Away' }}', {{ $order->total }}, {{ json_encode($order->items->map(fn($i) => ['name' => $i->menu_item_name, 'qty' => $i->quantity, 'subtotal' => $i->formattedSubtotal])) }})"
        >
            Bayar Langsung
        </button>

        <form method="POST" action="{{ route('cashier.orders.cancel', $order) }}" style="width:100%;">
            @csrf @method('PATCH')
            <button
                class="btn btn-secondary btn-full btn-xs"
                type="submit"
                style="color:var(--danger); border-color:#fecdd3;"
                onclick="return confirm('Batalkan pesanan {{ $order->order_code }}?')"
            >
                Batalkan Pesanan
            </button>
        </form>
    </div>
</div>
