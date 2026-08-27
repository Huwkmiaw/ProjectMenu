@extends('layouts.customer')

@section('title', 'Tracking Pesanan — ' . $order->order_code)

@push('styles')
<style>
    .tracking-page { padding: 36px 0 80px; }

    .tracking-card { max-width: 560px; margin: 0 auto; }

    /* ── Status Timeline ── */
    .status-timeline {
        padding: 28px 32px;
        position: relative;
    }
    .timeline-item {
        display: flex;
        align-items: flex-start;
        gap: 20px;
        padding-bottom: 28px;
        position: relative;
    }
    .timeline-item:last-child { padding-bottom: 0; }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: 19px; top: 40px;
        width: 2px;
        height: calc(100% - 12px);
        background: var(--color-border);
    }
    .timeline-item:last-child::before { display: none; }
    .timeline-icon {
        width: 40px; height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
        border: 2px solid var(--color-border);
        background: var(--color-bg);
        z-index: 1;
        transition: var(--transition);
    }
    .timeline-icon.done    { background: var(--color-success); border-color: var(--color-success); filter: drop-shadow(0 2px 8px rgba(34,197,94,.3)); }
    .timeline-icon.active  { background: var(--color-primary); border-color: var(--color-primary); filter: drop-shadow(0 2px 8px rgba(249,115,22,.3)); animation: pulse 1.5s ease infinite; }
    .timeline-icon.pending { opacity: .4; }
    @keyframes pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(249,115,22,.4); }
        50%       { box-shadow: 0 0 0 8px rgba(249,115,22,.0); }
    }
    .timeline-content { flex: 1; padding-top: 8px; }
    .timeline-label { font-weight: 700; font-size: 1rem; }
    .timeline-desc  { font-size: .82rem; color: var(--color-text-light); margin-top: 2px; }
    .timeline-time  { font-size: .75rem; color: var(--color-muted); margin-top: 4px; }

    /* ── Current Status Banner ── */
    .status-banner {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px 24px;
        border-bottom: 1px solid var(--color-border);
    }
    .status-banner-icon { font-size: 2.5rem; }
    .status-banner-text h2 { font-size: 1.1rem; font-weight: 800; }
    .status-banner-text p  { font-size: .875rem; color: var(--color-text-light); margin-top: 2px; }

    /* ── Order Code Box ── */
    .order-code-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--color-primary-light);
        border: 1.5px solid #fed7aa;
        border-radius: var(--radius-md);
        padding: 12px 18px;
        margin-bottom: 24px;
    }
    .order-code-box span:first-child { font-size: .82rem; color: var(--color-text-light); }
    .order-code-box strong { font-size: 1.1rem; font-weight: 900; color: var(--color-primary); letter-spacing: .05em; }

    .auto-refresh-indicator {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: .8rem;
        color: var(--color-muted);
        margin-top: 16px;
    }
    .dot-pulse {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: var(--color-success);
        animation: dotPulse 1.5s ease infinite;
    }
    @keyframes dotPulse {
        0%, 100% { opacity: 1; }
        50%       { opacity: .2; }
    }
</style>
@endpush

