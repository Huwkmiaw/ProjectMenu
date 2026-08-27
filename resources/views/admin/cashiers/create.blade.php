@extends('layouts.dashboard')

@section('title', 'Tambah Kasir')
@section('page-title', 'Tambah Akun Kasir')

@section('topbar-actions')
    <a href="{{ route('admin.cashiers.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection

@section('content')
<div style="max-width:520px">
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.cashiers.store') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Nama Lengkap *</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" placeholder="cth: Kasir Tiga" required>
                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Username (untuk Login) *</label>
                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                           value="{{ old('username') }}" placeholder="cth: kasir3" required autocomplete="off">
                    @error('username')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Password *</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                           placeholder="Minimal 6 karakter" required>
                    @error('password')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Konfirmasi Password *</label>
                    <input type="password" name="password_confirmation" class="form-control"
                           placeholder="Ulangi password" required>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                <button type="submit" class="btn btn-primary btn-full">Simpan Akun Kasir</button>
            </form>
        </div>
    </div>
</div>
@endsection
