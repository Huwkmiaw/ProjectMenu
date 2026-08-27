@extends('layouts.dashboard')
@section('title', 'Kategori')
@section('page-title', 'Manajemen Kategori')
@section('topbar-actions')
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">+ Tambah Kategori</a>
@endsection

@section('content')
<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama Kategori</th>
                    <th>Slug</th>
                    <th>Urutan</th>
                    <th>Jumlah Menu</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                    <tr>
                        <td><strong>{{ $cat->name }}</strong></td>
                        <td style="color:var(--muted); font-size:.82rem">{{ $cat->slug }}</td>
                        <td>{{ $cat->sort_order }}</td>
                        <td>{{ $cat->menu_items_count }} menu</td>
                        <td>
                            <span class="badge {{ $cat->is_active ? 'badge-paid' : 'badge-cancelled' }}">
                                {{ $cat->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <a href="{{ route('admin.categories.edit', $cat) }}" class="btn btn-secondary btn-xs">Edit</a>
                                <form method="POST" action="{{ route('admin.categories.toggle', $cat) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-warning btn-xs" type="submit">
                                        {{ $cat->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                                @if($cat->menu_items_count === 0)
                                    <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}"
                                          onsubmit="return confirm('Hapus kategori {{ $cat->name }}?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-xs" type="submit">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center; padding:32px; color:var(--muted)">Belum ada kategori</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
