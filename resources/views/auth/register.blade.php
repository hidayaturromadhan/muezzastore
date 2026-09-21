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

    .login-wrap {
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .login-card {
        width: 100%;
        max-width: 480px;
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 8px 40px rgba(1,41,77,0.10);
        border: 1.5px solid var(--border);
        overflow: hidden;
    }

    .login-card-top {
        height: 5px;
        background: linear-gradient(90deg, var(--cyan), var(--cyan2), var(--navy));
    }

    .login-card-body { padding: 2.25rem 2.5rem 2.5rem; }

    /* logo */
    .login-logo {
        display: flex; flex-direction: column; align-items: center;
        margin-bottom: 1.75rem;
    }
    .login-logo-mark {
        width: 56px; height: 56px; border-radius: 16px;
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
        font-size: 1.25rem; font-weight: 800; color: var(--navy);
        letter-spacing: -.02em; line-height: 1; margin-bottom: .3rem;
    }
    .login-brand span { color: var(--cyan); }
    .login-sub { font-size: .8125rem; color: var(--gray); font-weight: 500; }

    .login-divider { border: none; border-top: 1px solid var(--border); margin: 0 0 1.5rem; }

    .login-title {
        font-family: 'Syne', sans-serif;
        font-size: 1.25rem; font-weight: 800; color: var(--navy); margin: 0 0 1.375rem;
    }

    /* fields */
    .field { margin-bottom: 1rem; }
    .field-label {
        display: block; font-size: .8125rem; font-weight: 700;
        color: var(--navy); margin-bottom: .375rem;
    }
    .field-input-wrap { position: relative; }
    .field-icon {
        position: absolute; left: 1rem; top: 50%;
        transform: translateY(-50%);
        color: #94a3b8; font-size: .875rem; pointer-events: none;
    }
    .field-input {
        width: 100%; height: 48px;
        padding: 0 1rem 0 2.75rem;
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
    .field-input.has-toggle { padding-right: 2.75rem; }

    /* password strength bar */
    .pw-strength { margin-top: .5rem; }
    .pw-strength-bar {
        height: 4px; border-radius: 4px; background: var(--border);
        overflow: hidden; margin-bottom: .3rem;
    }
    .pw-strength-fill {
        height: 100%; border-radius: 4px; width: 0;
        transition: width .3s, background .3s;
    }
    .pw-strength-label { font-size: .7rem; font-weight: 600; color: var(--gray); }

    /* password match indicator */
    .pw-match {
        margin-top: .375rem;
        font-size: .7rem; font-weight: 600;
        display: none; align-items: center; gap: .3rem;
    }
    .pw-match.show { display: flex; }
    .pw-match.ok  { color: #16a34a; }
    .pw-match.err { color: #dc2626; }

    /* toggle pw */
    .toggle-pw {
        position: absolute; right: 1rem; top: 50%; transform: translateY(-50%);
        background: none; border: none; cursor: pointer;
        color: #94a3b8; font-size: .875rem; padding: 0; transition: color .2s;
    }
    .toggle-pw:hover { color: var(--cyan); }

    /* 2-col grid for email+username */
    .field-row { display: grid; grid-template-columns: 1fr 1fr; gap: .875rem; }

    /* submit */
    .btn-submit {
        width: 100%; height: 50px;
        background: linear-gradient(135deg, var(--cyan), var(--cyan2));
        color: #fff; border: none; border-radius: 12px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: .9375rem; font-weight: 700; cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: .5rem;
        transition: box-shadow .2s, transform .2s; margin: 1.375rem 0 1.125rem;
    }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(3,174,198,0.35); }

    /* login link */
    .login-row {
        text-align: center; font-size: .8125rem; color: var(--gray); font-weight: 500;
    }
    .login-row a { color: var(--cyan); font-weight: 700; text-decoration: none; }
    .login-row a:hover { text-decoration: underline; }

    /* alert */
    .alert-box {
        display: flex; align-items: flex-start; gap: .625rem;
        padding: .875rem 1rem; border-radius: 12px; margin-bottom: 1.125rem;
        font-size: .875rem; font-weight: 500;
        background: #fef2f2; border-left: 3px solid #dc2626; color: #dc2626;
    }

    /* terms note */
    .terms-note {
        font-size: .75rem; color: var(--gray); text-align: center;
        margin-top: .875rem; line-height: 1.5;
    }

    @media (max-width: 480px) {
        .login-card-body { padding: 1.75rem 1.375rem 2rem; }
        .field-row { grid-template-columns: 1fr; }
    }
</style>

<div class="login-wrap">
    <div class="login-card">

        <div class="login-card-top"></div>

        <div class="login-card-body">

            {{-- Logo --}}
            <div class="login-logo">
                <div class="login-logo-mark">
                    <img src="{{ asset('images/logo.png') }}" alt="MuezzaStore">
                </div>
                <div class="login-brand">Muezza<span>Store</span></div>
                <div class="login-sub">Buat akun dan mulai top up sekarang</div>
            </div>

            <hr class="login-divider">

            <h2 class="login-title">Buat Akun Baru</h2>

            {{-- Errors --}}
            @if($errors->any())
                <div class="alert-box">
                    <i class="fas fa-exclamation-circle" style="margin-top:2px;flex-shrink:0;"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}">
                @csrf

                {{-- Username + Email --}}
                <div class="field-row">
                    <div class="field">
                        <label class="field-label" for="username">Username</label>
                        <div class="field-input-wrap">
                            <i class="fas fa-at field-icon"></i>
                            <input
                                id="username"
                                name="username"
                                type="text"
                                class="field-input"
                                placeholder="username_kamu"
                                value="{{ old('username') }}"
                                autocomplete="username"
                                required
                            >
                        </div>
                    </div>

                    <div class="field">
                        <label class="field-label" for="email">Email</label>
                        <div class="field-input-wrap">
                            <i class="fas fa-envelope field-icon"></i>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                class="field-input"
                                placeholder="email@kamu.com"
                                value="{{ old('email') }}"
                                autocomplete="email"
                                required
                            >
                        </div>
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
                            placeholder="Minimal 8 karakter"
                            autocomplete="new-password"
                            required
                        >
                        <button type="button" class="toggle-pw" data-target="password" tabindex="-1">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    {{-- strength bar --}}
                    <div class="pw-strength">
                        <div class="pw-strength-bar">
                            <div class="pw-strength-fill" id="strengthFill"></div>
                        </div>
                        <span class="pw-strength-label" id="strengthLabel"></span>
                    </div>
                </div>

                {{-- Confirm Password --}}
                <div class="field">
                    <label class="field-label" for="password_confirmation">Konfirmasi Password</label>
                    <div class="field-input-wrap">
                        <i class="fas fa-lock field-icon"></i>
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            class="field-input has-toggle"
                            placeholder="Ulangi password"
                            autocomplete="new-password"
                            required
                        >
                        <button type="button" class="toggle-pw" data-target="password_confirmation" tabindex="-1">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="pw-match" id="pwMatch">
                        <i class="fas fa-circle-check"></i>
                        <span id="pwMatchText"></span>
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-submit">
                    <i class="fas fa-user-plus"></i> Daftar Sekarang
                </button>

                {{-- Login link --}}
                <div class="login-row">
                    Sudah punya akun?
                    <a href="{{ route('login') }}">Masuk di sini</a>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    /* ── toggle show/hide password ── */
    document.querySelectorAll('.toggle-pw').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.target);
            const ico   = btn.querySelector('i');
            const show  = input.type === 'password';
            input.type      = show ? 'text' : 'password';
            ico.className   = show ? 'fas fa-eye-slash' : 'fas fa-eye';
        });
    });

    /* ── password strength ── */
    const pwInput      = document.getElementById('password');
    const strengthFill = document.getElementById('strengthFill');
    const strengthLbl  = document.getElementById('strengthLabel');

    const getStrength = v => {
        let s = 0;
        if (v.length >= 8)              s++;
        if (/[A-Z]/.test(v))            s++;
        if (/[0-9]/.test(v))            s++;
        if (/[^A-Za-z0-9]/.test(v))     s++;
        return s;
    };

    const levels = [
        { pct: '0%',   color: 'transparent', label: '' },
        { pct: '25%',  color: '#ef4444',      label: 'Lemah' },
        { pct: '50%',  color: '#f97316',      label: 'Cukup' },
        { pct: '75%',  color: '#eab308',      label: 'Baik' },
        { pct: '100%', color: '#22c55e',      label: 'Kuat' },
    ];

    pwInput?.addEventListener('input', () => {
        const s   = pwInput.value.length ? getStrength(pwInput.value) : 0;
        const lvl = levels[s];
        strengthFill.style.width      = lvl.pct;
        strengthFill.style.background = lvl.color;
        strengthLbl.textContent       = lvl.label;
        strengthLbl.style.color       = lvl.color;
        checkMatch();
    });

    /* ── password match ── */
    const pwConfirm = document.getElementById('password_confirmation');
    const matchBox  = document.getElementById('pwMatch');
    const matchText = document.getElementById('pwMatchText');
    const matchIco  = matchBox.querySelector('i');

    const checkMatch = () => {
        const a = pwInput.value, b = pwConfirm.value;
        if (!b) { matchBox.className = 'pw-match'; return; }
        if (a === b) {
            matchBox.className  = 'pw-match show ok';
            matchIco.className  = 'fas fa-circle-check';
            matchText.textContent = 'Password cocok';
        } else {
            matchBox.className  = 'pw-match show err';
            matchIco.className  = 'fas fa-circle-xmark';
            matchText.textContent = 'Password tidak cocok';
        }
    };

    pwConfirm?.addEventListener('input', checkMatch);
});
</script>
@endsection