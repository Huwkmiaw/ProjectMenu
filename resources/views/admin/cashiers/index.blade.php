@extends('layouts.dashboard')

@section('title', 'Manajemen Kasir')
@section('page-title', 'Manajemen Akun Kasir')

@section('topbar-actions')
    <a href="{{ route('admin.cashiers.create') }}" class="btn btn-primary btn-sm">+ Tambah Kasir</a>
@endsection

@section('content')
<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Status</th>
                    <th>Dibuat Pada</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cashiers as $cashier)
                    <tr>
                        <td>
                            <div class="flex items-center gap-1">
                                <div class="user-avatar" style="width:30px; height:30px; font-size:.8rem;">
                                    {{ strtoupper(substr($cashier->name, 0, 1)) }}
                                </div>
                                <strong>{{ $cashier->name }}</strong>
                            </div>
                        </td>
                        <td><code>{{ $cashier->username }}</code></td>
                        <td>
                            <span class="badge {{ $cashier->is_active ? 'badge-paid' : 'badge-cancelled' }}">
                                {{ $cashier->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td style="color:var(--muted); font-size:.82rem;">{{ $cashier->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <a href="{{ route('admin.cashiers.edit', $cashier) }}" class="btn btn-secondary btn-xs">Edit</a>
                                <form method="POST" action="{{ route('admin.cashiers.toggle', $cashier) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-warning btn-xs" type="submit">
                                        {{ $cashier->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.cashiers.destroy', $cashier) }}"
                                      onsubmit="return confirm('Hapus akun kasir {{ $cashier->name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-xs" type="submit">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding:32px; color:var(--muted)">Belum ada akun kasir terdaftar</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
