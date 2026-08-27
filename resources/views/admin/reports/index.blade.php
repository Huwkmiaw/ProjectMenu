@extends('layouts.dashboard')

@section('title', 'Laporan Penjualan')
@section('page-title', 'Laporan Penjualan')

@section('topbar-actions')
    <a href="{{ route('admin.reports.export-csv', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success btn-sm">
        Unduh CSV
    </a>
@endsection

@push('styles')
<style>
    .filter-card {
        background: var(--surface);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        padding: 20px 24px;
        margin-bottom: 24px;
    }
    .filter-form {
        display: flex;
        align-items: flex-end;
        gap: 16px;
        flex-wrap: wrap;
    }
    .filter-group {
        flex: 1;
        min-width: 180px;
    }
    .chart-bar-wrap {
        display: flex;
        align-items: flex-end;
        gap: 8px;
        height: 140px;
        padding: 0 8px;
    }
    .chart-bar {
        flex: 1;
        background: linear-gradient(180deg, var(--primary), #fb923c);
        border-radius: 4px 4px 0 0;
        min-width: 16px;
        position: relative;
        transition: opacity .2s;
        cursor: pointer;
    }
    .chart-bar:hover { opacity: .8; }
    .chart-bar-label {
        position: absolute;
        bottom: -22px;
        left: 50%;
        transform: translateX(-50%);
        font-size: .65rem;
        color: var(--muted);
        white-space: nowrap;
    }
</style>
@endpush

@section('content')
{{-- Filter Periode --}}
<div class="filter-card">
    <form method="GET" action="{{ route('admin.reports.index') }}" class="filter-form">
        <div class="filter-group">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" required>
        </div>
        <div class="filter-group">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" required>
        </div>
        <div>
            <button type="submit" class="btn btn-primary">Filter Laporan</button>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>
</div>

{{-- Summary Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon green">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="stat-value" style="font-size:1.25rem">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</div>
            <div class="stat-label">Total Omzet</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <div>
            <div class="stat-value" style="font-size:1.15rem">Rp {{ number_format($summary['cash_revenue'], 0, ',', '.') }}</div>
            <div class="stat-label">Omzet Tunai</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
        </div>
        <div>
            <div class="stat-value" style="font-size:1.15rem">Rp {{ number_format($summary['cashless_revenue'], 0, ',', '.') }}</div>
            <div class="stat-label">Omzet Non-Tunai</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ $summary['total_transactions'] }}</div>
            <div class="stat-label">
                {{ $summary['dine_in_count'] }} Dine In / {{ $summary['take_away_count'] }} Take Away
            </div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 380px; gap: 24px; align-items: start; margin-bottom: 24px;">

    {{-- Daily Revenue Chart --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Tren Pendapatan Harian</span>
        </div>
        <div class="card-body">
            @if($dailyData->isEmpty())
                <div style="text-align:center; padding:32px; color:var(--muted);">Tidak ada transaksi pada periode ini</div>
            @else
                @php $maxDaily = $dailyData->max('revenue') ?: 1; @endphp
                <div class="chart-bar-wrap">
                    @foreach($dailyData as $day)
                        @php $heightPct = ($day['revenue'] / $maxDaily) * 100; @endphp
                        <div class="chart-bar" style="height: {{ max($heightPct, 6) }}%"
                             title="{{ $day['date'] }}: Rp {{ number_format($day['revenue'], 0, ',', '.') }} ({{ $day['count'] }} order)">
                            <span class="chart-bar-label">{{ $day['date'] }}</span>
                        </div>
                    @endforeach
                </div>
                <div style="margin-top: 32px; text-align: right; font-size: .8rem; color: var(--muted);">
                    Arahkan kursor ke batang grafik untuk melihat rincian omzet harian
                </div>
            @endif
        </div>
    </div>

    {{-- Top 10 Best Sellers --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Menu Terlaris di Periode Ini</span>
        </div>
        <div style="padding: 12px 0;">
            @forelse($topItems as $i => $item)
                <div style="display:flex; align-items:center; gap:12px; padding: 10px 20px; border-bottom: 1px solid var(--border);">
                    <span style="width:24px; height:24px; border-radius:50%; background: {{ $i === 0 ? '#fbbf24' : ($i === 1 ? '#94a3b8' : ($i === 2 ? '#c4975c' : '#f1f5f9')) }}; color: {{ $i < 3 ? '#fff' : 'var(--text)' }}; display:flex; align-items:center; justify-content:center; font-size:.75rem; font-weight:800; flex-shrink:0">
                        {{ $i + 1 }}
                    </span>
                    <div style="flex:1; min-width:0">
                        <div style="font-weight:600; font-size:.875rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis">{{ $item->name }}</div>
                        <div style="font-size:.75rem; color:var(--muted)">{{ $item->category->name ?? '' }}</div>
                    </div>
                    <div style="font-weight:800; font-size:.875rem; color:var(--primary); text-align:right;">
                        {{ $item->sold_count }} <span style="font-size:.7rem; font-weight:500; color:var(--muted);">terjual</span>
                    </div>
                </div>
            @empty
                <div style="text-align:center; padding:32px; color:var(--muted); font-size:.875rem">Belum ada data menu terjual</div>
            @endforelse
        </div>
    </div>

</div>

{{-- Detail Transaksi Table --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">Rincian Transaksi Selesai ({{ $orders->count() }})</span>
        <a href="{{ route('admin.reports.export-csv', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success btn-sm">
            Ekspor CSV
        </a>
    </div>
    @if($orders->isEmpty())
        <div style="text-align:center; padding:48px; color:var(--muted);">
            <div style="width:56px; height:56px; margin:0 auto 12px; color:var(--muted)">
                <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            </div>
            <div style="font-weight:600">Tidak ada riwayat transaksi pada rentang tanggal yang dipilih</div>
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
                        <th>Total</th>
                        <th>Metode Bayar</th>
                        <th>Kasir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td><code>{{ $order->order_code }}</code></td>
                            <td style="font-size:.82rem; color:var(--muted); white-space:nowrap;">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td><strong>{{ $order->customer_name }}</strong></td>
                            <td>
                                @if($order->isDineIn())
                                    <span class="badge badge-dine-in">Dine In</span>
                                @else
                                    <span class="badge badge-take-away">Take Away</span>
                                @endif
                            </td>
                            <td style="font-size:.85rem; color:var(--text-light); max-width:260px;">
                                {{ $order->items->map(fn($i) => $i->menu_item_name . ' (' . $i->quantity . ')')->join(', ') }}
                            </td>
                            <td><strong style="color:var(--primary)">{{ $order->formattedTotal }}</strong></td>
                            <td>
                                @if($order->payment_method === 'cash')
                                    <span class="badge badge-paid">Tunai</span>
                                @elseif($order->payment_method === 'cashless')
                                    <span class="badge badge-confirmed">Non-Tunai</span>
                                @else
                                    <span class="badge badge-completed">{{ $order->statusLabel }}</span>
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
