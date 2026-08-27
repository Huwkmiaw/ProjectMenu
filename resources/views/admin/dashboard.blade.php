@extends('layouts.dashboard')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Admin')

@section('topbar-actions')
    <span style="font-size:.875rem; color:var(--text-light)">{{ now()->format('d M Y') }}</span>
@endsection

@push('styles')
<style>
    .chart-bar-wrap { display: flex; align-items: flex-end; gap: 8px; height: 120px; padding: 0 8px; }
    .chart-bar {
        flex: 1;
        background: linear-gradient(180deg, var(--primary), #fb923c);
        border-radius: 4px 4px 0 0;
        min-width: 20px;
        position: relative;
        transition: opacity .2s;
        cursor: pointer;
    }
    .chart-bar:hover { opacity: .8; }
    .chart-bar-label { position: absolute; bottom: -20px; left: 50%; transform: translateX(-50%); font-size: .6rem; color: var(--muted); white-space: nowrap; }
    .chart-labels { display: flex; gap: 8px; padding: 0 8px; margin-top: 24px; }
    .chart-labels span { flex: 1; text-align: center; font-size: .65rem; color: var(--muted); }
</style>
@endpush

@section('content')
{{-- Stats Row --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon orange">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ $stats['orders_today'] }}</div>
            <div class="stat-label">Pesanan Hari Ini</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="stat-value" style="font-size:1.2rem">Rp {{ number_format($stats['revenue_today'], 0, ',', '.') }}</div>
            <div class="stat-label">Omzet Hari Ini</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ $stats['orders_pending'] }}</div>
            <div class="stat-label">Pesanan Pending</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ $stats['total_menu_items'] }}</div>
            <div class="stat-label">Total Menu</div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 360px; gap: 24px; align-items: start;">

    {{-- Revenue Chart (last 7 days) --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Omzet 7 Hari Terakhir</span>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary btn-sm">Laporan Lengkap →</a>
        </div>
        <div class="card-body">
            @php $maxRevenue = $dailyRevenue->max('revenue') ?: 1; @endphp
            <div class="chart-bar-wrap">
                @foreach($dailyRevenue as $day)
                    @php $heightPct = ($day['revenue'] / $maxRevenue) * 100; @endphp
                    <div class="chart-bar" style="height: {{ max($heightPct, 4) }}%"
                         title="{{ $day['date'] }}: Rp {{ number_format($day['revenue'], 0, ',', '.') }}">
                        <span class="chart-bar-label">{{ $day['date'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Top Menu --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Menu Terlaris Bulan Ini</span>
        </div>
        <div style="padding: 16px 0;">
            @forelse($topMenuItems as $i => $item)
                <div style="display:flex; align-items:center; gap:12px; padding: 10px 20px;">
                    <span style="width:24px; height:24px; border-radius:50%; background: {{ $i === 0 ? '#fbbf24' : ($i === 1 ? '#94a3b8' : '#c4975c') }}; display:flex; align-items:center; justify-content:center; font-size:.75rem; font-weight:800; color:#fff; flex-shrink:0">
                        {{ $i + 1 }}
                    </span>
                    <div style="flex:1; min-width:0">
                        <div style="font-weight:600; font-size:.875rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis">{{ $item->name }}</div>
                        <div style="font-size:.75rem; color:var(--muted)">{{ $item->sold_count }} terjual</div>
                    </div>
                    <div style="font-weight:700; font-size:.875rem; color:var(--primary)">{{ $item->formattedPrice }}</div>
                </div>
            @empty
                <div style="text-align:center; padding:24px; color:var(--muted); font-size:.875rem">Belum ada data penjualan</div>
            @endforelse
        </div>
    </div>

</div>

{{-- Quick Links --}}
<div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:16px; margin-top:24px;">
    <a href="{{ route('admin.menus.create') }}" class="card" style="padding:20px; text-align:center; cursor:pointer; transition: var(--transition); text-decoration:none; color:inherit;"
       onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border)'">
        <div style="color:var(--primary); margin-bottom:8px; display:flex; justify-content:center;">
            <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        </div>
        <div style="font-weight:600; font-size:.875rem">Tambah Menu</div>
    </a>
    <a href="{{ route('admin.categories.create') }}" class="card" style="padding:20px; text-align:center; cursor:pointer; transition: var(--transition); text-decoration:none; color:inherit;"
       onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border)'">
        <div style="color:var(--primary); margin-bottom:8px; display:flex; justify-content:center;">
            <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
        </div>
        <div style="font-weight:600; font-size:.875rem">Tambah Kategori</div>
    </a>
    <a href="{{ route('admin.cashiers.create') }}" class="card" style="padding:20px; text-align:center; cursor:pointer; transition: var(--transition); text-decoration:none; color:inherit;"
       onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border)'">
        <div style="color:var(--primary); margin-bottom:8px; display:flex; justify-content:center;">
            <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
        </div>
        <div style="font-weight:600; font-size:.875rem">Tambah Kasir</div>
    </a>
    <a href="{{ route('cashier.dashboard') }}" class="card" style="padding:20px; text-align:center; cursor:pointer; transition: var(--transition); text-decoration:none; color:inherit;"
       onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border)'">
        <div style="color:var(--primary); margin-bottom:8px; display:flex; justify-content:center;">
            <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div style="font-weight:600; font-size:.875rem">Dashboard Kasir</div>
    </a>
</div>
@endsection
