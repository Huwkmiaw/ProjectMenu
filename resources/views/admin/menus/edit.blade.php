@extends('layouts.dashboard')
@section('title', 'Edit Menu')
@section('page-title', 'Edit Menu')
@section('topbar-actions')
    <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection

@section('content')
<div style="max-width:680px">
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.menus.update', $menu) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px">
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label">Nama Menu *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $menu->name) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kategori *</label>
                        <select name="category_id" class="form-control" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ (old('category_id', $menu->category_id)) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Harga (Rp) *</label>
                        <input type="number" name="price" class="form-control" value="{{ old('price', $menu->price) }}" min="0" required>
                    </div>
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $menu->description) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Urutan Tampil</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $menu->sort_order) }}" min="0">
                    </div>
                    <div class="form-group" style="display:flex; align-items:center; gap:10px; padding-top:24px">
                        <input type="checkbox" name="is_available" id="is_available" value="1"
                            {{ old('is_available', $menu->is_available) ? 'checked' : '' }}
                            style="width:18px; height:18px; accent-color:var(--primary)">
                        <label for="is_available" class="form-label" style="margin-bottom:0; cursor:pointer">Tersedia untuk dipesan</label>
                    </div>
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label">Foto Menu</label>
                        @if($menu->image)
                            <div style="margin-bottom:10px">
                                <img src="{{ $menu->imageUrl }}" alt="{{ $menu->name }}"
                                     style="max-width:160px; border-radius:10px; border:1px solid var(--border)">
                                <div style="font-size:.75rem; color:var(--muted); margin-top:4px">Foto saat ini</div>
                            </div>
                        @endif
                        <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(this)">
                        <div id="imagePreview" style="margin-top:12px; display:none">
                            <img id="previewImg" src="" alt="Preview" style="max-width:160px; border-radius:10px; border:1px solid var(--border)">
                        </div>
                    </div>
                </div>
                @if($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif
                <button type="submit" class="btn btn-primary">Perbarui Menu</button>
            </form>
        </div>
    </div>
</div>
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
