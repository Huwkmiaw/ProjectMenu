@extends('layouts.dashboard')

@section('title', 'Riwayat Transaksi')
@section('page-title', 'Riwayat Transaksi')

@section('topbar-actions')
    <a href="{{ route('cashier.dashboard') }}" class="btn btn-primary btn-sm" style="min-height:38px; padding:8px 16px;">
        Dashboard Live
    </a>
@endsection

@push('styles')
<style>
    .history-filter-card {
        background: var(--surface);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        padding: 16px 20px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }
    .date-form-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .date-input-touch {
        padding: 10px 16px;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-md);
        font-size: .95rem;
        font-weight: 600;
        background: #fff;
        min-height: 44px;
        cursor: pointer;
    }
    .btn-touch {
        min-height: 44px;
        padding: 10px 18px;
        font-size: .9rem;
        font-weight: 700;
        border-radius: var(--radius-md);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
</style>
@endpush

@section('content')
{{-- Date Filter Bar --}}
<div class="history-filter-card">
    <form method="GET" action="{{ route('cashier.orders.history') }}" class="date-form-wrap">
        <label for="historyDate" style="font-weight:700; font-size:.9rem; color:var(--text);">Pilih Tanggal:</label>
        <input
            type="date"
            id="historyDate"
            name="date"
            class="date-input-touch"
            value="{{ $selectedDate }}"
            onchange="this.form.submit()"
        >
        <button type="submit" class="btn btn-primary btn-touch">Tampilkan</button>

        <a href="{{ route('cashier.orders.history', ['date' => now()->format('Y-m-d')]) }}"
           class="btn {{ $selectedDate === now()->format('Y-m-d') ? 'btn-success' : 'btn-secondary' }} btn-touch">
            Hari Ini
        </a>
        <a href="{{ route('cashier.orders.history', ['date' => now()->subDay()->format('Y-m-d')]) }}"
           class="btn {{ $selectedDate === now()->subDay()->format('Y-m-d') ? 'btn-success' : 'btn-secondary' }} btn-touch">
            Kemarin
        </a>
    </form>

    <div style="font-size:.9rem; color:var(--text-light); font-weight:600;">
        Menampilkan transaksi: <strong>{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}</strong>
    </div>
</div>

{{-- Summary Stats --}}
<div class="stats-grid" style="margin-bottom:24px">
    <div class="stat-card">
        <div class="stat-icon green">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="stat-value" style="font-size:1.25rem">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <div class="stat-label">Total Omzet ({{ \Carbon\Carbon::parse($selectedDate)->format('d M') }})</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <div>
            <div class="stat-value" style="font-size:1.15rem">Rp {{ number_format($cashTotal, 0, ',', '.') }}</div>
            <div class="stat-label">Pembayaran Tunai</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
        </div>
        <div>
            <div class="stat-value" style="font-size:1.15rem">Rp {{ number_format($cashlessTotal, 0, ',', '.') }}</div>
            <div class="stat-label">Non-Tunai (QRIS/Debit)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ $orders->whereIn('status', ['paid', 'completed'])->count() }}</div>
            <div class="stat-label">Transaksi Berhasil</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Daftar Riwayat Transaksi ({{ $orders->count() }})</span>
    </div>

    @if($orders->isEmpty())
        <div style="text-align:center; padding:48px 20px; color:var(--muted);">
            <div style="width:56px; height:56px; margin:0 auto 12px; color:var(--muted)">
                <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            </div>
            <div style="font-weight:700; font-size:1.05rem; color:var(--text)">Tidak ada transaksi pada tanggal ini</div>
            <div style="font-size:.85rem; margin-top:4px">Gunakan filter tanggal di atas untuk melihat tanggal lainnya.</div>
        </div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Waktu</th>
                        <th>Customer</th>
                        <th>Layanan</th>
                        <th>Rincian Menu</th>
                        <th>Total Tagihan</th>
                        <th>Metode Bayar</th>
                        <th>Kasir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td><strong><code>{{ $order->order_code }}</code></strong></td>
                            <td style="color:var(--muted); font-size:.85rem; white-space:nowrap;">{{ $order->created_at->format('H:i') }}</td>
                            <td><strong style="font-size:.95rem;">{{ $order->customer_name }}</strong></td>
                            <td>
                                @if($order->isDineIn())
                                    <span class="badge badge-dine-in">Dine In</span>
                                @else
                                    <span class="badge badge-take-away">Take Away</span>
                                @endif
                            </td>
                            <td style="font-size:.85rem; color:var(--text-light); max-width:280px;">
                                {{ $order->items->map(fn($i) => $i->menu_item_name . ' ×' . $i->quantity)->join(', ') }}
                            </td>
                            <td><strong style="font-size:.95rem; color:var(--primary);">{{ $order->formattedTotal }}</strong></td>
                            <td>
                                @if($order->status === 'cancelled')
                                    <span class="badge badge-cancelled">Dibatalkan</span>
                                @elseif($order->payment_method === 'cash')
                                    <span class="badge badge-paid">Tunai</span>
                                    @if($order->change_amount > 0)
                                        <div style="font-size:.72rem; color:var(--muted); margin-top:2px;">
                                            Kembalian: Rp {{ number_format($order->change_amount, 0, ',', '.') }}
                                        </div>
                                    @endif
                                @elseif($order->payment_method === 'cashless')
                                    <span class="badge badge-confirmed">Non-Tunai</span>
                                @else
                                    <span class="badge badge-completed">Selesai</span>
                                @endif
                            </td>
                            <td style="font-size:.85rem;">{{ $order->cashier?->name ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
