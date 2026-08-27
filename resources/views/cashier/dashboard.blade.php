@extends('layouts.dashboard')

@section('title', 'Dashboard Kasir')
@section('page-title', 'Dashboard Kasir')

@section('topbar-actions')
    <span id="connectionStatus" style="font-size:.8rem; color:var(--success); display:flex; align-items:center; gap:6px;">
        <span style="width:8px;height:8px;border-radius:50%;background:var(--success);display:inline-block;animation:dotPulse 1.5s ease infinite;"></span>
        Live
    </span>
@endsection

@push('styles')
<style>
    @keyframes dotPulse { 0%,100%{opacity:1} 50%{opacity:.2} }
    .orders-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; }

    /* Notification banner */
    .new-order-banner {
        background: linear-gradient(135deg, var(--primary), #fb923c);
        color: #fff;
        padding: 12px 20px;
        border-radius: var(--radius-md);
        display: none;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 20px;
        animation: slideIn .3s ease;
        box-shadow: 0 4px 20px rgba(249,115,22,.4);
    }
    .new-order-banner.show { display: flex; }

    /* Modal Overlay & Card */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 16px;
    }
    .modal-overlay.active { display: flex; animation: fadeIn .2s ease; }

    .modal-card {
        background: var(--surface);
        border-radius: var(--radius-xl);
        max-width: 520px;
        width: 100%;
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--border);
        overflow: hidden;
        animation: popModal .3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .modal-header {
        padding: 18px 24px;
        background: linear-gradient(135deg, var(--secondary), #0f172a);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .modal-header h3 { font-size: 1.15rem; font-weight: 800; }
    .modal-body { padding: 24px; }

    /* Tabs for Payment Method */
    .pay-method-tabs {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 20px;
    }
    .pay-tab-btn {
        background: #f8fafc;
        border: 2px solid var(--border);
        border-radius: var(--radius-md);
        padding: 14px;
        font-weight: 700;
        font-size: .95rem;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        transition: var(--transition);
        color: var(--text);
    }
    .pay-tab-btn:hover { border-color: var(--primary); }
    .pay-tab-btn.active {
        border-color: var(--primary);
        background: #fff7ed;
        color: var(--primary);
        box-shadow: 0 2px 10px rgba(249,115,22,.15);
    }

    /* Quick Cash Nominal Buttons */
    .quick-cash-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        margin-top: 10px;
    }
    .quick-cash-btn {
        background: var(--surface);
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 8px;
        font-size: .85rem;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition);
        text-align: center;
    }
    .quick-cash-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    .change-box {
        background: #f0fdf4;
        border: 1.5px solid #bbf7d0;
        border-radius: var(--radius-md);
        padding: 14px 18px;
        margin-top: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .change-box .change-label { font-size: .85rem; font-weight: 700; color: #166534; }
    .change-box .change-val { font-size: 1.3rem; font-weight: 900; color: #16a34a; }

    @keyframes popModal {
        from { transform: scale(0.9); opacity: 0; }
        to   { transform: scale(1); opacity: 1; }
    }
</style>
@endpush

@section('content')
{{-- Stats Row --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon orange">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="stat-value" id="stat-pending">{{ $todayStats['pending'] }}</div>
            <div class="stat-label">Menunggu Pembayaran</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div>
            <div class="stat-value" id="stat-completed">{{ $todayStats['completed'] + $todayStats['paid'] }}</div>
            <div class="stat-label">Transaksi Selesai</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="stat-value" style="font-size:1.2rem" id="stat-revenue">Rp {{ number_format($todayStats['revenue'], 0, ',', '.') }}</div>
            <div class="stat-label">Omzet Hari Ini</div>
        </div>
    </div>
</div>

{{-- New Order Alert Banner --}}
<div class="new-order-banner" id="newOrderBanner">
    <span>Ada pesanan baru masuk dari pelanggan!</span>
    <button onclick="document.getElementById('newOrderBanner').classList.remove('show')"
        style="background:rgba(255,255,255,.2);border:none;color:#fff;padding:4px 12px;border-radius:6px;cursor:pointer;font-weight:600">
        OK
    </button>
</div>

{{-- Pending Orders Card --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">
            Pesanan Masuk (Pending)
            <span id="pending-badge" class="badge badge-pending" style="margin-left:8px">{{ $pendingOrders->count() }}</span>
        </span>
        <div class="flex gap-1">
            <a href="{{ route('cashier.orders.history') }}" class="btn btn-secondary btn-sm">Riwayat Transaksi</a>
        </div>
    </div>

    <div style="padding: 20px;">
        <div id="noPendingMsg" style="{{ $pendingOrders->isEmpty() ? 'display:block;' : 'display:none;' }} text-align:center; padding:48px 20px; color:var(--muted);">
            <div style="width:56px; height:56px; margin:0 auto 12px; color:var(--success)">
                <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div style="font-weight:700; font-size:1.1rem; color:var(--text)">Tidak ada pesanan pending</div>
            <div style="font-size:.85rem; margin-top:4px">Pesanan baru yang dibuat oleh pelanggan akan otomatis muncul di sini.</div>
        </div>

        <div class="orders-grid" id="ordersGrid">
            @foreach($pendingOrders as $order)
                @include('cashier.orders._card', ['order' => $order])
            @endforeach
        </div>
    </div>
</div>

{{-- ── PAYMENT POP-UP MODAL ── --}}
<div class="modal-overlay" id="paymentModalOverlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3>Proses Pembayaran</h3>
            <button type="button" onclick="closePaymentModal()" style="background:none; border:none; color:#fff; font-size:1.4rem; cursor:pointer;">✕</button>
        </div>

        <div class="modal-body">
            {{-- Order Brief Info --}}
            <div style="background:#f8fafc; border:1px solid var(--border); border-radius:var(--radius-md); padding:14px; margin-bottom:20px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                    <strong id="modalOrderCode" style="color:var(--primary); font-size:1.05rem;">ORD-XXXX</strong>
                    <span id="modalOrderType" class="badge badge-dine-in">Dine In</span>
                </div>
                <div style="font-size:.9rem; margin-bottom:6px;">
                    Pelanggan: <strong id="modalCustomerName">Nama</strong>
                </div>
                <div id="modalItemsList" style="font-size:.8rem; color:var(--text-light); max-height:80px; overflow-y:auto; border-top:1px dashed var(--border); padding-top:6px; margin-top:6px;">
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:10px; border-top:1.5px solid var(--border); padding-top:8px;">
                    <span style="font-weight:700; font-size:.95rem;">Total Tagihan:</span>
                    <strong id="modalOrderTotal" style="font-size:1.3rem; color:var(--secondary);">Rp 0</strong>
                </div>
            </div>

            {{-- Payment Form --}}
            <form id="paymentProcessForm" onsubmit="submitPayment(event)">
                <input type="hidden" id="modalOrderId" name="order_id">
                <input type="hidden" id="modalPaymentMethod" name="payment_method" value="cash">

                <label class="form-label" style="margin-bottom:8px;">Pilih Metode Pembayaran *</label>
                <div class="pay-method-tabs">
                    <button type="button" class="pay-tab-btn active" id="tabCash" onclick="selectPayMethod('cash')">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Tunai (Cash)</span>
                    </button>
                    <button type="button" class="pay-tab-btn" id="tabCashless" onclick="selectPayMethod('cashless')">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span>Non-Tunai (QRIS/Debit)</span>
                    </button>
                </div>

                {{-- Cash Section --}}
                <div id="cashInputSection">
                    <label class="form-label" for="inputAmountPaid">Nominal Uang Diterima (Rp)</label>
                    <input
                        type="number"
                        id="inputAmountPaid"
                        class="form-control"
                        placeholder="Masukkan jumlah uang tunai"
                        oninput="calculateChange()"
                        style="font-size:1.1rem; font-weight:700; padding:12px;"
                    >

                    <div class="quick-cash-grid" id="quickCashGrid">
                        <!-- populated dynamically -->
                    </div>

                    <div class="change-box">
                        <span class="change-label">Kembalian:</span>
                        <span class="change-val" id="displayChangeVal">Rp 0</span>
                    </div>
                </div>

                {{-- Non-Cash Section --}}
                <div id="cashlessSection" style="display:none; background:#eff6ff; border:1px solid #bfdbfe; border-radius:var(--radius-md); padding:16px; text-align:center; color:#1e40af; font-size:.9rem;">
                    <div style="font-weight:700;">Pembayaran Non-Tunai</div>
                    <div style="font-size:.82rem; margin-top:4px;">Pastikan transaksi QRIS / Mesin EDC kasir telah berhasil sebelum menekan tombol di bawah.</div>
                </div>

                <div style="margin-top:24px; display:flex; gap:10px;">
                    <button type="button" class="btn btn-secondary" onclick="closePaymentModal()" style="flex:1;">Batal</button>
                    <button type="submit" class="btn btn-success" id="btnSubmitPayment" style="flex:2; font-weight:800; font-size:1rem; padding:12px;">
                        Selesaikan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let knownOrderIds = new Set({{ json_encode($pendingOrders->pluck('id')->toArray()) }});
    let audioCtx = null;
    let currentOrderTotal = 0;
    let currentOrderId = null;

    function formatRupiah(num) {
        return 'Rp ' + Math.round(num).toLocaleString('id-ID');
    }

    // Web Audio API — synthetic notification bell
    function playNotificationSound() {
        try {
            if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.type = 'sine';
            osc.frequency.setValueAtTime(900, audioCtx.currentTime);
            osc.frequency.setValueAtTime(650, audioCtx.currentTime + 0.15);
            gain.gain.setValueAtTime(0.4, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.5);
            osc.start(audioCtx.currentTime);
            osc.stop(audioCtx.currentTime + 0.5);
        } catch (e) {}
    }

    document.addEventListener('click', function initAudio() {
        if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        document.removeEventListener('click', initAudio);
    }, { once: true });

    // Open Payment Modal
    function openPaymentModal(orderId, orderCode, customerName, serviceType, total, items) {
        currentOrderId = orderId;
        currentOrderTotal = parseFloat(total);

        document.getElementById('modalOrderId').value = orderId;
        document.getElementById('modalOrderCode').textContent = orderCode;
        document.getElementById('modalCustomerName').textContent = customerName;
        document.getElementById('modalOrderType').textContent = serviceType;
        document.getElementById('modalOrderTotal').textContent = formatRupiah(currentOrderTotal);

        // Populate items list
        let itemsHtml = '';
        if (Array.isArray(items)) {
            itemsHtml = items.map(i => `<div>• ${i.name} ×${i.qty} (${i.subtotal})</div>`).join('');
        }
        document.getElementById('modalItemsList').innerHTML = itemsHtml;

        // Reset to Cash
        selectPayMethod('cash');

        // Set quick cash buttons
        setupQuickCashButtons(currentOrderTotal);

        document.getElementById('paymentModalOverlay').classList.add('active');
    }

    function closePaymentModal() {
        document.getElementById('paymentModalOverlay').classList.remove('active');
        document.getElementById('inputAmountPaid').value = '';
    }

    function selectPayMethod(method) {
        document.getElementById('modalPaymentMethod').value = method;
        document.getElementById('tabCash').classList.toggle('active', method === 'cash');
        document.getElementById('tabCashless').classList.toggle('active', method === 'cashless');

        const cashSec = document.getElementById('cashInputSection');
        const cashlessSec = document.getElementById('cashlessSection');

        if (method === 'cash') {
            cashSec.style.display = 'block';
            cashlessSec.style.display = 'none';
            document.getElementById('inputAmountPaid').value = currentOrderTotal;
            calculateChange();
        } else {
            cashSec.style.display = 'none';
            cashlessSec.style.display = 'block';
        }
    }

    function setupQuickCashButtons(total) {
        const grid = document.getElementById('quickCashGrid');
        const nominals = [total];

        // Suggest standard Indonesian Rupiah notes
        [20000, 50000, 100000, 200000].forEach(n => {
            if (n > total && !nominals.includes(n)) {
                nominals.push(n);
            }
        });

        grid.innerHTML = nominals.slice(0, 4).map((nom, idx) => `
            <button type="button" class="btn-quick-cash" onclick="setCashAmount(${nom})">
                ${idx === 0 ? 'Uang Pas' : formatRupiah(nom)}
            </button>
        `).join('');

        document.getElementById('inputAmountPaid').value = total;
        calculateChange();
    }

    function setCashAmount(val) {
        document.getElementById('inputAmountPaid').value = val;
        calculateChange();
    }

    function calculateChange() {
        const paid = parseFloat(document.getElementById('inputAmountPaid').value) || 0;
        const change = Math.max(0, paid - currentOrderTotal);
        document.getElementById('displayChangeVal').textContent = formatRupiah(change);
    }

    function submitPayment(e) {
        e.preventDefault();
        const method = document.getElementById('modalPaymentMethod').value;
        const paid = parseFloat(document.getElementById('inputAmountPaid').value) || 0;

        if (method === 'cash' && paid < currentOrderTotal) {
            alert('Nominal uang tunai kurang dari total tagihan!');
            return;
        }

        const btn = document.getElementById('btnSubmitPayment');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span> Memproses...';

        fetch(`/cashier/orders/${currentOrderId}/pay`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                payment_method: method,
                amount_paid: method === 'cash' ? paid : currentOrderTotal,
            }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                closePaymentModal();
                // Remove card from UI
                const card = document.getElementById('order-card-' + currentOrderId);
                if (card) {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    setTimeout(() => card.remove(), 250);
                }
                knownOrderIds.delete(currentOrderId);
                updateCounts();
                alert(`Pembayaran Berhasil!\nMetode: ${data.payment_method === 'cash' ? 'Tunai' : 'Non-Tunai'}\nKembalian: ${data.formatted_change}`);
            } else {
                alert(data.message || 'Gagal memproses pembayaran.');
            }
        })
        .catch(() => alert('Gangguan koneksi ke server.'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = 'Selesaikan Pembayaran';
        });
    }

    function updateCounts() {
        const remaining = document.querySelectorAll('#ordersGrid .order-card').length;
        document.getElementById('pending-badge').textContent = remaining;
        document.getElementById('stat-pending').textContent = remaining;
        document.getElementById('noPendingMsg').style.display = remaining === 0 ? 'block' : 'none';
    }

    // Polling for new orders
    function pollPendingOrders() {
        fetch('{{ route("cashier.orders.pending") }}', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
        })
        .then(r => r.json())
        .then(data => {
            const orders = data.orders;
            const newIds = new Set(orders.map(o => o.id));
            let hasNew = false;

            orders.forEach(order => {
                if (!knownOrderIds.has(order.id)) {
                    hasNew = true;
                    appendOrderCard(order);
                }
            });

            knownOrderIds.forEach(id => {
                if (!newIds.has(id)) {
                    const el = document.getElementById('order-card-' + id);
                    if (el) el.remove();
                }
            });

            knownOrderIds = newIds;
            updateCounts();

            if (hasNew) {
                playNotificationSound();
                document.getElementById('newOrderBanner').classList.add('show');
                setTimeout(() => document.getElementById('newOrderBanner').classList.remove('show'), 6000);
            }
        })
        .catch(() => {});
    }

    function appendOrderCard(order) {
        const grid = document.getElementById('ordersGrid');
        const typeBadge = order.order_type === 'dine_in'
            ? `<span class="badge badge-dine-in">Dine In</span>`
            : `<span class="badge badge-take-away">Take Away</span>`;

        const itemsList = order.items.map(i => `${i.name} ×${i.quantity}`).join(', ');
        const note = order.customer_note
            ? `<div style="font-size:.8rem;color:var(--text-light);margin-top:4px;background:#f8fafc;padding:6px 10px;border-radius:6px;">Catatan: ${order.customer_note}</div>` : '';

        grid.insertAdjacentHTML('afterbegin', `
            <div class="order-card" id="order-card-${order.id}">
                <div class="order-card-header">
                    <div>
                        <div class="order-code">${order.order_code}</div>
                        <div class="order-time">${order.created_at}</div>
                    </div>
                    <div style="display:flex;gap:6px;flex-wrap:wrap;justify-content:flex-end">
                        ${typeBadge}
                        <span class="badge badge-pending">Menunggu</span>
                    </div>
                </div>
                <div style="font-weight:700;margin-bottom:4px;font-size:.95rem;">${order.customer_name}</div>
                <div class="order-items-list">${itemsList}</div>
                ${note}
                <div class="flex justify-between items-center mt-2" style="border-top:1px dashed var(--border);padding-top:10px;">
                    <span class="order-total">${order.formatted_total}</span>
                    <span style="font-size:.78rem;color:var(--muted)">${order.items_count} item</span>
                </div>
                <div class="order-actions" style="margin-top:12px;">
                    <button
                        type="button"
                        class="btn btn-success btn-full btn-sm"
                        style="font-size:.9rem;font-weight:800;padding:10px;"
                        onclick='openPaymentModal(${order.id}, "${order.order_code}", "${order.customer_name.replace(/"/g, '&quot;')}", "${order.order_type === 'dine_in' ? 'Dine In (Meja ' + order.table_number + ')' : 'Take Away'}", ${order.total}, ${JSON.stringify(order.items.map(i => ({name: i.name, qty: i.quantity, subtotal: i.subtotal})))})'
                    >
                        💳 Bayar Langsung
                    </button>
                    <form method="POST" action="/cashier/orders/${order.id}/cancel" style="width:100%;">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="PATCH">
                        <button class="btn btn-secondary btn-full btn-xs" type="submit" style="color:var(--danger);border-color:#fecdd3;" onclick="return confirm('Batalkan pesanan ini?')">
                            ✕ Batalkan Pesanan
                        </button>
                    </form>
                </div>
            </div>
        `);
    }

    // Auto poll every 10 seconds
    setInterval(pollPendingOrders, 10000);
</script>
@endpush
