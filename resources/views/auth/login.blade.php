@extends('layouts.app')

@section('content')
<style>
    :root {
        --cyan:  #03AEC6;
        --cyan2: #029bb5;
        --navy:  #01294D;
        --gray:  #64748b;
        --border:#e2e8f0;
    }
    *, *::before, *::after { box-sizing: border-box; }

    /* ── CENTER WRAPPER ──────────────────────────────── */
    .login-wrap {
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* ── CARD ────────────────────────────────────────── */
    .login-card {
        width: 100%;
        max-width: 440px;
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 8px 40px rgba(1,41,77,0.10);
        border: 1.5px solid var(--border);
        overflow: hidden;
    }

    /* top accent bar */
    .login-card-top {
        height: 5px;
        background: linear-gradient(90deg, var(--cyan), var(--cyan2), var(--navy));
    }

    .login-card-body { padding: 2.25rem 2.5rem 2.5rem; }

    /* logo area */
    .login-logo {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 1.875rem;
    }

    .login-logo-mark {
        width: 56px; height: 56px;
        border-radius: 16px;
        background: linear-gradient(135deg, rgba(3,174,198,0.12), rgba(3,174,198,0.06));
        border: 1.5px solid rgba(3,174,198,0.25);
        display: flex; align-items: center; justify-content: center;
        margin-bottom: .875rem;
    }

    .login-logo-mark img {
        height: 34px; width: auto; object-fit: contain;
        filter: brightness(0) saturate(100%) invert(62%) sepia(97%) saturate(400%) hue-rotate(155deg) brightness(95%);
    }

    .login-brand {
        font-family: 'Syne', sans-serif;
        font-size: 1.25rem; font-weight: 800;
        color: var(--navy); letter-spacing: -.02em; line-height: 1;
        margin-bottom: .3rem;
    }
    .login-brand span { color: var(--cyan); }

    .login-sub {
        font-size: .8125rem; color: var(--gray); font-weight: 500;
    }

    /* divider */
    .login-divider {
        border: none; border-top: 1px solid var(--border);
        margin: 0 0 1.625rem;
    }

    /* title */
    .login-title {
        font-family: 'Syne', sans-serif;
        font-size: 1.25rem; font-weight: 800;
        color: var(--navy); margin: 0 0 1.5rem;
    }

    /* fields */
    .field { margin-bottom: 1.125rem; }

    .field-label {
        display: block;
        font-size: .8125rem; font-weight: 700; color: var(--navy);
        margin-bottom: .4rem;
    }

    .field-input-wrap { position: relative; }

    .field-icon {
        position: absolute; left: 1rem; top: 50%;
        transform: translateY(-50%);
        color: #94a3b8; font-size: .9rem; pointer-events: none;
    }

    .field-input {
        width: 100%; height: 50px;
        padding: 0 1rem 0 2.875rem;
        border: 2px solid var(--border); border-radius: 12px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: .9375rem; color: var(--navy); background: #f8fafc;
        transition: border-color .2s, box-shadow .2s, background .2s;
    }
    .field-input:focus {
        outline: none; border-color: var(--cyan);
        background: #fff; box-shadow: 0 0 0 3px rgba(3,174,198,0.10);
    }
    .field-input::placeholder { color: #94a3b8; }

    /* password toggle */
    .toggle-pw {
        position: absolute; right: 1rem; top: 50%;
        transform: translateY(-50%);
        background: none; border: none; cursor: pointer;
        color: #94a3b8; font-size: .9rem; padding: 0;
        transition: color .2s;
    }
    .toggle-pw:hover { color: var(--cyan); }
    /* give room for eye icon */
    .field-input.has-toggle { padding-right: 2.875rem; }

    /* remember me */
    .remember-row {
        display: flex; align-items: center; gap: .625rem;
        margin-bottom: 1.5rem;
    }

    .custom-check {
        width: 18px; height: 18px; flex-shrink: 0;
        border: 2px solid var(--border); border-radius: 5px;
        appearance: none; -webkit-appearance: none;
        background: #f8fafc; cursor: pointer;
        transition: border-color .2s, background .2s;
        display: grid; place-items: center;
    }
    .custom-check:checked {
        background: var(--cyan); border-color: var(--cyan);
    }
    .custom-check:checked::after {
        content: '';
        width: 10px; height: 7px;
        background: url("data:image/svg+xml,%3Csvg viewBox='0 0 10 7' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 3.5L3.8 6L9 1' stroke='white' stroke-width='1.8' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") no-repeat center / contain;
        display: block;
    }
    .custom-check:focus { outline: none; box-shadow: 0 0 0 3px rgba(3,174,198,0.15); }

    .remember-label {
        font-size: .8125rem; color: var(--gray); font-weight: 500; cursor: pointer;
    }

    /* submit */
    .btn-login-submit {
        width: 100%; height: 50px;
        background: linear-gradient(135deg, var(--cyan), var(--cyan2));
        color: #fff; border: none; border-radius: 12px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: .9375rem; font-weight: 700; cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: .5rem;
        transition: box-shadow .2s, transform .2s;
        margin-bottom: 1.25rem;
    }
    .btn-login-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(3,174,198,0.35);
    }

    /* register link */
    .register-row {
        text-align: center;
        font-size: .8125rem; color: var(--gray); font-weight: 500;
    }
    .register-row a {
        color: var(--cyan); font-weight: 700; text-decoration: none;
    }
    .register-row a:hover { text-decoration: underline; }

    /* error alert */
    .alert-box {
        display: flex; align-items: flex-start; gap: .625rem;
        padding: .875rem 1rem; border-radius: 12px; margin-bottom: 1.25rem;
        font-size: .875rem; font-weight: 500;
        background: #fef2f2; border-left: 3px solid #dc2626; color: #dc2626;
    }

    @media (max-width: 480px) {
        .login-card-body { padding: 1.75rem 1.5rem 2rem; }
    }
</style>

<div class="login-wrap">
    <div class="login-card">

        {{-- accent bar --}}
        <div class="login-card-top"></div>

        <div class="login-card-body">

            {{-- Logo --}}
            <div class="login-logo">
                <div class="login-logo-mark">
                    <img src="{{ asset('images/logo.png') }}" alt="MuezzaStore">
                </div>
                <div class="login-brand">Muezza<span>Store</span></div>
                <div class="login-sub">Platform Top Up Digital Terpercaya</div>
            </div>

            <hr class="login-divider">

            <h2 class="login-title">Masuk ke Akun</h2>

            {{-- Errors --}}
            @if($errors->any())
                <div class="alert-box">
                    <i class="fas fa-exclamation-circle" style="margin-top:2px;flex-shrink:0;"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                {{-- Username / Email --}}
                <div class="field">
                    <label class="field-label" for="login">
                        Username atau Email
                    </label>
                    <div class="field-input-wrap">
                        <i class="fas fa-user field-icon"></i>
                        <input
                            id="login"
                            name="login"
                            type="text"
                            class="field-input"
                            placeholder="Masukkan username atau email"
                            value="{{ old('login') }}"
                            autocomplete="username"
                            required
                        >
                    </div>
                </div>

                {{-- Password --}}
                <div class="field">
                    <label class="field-label" for="password">Password</label>
                    <div class="field-input-wrap">
                        <i class="fas fa-lock field-icon"></i>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            class="field-input has-toggle"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button" class="toggle-pw" id="togglePw" tabindex="-1" aria-label="Toggle password">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                {{-- Remember me --}}
                <div class="remember-row">
                    <input class="custom-check" type="checkbox" name="remember" id="remember">
                    <label class="remember-label" for="remember">Ingat saya di perangkat ini</label>
                </div>

                <a href="{{ route('google.redirect') }}" class="btn-login-submit" style="background:#fff;color:#01294D;border:2px solid #e2e8f0;">
                    <i class="fab fa-google"></i> Masuk dengan Google
                </a>


                {{-- Submit --}}
                <button type="submit" class="btn-login-submit">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </button>

                {{-- Register link --}}
                <div class="register-row">
                    Belum punya akun?
                    <a href="{{ route('register') }}">Daftar sekarang</a>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    // Password show/hide toggle
    const pwInput   = document.getElementById('password');
    const toggleBtn = document.getElementById('togglePw');
    const toggleIco = document.getElementById('toggleIcon');

    toggleBtn?.addEventListener('click', () => {
        const isHidden = pwInput.type === 'password';
        pwInput.type        = isHidden ? 'text' : 'password';
        toggleIco.className = isHidden ? 'fas fa-eye-slash' : 'fas fa-eye';
    });
</script>
@endsection