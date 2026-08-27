@extends('layouts.dashboard')
@section('title', 'Edit Kategori')
@section('page-title', 'Edit Kategori')
@section('topbar-actions')
    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection

@section('content')
<div style="max-width:560px">
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.categories.update', $category) }}">
                @csrf @method('PUT')
                <div class="form-group">
                    <label class="form-label">Nama Kategori *</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $category->name) }}" required>
                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Urutan Tampil</label>
                    <input type="number" name="sort_order" class="form-control"
                        value="{{ old('sort_order', $category->sort_order) }}" min="0">
                </div>
                @if($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif
                <button type="submit" class="btn btn-primary">Perbarui Kategori</button>
            </form>
        </div>
    </div>
</div>
@endsection
