@extends('layouts.dashboard')

@section('title', 'Edit Kasir')
@section('page-title', 'Edit Akun Kasir')

@section('topbar-actions')
    <a href="{{ route('admin.cashiers.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection

@section('content')
<div style="max-width:520px">
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.cashiers.update', $cashier) }}">
                @csrf @method('PUT')
                <div class="form-group">
                    <label class="form-label">Nama Lengkap *</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $cashier->name) }}" required>
                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Username *</label>
                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                           value="{{ old('username', $cashier->username) }}" required autocomplete="off">
                    @error('username')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Password Baru <small style="color:var(--muted); font-weight:normal;">(kosongkan jika tidak diubah)</small></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                           placeholder="Minimal 6 karakter">
                    @error('password')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="form-control"
                           placeholder="Ulangi password baru">
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                <button type="submit" class="btn btn-primary btn-full">Perbarui Akun</button>
            </form>
        </div>
    </div>
</div>
@endsection
