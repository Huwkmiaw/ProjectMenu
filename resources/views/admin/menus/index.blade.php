@extends('layouts.dashboard')
@section('title', 'Manajemen Menu')
@section('page-title', 'Manajemen Menu')
@section('topbar-actions')
    <a href="{{ route('admin.menus.create') }}" class="btn btn-primary btn-sm">+ Tambah Menu</a>
@endsection

@section('content')
{{-- Filter --}}
<div style="display:flex; gap:10px; margin-bottom:20px; flex-wrap:wrap;">
    @foreach($categories as $cat)
        <a href="{{ route('admin.menus.index', ['category' => $cat->id]) }}"
           class="btn btn-sm {{ request('category') == $cat->id ? 'btn-primary' : 'btn-secondary' }}">
            {{ $cat->name }}
        </a>
    @endforeach
    @if(request('category'))
        <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary btn-sm">Reset Filter</a>
    @endif
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Urutan</th>
                    <th>Tersedia</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($menuItems as $item)
                    <tr>
                        <td>
                            @if($item->image)
                                <img src="{{ $item->imageUrl }}" alt="{{ $item->name }}"
                                     style="width:48px; height:48px; object-fit:cover; border-radius:8px; border:1px solid var(--border)">
                            @else
                                <div style="width:48px; height:48px; border-radius:8px; background:#f1f5f9; display:flex; align-items:center; justify-content:center; color:#94a3b8;">
                                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $item->name }}</strong>
                            @if($item->description)
                                <div style="font-size:.75rem; color:var(--muted)">{{ Str::limit($item->description, 50) }}</div>
                            @endif
                        </td>
                        <td>{{ $item->category->name }}</td>
                        <td><strong>{{ $item->formattedPrice }}</strong></td>
                        <td>{{ $item->sort_order }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.menus.toggle', $item) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-xs {{ $item->is_available ? 'btn-success' : 'btn-danger' }}">
                                    {{ $item->is_available ? 'Tersedia' : 'Habis' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <a href="{{ route('admin.menus.edit', $item) }}" class="btn btn-secondary btn-xs">Edit</a>
                                <form method="POST" action="{{ route('admin.menus.destroy', $item) }}"
                                      onsubmit="return confirm('Hapus menu {{ $item->name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-xs" type="submit">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center; padding:32px; color:var(--muted)">Belum ada menu</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
