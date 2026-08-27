@extends('layouts.customer')

@section('title', 'Selamat Datang')

@push('styles')
<style>
    body { background: linear-gradient(135deg, #fff7ed 0%, #f8fafc 50%, #eff6ff 100%); }

    .welcome-wrapper {
        min-height: calc(100vh - 64px);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px 24px;
        text-align: center;
    }

    .welcome-hero {
        margin-bottom: 40px;
        animation: fadeUp .6s ease both;
    }
    .welcome-hero-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        background: linear-gradient(135deg, var(--color-primary), #fb923c);
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 30px rgba(249,115,22,.3);
        animation: float 3s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50%       { transform: translateY(-8px); }
    }
    .welcome-hero h1 {
        font-size: clamp(2rem, 5vw, 3rem);
        font-weight: 900;
        color: var(--color-secondary);
        line-height: 1.1;
        margin-bottom: 12px;
    }
    .welcome-hero h1 span { color: var(--color-primary); }
    .welcome-hero p {
        font-size: 1.1rem;
        color: var(--color-text-light);
        max-width: 440px;
        margin: 0 auto;
    }

    .order-type-section {
        width: 100%;
        max-width: 560px;
        animation: fadeUp .6s .15s ease both;
    }
    .order-type-section h2 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--color-text-light);
        text-transform: uppercase;
        letter-spacing: .08em;
        margin-bottom: 24px;
    }

    .order-type-cards {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 28px;
    }

    .order-type-card {
        background: var(--color-surface);
        border: 2.5px solid var(--color-border);
        border-radius: var(--radius-xl);
        padding: 36px 24px;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 14px;
        box-shadow: var(--shadow-sm);
        position: relative;
        overflow: hidden;
    }
    .order-type-card::before {
        content: '';
        position: absolute;
        inset: 0;
        opacity: 0;
        transition: var(--transition);
    }
    .order-type-card.dine-in::before   { background: radial-gradient(circle at top, #fff7ed, transparent 70%); }
    .order-type-card.take-away::before { background: radial-gradient(circle at top, #f0fdf4, transparent 70%); }
    .order-type-card:hover {
        border-color: var(--color-primary);
        box-shadow: var(--shadow-lg);
        transform: translateY(-6px);
    }
    .order-type-card:hover::before { opacity: 1; }

    .order-type-card .ot-vector {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 1;
        transition: transform .2s;
    }
    .order-type-card.dine-in .ot-vector   { background: #fff7ed; color: var(--color-primary); }
    .order-type-card.take-away .ot-vector { background: #f0fdf4; color: #16a34a; }
    .order-type-card:hover .ot-vector { transform: scale(1.1); }

    .order-type-card .ot-label {
        font-size: 1.25rem;
        font-weight: 900;
        color: var(--color-secondary);
        position: relative;
        z-index: 1;
    }
    .order-type-card .ot-desc {
        font-size: .85rem;
        color: var(--color-text-light);
        position: relative;
        z-index: 1;
        line-height: 1.4;
    }

    /* Success Modal Popup with Circular Countdown */
    .order-success-modal {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.75);
        backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 20px;
        animation: fadeIn .3s ease;
    }
    .order-success-card {
        background: #fff;
        border-radius: var(--radius-xl);
        max-width: 480px;
        width: 100%;
        padding: 36px 32px;
        text-align: center;
        box-shadow: var(--shadow-lg);
        animation: scaleUp .4s cubic-bezier(0.34, 1.56, 0.64, 1);
        position: relative;
    }
    .success-icon-badge {
        width: 72px; height: 72px;
        background: #f0fdf4;
        color: var(--color-success);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        border: 2px solid #bbf7d0;
    }
    .success-code-box {
        background: #fff7ed;
        border: 2px dashed #fed7aa;
        border-radius: var(--radius-md);
        padding: 14px;
        margin: 18px 0;
    }
    .success-code-box .code-val {
        font-size: 1.6rem;
        font-weight: 900;
        color: var(--color-primary);
        letter-spacing: .08em;
    }

    /* Circular Countdown Ring */
    .countdown-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin: 20px 0 16px;
    }
    .circular-timer {
        position: relative;
        width: 76px;
        height: 76px;
    }
    .circular-timer svg {
        transform: rotate(-90deg);
        width: 76px;
        height: 76px;
    }
    .circular-timer circle {
        fill: none;
        stroke-width: 6;
    }
    .timer-bg {
        stroke: #e2e8f0;
    }
    .timer-progress {
        stroke: var(--color-primary);
        stroke-linecap: round;
        stroke-dasharray: 200;
        stroke-dashoffset: 0;
        transition: stroke-dashoffset 1s linear;
    }
    .timer-number {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        font-weight: 900;
        color: var(--color-secondary);
    }

    @keyframes scaleUp {
        from { transform: scale(0.85); opacity: 0; }
        to   { transform: scale(1); opacity: 1; }
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to   { opacity: 1; }
    }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(24px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 480px) {
        .order-type-cards { grid-template-columns: 1fr; }
        .welcome-hero h1 { font-size: 1.8rem; }
    }
</style>
@endpush

@section('content')
<div class="welcome-wrapper">

    {{-- SUCCESS POPUP MODAL WITH CIRCULAR COUNTDOWN --}}
    @if(session('order_success'))
        @php $successData = session('order_success'); @endphp
        <div class="order-success-modal" id="successModal">
            <div class="order-success-card">
                <div class="success-icon-badge">
                    <svg width="38" height="38" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h2 style="font-size:1.4rem; font-weight:800; color:var(--color-secondary); margin-bottom:6px;">
                    Pesanan Berhasil Terkirim!
                </h2>
                <p style="color:var(--color-text-light); font-size:.92rem;">
                    Terima kasih <strong>{{ $successData['customer_name'] ?? 'Pelanggan' }}</strong> ({{ $successData['type'] ?? 'Pesanan' }}).
                </p>

                <div class="success-code-box">
                    <div style="font-size:.78rem; color:var(--color-muted); font-weight:700; text-transform:uppercase;">Nomor Pesanan Anda</div>
                    <div class="code-val">{{ $successData['code'] ?? 'ORD' }}</div>
                </div>

                <div style="background:#f8fafc; border-radius:var(--radius-md); padding:12px 16px; margin-bottom:12px; font-size:.875rem; color:#475569;">
                    Silakan langsung menuju ke meja kasir untuk menyelesaikan pembayaran pesanan Anda.
                </div>

                {{-- Circular Progress Timer --}}
                <div class="countdown-wrap">
                    <div class="circular-timer">
                        <svg viewBox="0 0 76 76">
                            <circle class="timer-bg" cx="38" cy="38" r="32"></circle>
                            <circle class="timer-progress" id="timerProgressCircle" cx="38" cy="38" r="32"></circle>
                        </svg>
                        <div class="timer-number" id="countdownNum">5s</div>
                    </div>
                    <div style="font-size:.78rem; color:var(--color-muted); margin-top:8px;">
                        Layar akan otomatis kembali ke menu awal...
                    </div>
                </div>

                <button type="button" class="btn btn-primary btn-full btn-lg" onclick="closeSuccessModal()">
                    Pesan Menu Lain Sekarang
                </button>
            </div>
        </div>
    @endif

    {{-- HERO --}}
    <div class="welcome-hero">
        <div class="welcome-hero-icon">
            <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <h1>Selamat Datang di<br><span>MenuKasir</span></h1>
        <p>Pilih tipe layanan untuk langsung mulai memesan makanan & minuman favoritmu.</p>
    </div>

    {{-- ORDER TYPE SELECTION (Direct Single-Click) --}}
    <div class="order-type-section">
        <h2>Pilih Layanan:</h2>

        <form action="{{ route('order-type.set') }}" method="POST" id="orderTypeForm">
            @csrf
            <input type="hidden" name="order_type" id="orderTypeInput">

            <div class="order-type-cards">
                {{-- DINE IN --}}
                <div class="order-type-card dine-in" onclick="selectAndSubmit('dine_in')">
                    <div class="ot-vector">
                        <svg fill="currentColor" viewBox="0 0 73.602 46.542" style="width:40px; height:26px;">
                            <g>
                                <path d="M10.429,4.148c-0.743,0-1.344,0.598-1.344,1.338v8.018c0,1.084-0.807,1.988-1.854,2.14V5.937 c0-0.735-0.602-1.338-1.345-1.338c-0.744,0-1.345,0.603-1.345,1.338v9.707c-1.043-0.152-1.854-1.056-1.854-2.14V5.486 c0-0.74-0.601-1.338-1.343-1.338C0.604,4.148,0,4.746,0,5.486v8.018c0,2.563,2.012,4.669,4.543,4.835v21.749 c0,0.744,0.601,1.342,1.345,1.342c0.743,0,1.345-0.598,1.345-1.342V18.339c2.532-0.166,4.543-2.273,4.543-4.835V5.486 C11.775,4.746,11.175,4.148,10.429,4.148z"></path>
                                <path d="M67.94,4.113c-2.329,0-2.154,4.669-2.154,10.426c0,3.801-0.076,7.126,0.542,8.948v16.601c0,0.744,0.601,1.342,1.344,1.342 c0.745,0,1.346-0.598,1.346-1.342V24.675c2.158-1.1,4.584-5.219,4.584-10.136C73.601,8.782,70.272,4.113,67.94,4.113z"></path>
                                <path d="M38.17,0C25.276,0,14.817,10.419,14.817,23.272c0,12.853,10.459,23.271,23.353,23.271 c12.901,0,23.359-10.418,23.359-23.271C61.529,10.419,51.071,0,38.17,0z"></path>
                            </g>
                        </svg>
                    </div>
                    <span class="ot-label">Dine In</span>
                    <span class="ot-desc">Makan di tempat</span>
                </div>

                {{-- TAKE AWAY --}}
                <div class="order-type-card take-away" onclick="selectAndSubmit('take_away')">
                    <div class="ot-vector">
                        <svg fill="currentColor" viewBox="0 0 463 463" style="width:36px; height:36px;">
                            <g>
                                <path d="M367.372,142.726c-0.413-8.257-7.213-14.726-15.481-14.726H298.12l-12.974-82.169C280.953,19.274,258.396,0,231.49,0 c-26.885,0-49.442,19.274-53.635,45.831L164.881,128H111.11c-8.268,0-15.068,6.469-15.481,14.726l-15.2,304 c-0.211,4.22,1.338,8.396,4.25,11.457S91.685,463,95.911,463h271.18c4.225,0,8.318-1.756,11.23-4.817s4.461-7.236,4.25-11.457 L367.372,142.726z M192.672,48.171C195.708,28.95,212.032,15,231.511,15c19.458,0,35.784,13.95,38.819,33.171L282.935,128 H180.068L192.672,48.171z M367.453,447.845C367.305,448,367.149,448,367.091,448H95.911c-0.059,0-0.214,0-0.362-0.155 c-0.148-0.155-0.14-0.311-0.137-0.369l15.2-304c0.013-0.267,0.233-0.476,0.5-0.476h51.402l-2.421,15.33 c-0.646,4.092,2.147,7.932,6.238,8.578c0.396,0.063,0.79,0.093,1.179,0.093c3.626,0,6.815-2.636,7.399-6.331l2.79-17.67h107.604 l2.79,17.67c0.646,4.091,4.483,6.88,8.578,6.238c4.091-0.646,6.884-4.486,6.238-8.578l-2.42-15.33h51.402 c0.267,0,0.486,0.209,0.5,0.476l15.2,304C367.593,447.534,367.601,447.689,367.453,447.845z"></path>
                                <path d="M231.501,192c-4.142,0-7.5,3.357-7.5,7.5V240h-9v-40.5c0-4.143-3.358-7.5-7.5-7.5c-4.142,0-7.5,3.357-7.5,7.5V240h-9 v-40.5c0-4.143-3.358-7.5-7.5-7.5c-4.142,0-7.5,3.357-7.5,7.5v56c0,12.958,10.542,23.5,23.5,23.5h0.5v128.5 c0,4.143,3.358,7.5,7.5,7.5c4.142,0,7.5-3.357,7.5-7.5V279h0.5c12.958,0,23.5-10.542,23.5-23.5v-56 C239.001,195.357,235.643,192,231.501,192z M224.001,255.5c0,4.687-3.813,8.5-8.5,8.5h-16c-4.687,0-8.5-3.813-8.5-8.5V255h33 V255.5z"></path>
                                <path d="M287.719,206.754c-0.816-8.162-7.617-14.317-15.82-14.317c-8.767,0-15.899,7.133-15.899,15.899V407.5 c0,4.143,3.358,7.5,7.5,7.5s7.5-3.357,7.5-7.5V359h6.717c6.653,0,13.02-2.837,17.47-7.782s6.6-11.576,5.9-18.191L287.719,206.754 z M284.036,341.186C282.403,343,280.159,344,277.718,344H271V208.336c0-0.496,0.403-0.899,0.899-0.899 c0.464,0,0.849,0.348,0.899,0.853l13.372,126.316C286.427,337.033,285.669,339.37,284.036,341.186z"></path>
                            </g>
                        </svg>
                    </div>
                    <span class="ot-label">Take Away</span>
                    <span class="ot-desc">Bawa pulang</span>
                </div>
            </div>
        </form>

        @if (session('error'))
            <div class="alert alert-danger mt-2">{{ session('error') }}</div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
    function selectAndSubmit(type) {
        document.getElementById('orderTypeInput').value = type;
        document.getElementById('orderTypeForm').submit();
    }

    // Circular countdown timer logic
    const modal = document.getElementById('successModal');
    if (modal) {
        let totalSeconds = 5;
        let remainingSeconds = 5;
        const circle = document.getElementById('timerProgressCircle');
        const numDisplay = document.getElementById('countdownNum');
        const circumference = 2 * Math.PI * 32;

        if (circle) {
            circle.style.strokeDasharray = `${circumference} ${circumference}`;
            circle.style.strokeDashoffset = '0';
        }

        const timerInterval = setInterval(() => {
            remainingSeconds--;
            if (numDisplay) numDisplay.textContent = remainingSeconds + 's';

            if (circle) {
                const offset = circumference - (remainingSeconds / totalSeconds) * circumference;
                circle.style.strokeDashoffset = offset;
            }

            if (remainingSeconds <= 0) {
                clearInterval(timerInterval);
                closeSuccessModal();
            }
        }, 1000);
    }

    function closeSuccessModal() {
        const m = document.getElementById('successModal');
        if (m) {
            m.style.transition = 'opacity .3s, transform .3s';
            m.style.opacity = '0';
            setTimeout(() => m.remove(), 300);
        }
    }
</script>
@endpush