@section('content')
<div class="tracking-page">
    <div class="container">
        <div class="tracking-card">

            {{-- Order Code --}}
            <div class="order-code-box">
                <span>Kode Pesanan</span>
                <strong>{{ $order->order_code }}</strong>
            </div>

            <div class="card" id="trackingCard">
                {{-- Status Banner --}}
                @php
                    $banners = [
                        'pending'   => ['icon' => '⏳', 'title' => 'Menunggu Konfirmasi', 'desc' => 'Pesanan Anda sedang menunggu kasir mengkonfirmasi.'],
                        'confirmed' => ['icon' => '✅', 'title' => 'Pesanan Dikonfirmasi', 'desc' => 'Pesanan Anda sedang disiapkan oleh dapur.'],
                        'paid'      => ['icon' => '💳', 'title' => 'Sudah Dibayar', 'desc' => 'Pembayaran telah diterima, pesanan segera selesai.'],
                        'completed' => ['icon' => '🎉', 'title' => 'Pesanan Selesai!', 'desc' => 'Silakan ambil pesanan Anda. Selamat makan!'],
                        'cancelled' => ['icon' => '❌', 'title' => 'Pesanan Dibatalkan', 'desc' => 'Maaf, pesanan Anda dibatalkan.'],
                    ];
                    $banner = $banners[$order->status] ?? $banners['pending'];
                @endphp

                <div class="status-banner">
                    <span class="status-banner-icon" id="statusIcon">{{ $banner['icon'] }}</span>
                    <div class="status-banner-text">
                        <h2 id="statusTitle">{{ $banner['title'] }}</h2>
                        <p id="statusDesc">{{ $banner['desc'] }}</p>
                    </div>
                </div>

                {{-- Timeline --}}
                <div class="status-timeline">
                    @php
                        $statuses = [
                            'pending'   => ['icon' => '📋', 'label' => 'Pesanan Masuk', 'desc' => 'Pesanan dikirim ke sistem'],
                            'confirmed' => ['icon' => '✅', 'label' => 'Dikonfirmasi', 'desc' => 'Kasir mengkonfirmasi pesanan'],
                            'paid'      => ['icon' => '💳', 'label' => 'Pembayaran', 'desc' => 'Pembayaran telah diterima'],
                            'completed' => ['icon' => '🎉', 'label' => 'Selesai', 'desc' => 'Pesanan sudah siap'],
                        ];
                        $statusOrder = ['pending', 'confirmed', 'paid', 'completed'];
                        $currentIdx  = array_search($order->status, $statusOrder);
                        if ($currentIdx === false) $currentIdx = 0;
                    @endphp

                    @foreach($statuses as $key => $step)
                        @php
                            $idx = array_search($key, $statusOrder);
                            $state = $idx < $currentIdx ? 'done' : ($idx === $currentIdx ? 'active' : 'pending');
                        @endphp
                        <div class="timeline-item">
                            <div class="timeline-icon {{ $state }}">{{ $step['icon'] }}</div>
                            <div class="timeline-content">
                                <div class="timeline-label" style="{{ $state === 'pending' ? 'color:var(--color-muted)' : '' }}">
                                    {{ $step['label'] }}
                                </div>
                                <div class="timeline-desc">{{ $step['desc'] }}</div>
                                @if($key === 'pending' && $order->created_at)
                                    <div class="timeline-time">{{ $order->created_at->format('H:i') }}</div>
                                @elseif($key === 'confirmed' && $order->confirmed_at)
                                    <div class="timeline-time">{{ $order->confirmed_at->format('H:i') }}</div>
                                @elseif($key === 'paid' && $order->paid_at)
                                    <div class="timeline-time">{{ $order->paid_at->format('H:i') }}</div>
                                @elseif($key === 'completed' && $order->completed_at)
                                    <div class="timeline-time">{{ $order->completed_at->format('H:i') }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Actions --}}
                <div style="padding: 0 32px 24px; display:flex; gap:12px; flex-wrap:wrap;">
                    <a href="{{ route('menu.index') }}" class="btn btn-secondary btn-sm">🍜 Pesan Lagi</a>
                    <a href="{{ route('welcome') }}" class="btn btn-outline-primary btn-sm">🏠 Kembali ke Awal</a>
                </div>

                <div class="auto-refresh-indicator" style="padding: 0 32px 24px;">
                    <div class="dot-pulse"></div>
                    Halaman diperbarui otomatis setiap 10 detik
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const orderCode = '{{ $order->order_code }}';
    const banners = {
        'pending':   { icon: '⏳', title: 'Menunggu Konfirmasi', desc: 'Pesanan Anda sedang menunggu kasir mengkonfirmasi.' },
        'confirmed': { icon: '✅', title: 'Pesanan Dikonfirmasi', desc: 'Pesanan Anda sedang disiapkan oleh dapur.' },
        'paid':      { icon: '💳', title: 'Sudah Dibayar', desc: 'Pembayaran telah diterima, pesanan segera selesai.' },
        'completed': { icon: '🎉', title: 'Pesanan Selesai!', desc: 'Silakan ambil pesanan Anda. Selamat makan!' },
        'cancelled': { icon: '❌', title: 'Pesanan Dibatalkan', desc: 'Maaf, pesanan Anda dibatalkan.' },
    };
    let currentStatus = '{{ $order->status }}';

    function pollStatus() {
        fetch(`/order/${orderCode}/tracking`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.ok ? r.json() : null)
        .then(data => {
            if (data && data.status && data.status !== currentStatus) {
                currentStatus = data.status;
                const b = banners[data.status];
                if (b) {
                    document.getElementById('statusIcon').textContent  = b.icon;
                    document.getElementById('statusTitle').textContent = b.title;
                    document.getElementById('statusDesc').textContent  = b.desc;
                }
                if (data.status === 'completed' || data.status === 'cancelled') {
                    clearInterval(pollTimer);
                }
            }
        })
        .catch(() => {});
    }

    // Start polling
    const pollTimer = setInterval(pollStatus, 10000);

    // Also support JSON response if requested via AJAX
    // (The controller returns view by default, JSON if expectsJson)
</script>
@endpush
