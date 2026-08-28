@extends('layouts.dashboard')
@section('title', 'Tambah Menu')
@section('page-title', 'Tambah Menu')
@section('topbar-actions')
    <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection

@section('content')
<div style="max-width:680px">
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.menus.store') }}" enctype="multipart/form-data">
                @csrf
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px">
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label">Nama Menu *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" placeholder="cth: Nasi Goreng Spesial" required>
                        @error('name')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kategori *</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">Pilih kategori...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Harga (Rp) *</label>
                        <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                            value="{{ old('price') }}" placeholder="15000" min="0" required>
                        @error('price')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3"
                            placeholder="Deskripsi singkat menu...">{{ old('description') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Urutan Tampil</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" min="0">
                    </div>
                    <div class="form-group" style="display:flex; align-items:center; gap:10px; padding-top:24px">
                        <input type="checkbox" name="is_available" id="is_available" value="1"
                            {{ old('is_available', '1') ? 'checked' : '' }}
                            style="width:18px; height:18px; accent-color:var(--primary)">
                        <label for="is_available" class="form-label" style="margin-bottom:0; cursor:pointer">Tersedia untuk dipesan</label>
                    </div>
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label">Link / URL Foto Menu <span style="color:var(--primary); font-size:.78rem; font-weight:600">(Rekomendasi Hosting Cloud)</span></label>
                        <input type="url" name="image_url" id="imageUrlInput" class="form-control @error('image_url') is-invalid @enderror"
                               value="{{ old('image_url') }}" placeholder="cth: https://images.unsplash.com/... atau https://i.postimg.cc/..."
                               oninput="previewUrlImage(this.value)">
                        <div style="font-size:.75rem; color:var(--muted); margin-top:4px">
                            Tips: Anda bisa upload foto ke <a href="https://postimages.org" target="_blank" style="color:var(--primary)">Postimages.org</a> atau <a href="https://imgbb.com" target="_blank" style="color:var(--primary)">ImgBB</a> lalu paste <strong>Direct Link URL</strong> di sini.
                        </div>
                        @error('image_url')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label">Atau Upload File Gambar</label>
                        <input type="file" name="image" id="imageInput" class="form-control @error('image') is-invalid @enderror" accept="image/*"
                               onchange="previewImage(this)">
                        <div id="imagePreview" style="margin-top:12px; display:none">
                            <div style="font-size:.75rem; color:var(--muted); margin-bottom:4px">Preview Foto:</div>
                            <img id="previewImg" src="" alt="Preview" style="max-width:180px; max-height:140px; object-fit:cover; border-radius:12px; border:1px solid var(--border)">
                        </div>
                        @error('image')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                </div>
                @if($errors->any())
                    <div class="alert alert-danger" style="margin-top:16px">{{ $errors->first() }}</div>
                @endif
                <button type="submit" class="btn btn-primary" style="margin-top:16px">Simpan Menu</button>
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

function previewUrlImage(url) {
    if (url && (url.startsWith('http://') || url.startsWith('https://') || url.startsWith('data:'))) {
        document.getElementById('previewImg').src = url;
        document.getElementById('imagePreview').style.display = 'block';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const val = document.getElementById('imageUrlInput')?.value;
    if (val) previewUrlImage(val);
});
</script>
@endsection
