@extends('layouts.dashboard')

@section('title', 'Semua Pesanan Aktif')
@section('page-title', 'Semua Pesanan Hari Ini')

@section('topbar-actions')
    <a href="{{ route('cashier.dashboard') }}" class="btn btn-primary btn-sm">Dashboard Live</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <span class="card-title">Daftar Pesanan yang Sedang Diproses</span>
        <span class="badge badge-primary">{{ $orders->count() }} pesanan</span>
    </div>

    <div style="padding: 24px;">
        @if($orders->isEmpty())
            <div style="text-align:center; padding:48px; color:var(--muted);">
                <div style="width:56px; height:56px; margin:0 auto 12px; color:var(--success)">
                    <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div style="font-weight:700; font-size:1.1rem">Tidak ada pesanan aktif saat ini</div>
                <div style="font-size:.85rem; margin-top:4px">Semua pesanan hari ini telah diselesaikan atau belum ada pesanan baru.</div>
            </div>
        @else
            <div class="orders-grid">
                @foreach($orders as $order)
                    @include('cashier.orders._card', ['order' => $order])
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
