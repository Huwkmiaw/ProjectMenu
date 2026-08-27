@extends('layouts.auth')

@section('title', 'Login — MenuKasir')

@section('content')
<div class="auth-header">
    <div style="width:56px; height:56px; margin:0 auto 12px; background:linear-gradient(135deg, var(--color-primary), #fb923c); border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff;">
        <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
    </div>
    <h1>MenuKasir</h1>
    <p>Panel Kasir & Admin</p>
</div>

<div class="auth-body">
    @if ($errors->any())
        <div class="alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate>
        @csrf

        <div class="form-group">
            <label class="form-label" for="username">Username</label>
            <input
                type="text"
                name="username"
                id="username"
                class="form-control {{ $errors->has('username') ? 'is-invalid' : '' }}"
                value="{{ old('username') }}"
                placeholder="cth: admin atau kasir1"
                required
                autofocus
                autocomplete="username"
            >
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input
                type="password"
                name="password"
                id="password"
                class="form-control"
                placeholder="Masukkan password"
                required
                autocomplete="current-password"
            >
        </div>

        <div class="form-check">
            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
            <label for="remember">Ingat saya</label>
        </div>

        <button type="submit" class="btn-submit" id="loginBtn">
            Masuk ke Dashboard
        </button>
    </form>
</div>

<div class="auth-footer">
    <a href="{{ route('welcome') }}">← Kembali ke Halaman Pesan</a>
</div>

<script>
    document.getElementById('loginForm').addEventListener('submit', function () {
        const btn = document.getElementById('loginBtn');
        btn.disabled = true;
        btn.innerHTML = '<div class="spinner"></div> Memeriksa...';
    });
</script>
@endsection
