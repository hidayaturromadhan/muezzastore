<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'MuezzaStore') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-cyan: #03AEC6;
            --primary-navy: #01294D;
            --light-bg: #FDFDFD;
            --text-dark: #1a202c;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e9f2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-navy) 0%, #023a6b 100%);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .nav-link {
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--primary-cyan);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 80%;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-cyan) 0%, #029bb5 100%);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(3, 174, 198, 0.3);
        }

        .btn-outline {
            border: 2px solid var(--primary-cyan);
            color: var(--primary-cyan);
            background: transparent;
            transition: all 0.3s ease;
        }

        .btn-outline:hover {
            background: var(--primary-cyan);
            color: white;
            transform: translateY(-2px);
        }

        .alert {
            border-radius: 12px;
            border-left: 4px solid;
            animation: slideInDown 0.5s ease;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border-left-color: #28a745;
        }

        .alert-error {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            border-left-color: #dc3545;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .user-badge {
            background: rgba(3, 174, 198, 0.2);
            border: 2px solid var(--primary-cyan);
            border-radius: 50px;
            padding: 0.5rem 1rem;
            color: white;
            font-weight: 600;
        }

        .container-main {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        /* ── RESPONSIVE NAVBAR ───────────────────────── */

        /* Desktop: tampilkan nav links, sembunyikan hamburger */
        .nav-desktop {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .nav-hamburger { display: none; }

        .mobile-menu { display: none; }

        /* Mobile (<= 640px) */
        @media (max-width: 640px) {
            .nav-desktop { display: none; }

            .nav-hamburger {
                display: flex;
                flex-direction: column;
                justify-content: center;
                gap: 5px;
                width: 40px; height: 40px;
                background: rgba(255,255,255,0.08);
                border: 1.5px solid rgba(255,255,255,0.18);
                border-radius: 10px;
                cursor: pointer;
                padding: 0 10px;
                flex-shrink: 0;
                transition: background .2s;
            }
            .nav-hamburger:hover { background: rgba(255,255,255,0.14); }

            .nav-hamburger span {
                display: block;
                height: 2px;
                background: #fff;
                border-radius: 2px;
                transition: transform .3s, opacity .3s, width .3s;
                width: 100%;
            }

            /* X state */
            .nav-hamburger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
            .nav-hamburger.open span:nth-child(2) { opacity: 0; width: 0; }
            .nav-hamburger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

            /* Mobile dropdown */
            .mobile-menu {
                display: block;
                max-height: 0;
                overflow: hidden;
                transition: max-height .35s ease, opacity .3s ease;
                opacity: 0;
                background: linear-gradient(135deg, #012040 0%, #023060 100%);
                border-top: 1px solid rgba(255,255,255,0.08);
            }

            .mobile-menu.open {
                max-height: 400px;
                opacity: 1;
            }

            .mobile-menu-inner {
                padding: 1rem 1.25rem 1.25rem;
                display: flex;
                flex-direction: column;
                gap: .5rem;
            }

            .mobile-user-info {
                display: flex;
                align-items: center;
                gap: .625rem;
                padding: .625rem .875rem;
                background: rgba(3,174,198,0.12);
                border: 1px solid rgba(3,174,198,0.22);
                border-radius: 10px;
                color: #fff;
                font-family: 'Plus Jakarta Sans', sans-serif;
                font-size: .875rem;
                font-weight: 600;
                margin-bottom: .25rem;
            }
            .mobile-user-info i { color: #03AEC6; font-size: 1.1rem; }

            .mobile-nav-link {
                display: flex;
                align-items: center;
                gap: .625rem;
                padding: .75rem .875rem;
                border-radius: 10px;
                color: rgba(255,255,255,0.8);
                font-family: 'Plus Jakarta Sans', sans-serif;
                font-size: .9rem;
                font-weight: 600;
                text-decoration: none;
                transition: background .2s, color .2s;
            }
            .mobile-nav-link:hover { background: rgba(255,255,255,0.08); color: #fff; }
            .mobile-nav-link i { width: 18px; text-align: center; color: #03AEC6; }

            .mobile-logout-btn {
                display: flex;
                align-items: center;
                gap: .625rem;
                width: 100%;
                padding: .75rem .875rem;
                border-radius: 10px;
                background: transparent;
                border: 1.5px solid rgba(3,174,198,0.3);
                color: #03AEC6;
                font-family: 'Plus Jakarta Sans', sans-serif;
                font-size: .9rem;
                font-weight: 700;
                cursor: pointer;
                transition: background .2s, color .2s;
            }
            .mobile-logout-btn:hover { background: #03AEC6; color: #fff; }
            .mobile-logout-btn i { width: 18px; text-align: center; }

            .mobile-login-btn {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: .5rem;
                padding: .875rem;
                border-radius: 12px;
                background: linear-gradient(135deg, #03AEC6, #029bb5);
                color: #fff;
                font-family: 'Plus Jakarta Sans', sans-serif;
                font-size: .9375rem;
                font-weight: 700;
                text-decoration: none;
                transition: box-shadow .2s;
            }
            .mobile-login-btn:hover { box-shadow: 0 6px 18px rgba(3,174,198,0.4); color: #fff; }
        }

        /* Tablet (641–768px): shrink user-badge text */
        @media (min-width: 641px) and (max-width: 768px) {
            .user-badge span { max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        }
        /* WhatsApp Floating Button */
        .wa-container {
            position: fixed;
            bottom: 40px;
            right: 40px;
            z-index: 100;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .wa-tooltip {
            background-color: #fff;
            color: #1a202c;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            font-family: 'Plus Jakarta Sans', sans-serif;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            position: relative;
            animation: bounce-wa 3s infinite;
        }

        .wa-tooltip::after {
            content: '';
            position: absolute;
            top: 50%;
            right: -6px;
            margin-top: -6px;
            border-width: 6px 0 6px 6px;
            border-style: solid;
            border-color: transparent transparent transparent #fff;
        }

        .wa-float {
            width: 60px;
            height: 60px;
            background-color: #25d366;
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 32px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
            animation: bounce-wa 3s infinite;
        }

        .wa-container:hover .wa-tooltip,
        .wa-container:hover .wa-float {
            animation: none;
        }

        .wa-float:hover {
            background-color: #128C7E;
            color: #FFF;
            transform: scale(1.1);
        }

        @keyframes bounce-wa {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        @media (max-width: 640px) {
            .wa-container {
                bottom: 20px;
                right: 20px;
                gap: 10px;
            }
            .wa-tooltip {
                font-size: 12px;
                padding: 6px 12px;
            }
            .wa-float {
                width: 50px;
                height: 50px;
                font-size: 26px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar sticky top-0 z-50" id="mainNav">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">

                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center" style="gap:0.6rem;text-decoration:none;flex-shrink:0;">
                    <img src="{{ asset('images/logo.png') }}" alt="MuezzaStore"
                         style="height:38px;width:auto;object-fit:contain;filter:brightness(0) saturate(100%) invert(62%) sepia(97%) saturate(400%) hue-rotate(155deg) brightness(95%);">
                    <span style="font-family:'Plus Jakarta Sans','Segoe UI',sans-serif;font-size:1.2rem;font-weight:800;letter-spacing:-0.02em;color:#fff;line-height:1;">
                        Muezza<span style="color:#03AEC6;">Store</span>
                    </span>
                </a>

                <!-- Desktop Nav -->
                <div class="nav-desktop">
                    @auth
                        <a href="{{ route('orders.index') }}" class="nav-link text-white text-sm font-semibold" style="padding:.5rem .75rem;white-space:nowrap;">
                            <i class="fas fa-receipt" style="margin-right:.3rem;"></i> Riwayat
                        </a>
                        <div class="user-badge flex items-center" style="gap:.5rem;max-width:180px;overflow:hidden;">
                            <i class="fas fa-user-circle" style="flex-shrink:0;"></i>
                            <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ auth()->user()->username }}</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="btn-outline px-4 py-2 rounded-lg font-semibold text-sm" style="white-space:nowrap;">
                                <i class="fas fa-sign-out-alt" style="margin-right:.25rem;"></i> Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary px-5 py-2 rounded-lg font-semibold text-sm text-white" style="white-space:nowrap;">
                            <i class="fas fa-sign-in-alt" style="margin-right:.25rem;"></i> Login
                        </a>
                    @endauth
                </div>

                <!-- Hamburger -->
                <button id="navToggle" class="nav-hamburger" aria-label="Buka menu">
                    <span></span><span></span><span></span>
                </button>

            </div>
        </div>

        <!-- Mobile Dropdown -->
        <div id="mobileMenu" class="mobile-menu" aria-hidden="true">
            <div class="mobile-menu-inner">
                @auth
                    <div class="mobile-user-info">
                        <i class="fas fa-user-circle"></i>
                        <span>{{ auth()->user()->username }}</span>
                    </div>
                    <a href="{{ route('orders.index') }}" class="mobile-nav-link">
                        <i class="fas fa-receipt"></i> Riwayat Pesanan
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="mobile-logout-btn">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="mobile-login-btn">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Alert Messages -->
    <div class="container mx-auto px-4 mt-4">
        @if (session('success'))
            <div class="alert alert-success p-4 rounded-lg shadow-md flex items-center space-x-3">
                <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                <span class="text-green-800 font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error p-4 rounded-lg shadow-md">
                <div class="flex items-start space-x-3">
                    <i class="fas fa-exclamation-circle text-red-600 text-2xl mt-1"></i>
                    <div class="flex-1">
                        <h4 class="font-bold text-red-800 mb-2">Terjadi error:</h4>
                        <ul class="list-none space-y-1">
                            @foreach ($errors->all() as $err)
                                <li class="text-red-700">• {{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="container-main" style="flex: 1;">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-gray-800 to-gray-900 text-white mt-16 py-8">
        <div class="container mx-auto px-4 text-center">
            <div class="flex flex-col items-center gap-3">
                <div class="flex items-center" style="gap: 0.625rem; justify-content: center;">
                    <img
                        src="{{ asset('images/logo.png') }}"
                        alt="MuezzaStore"
                        style="height: 36px; width: auto; object-fit: contain; filter: brightness(0) saturate(100%) invert(62%) sepia(97%) saturate(400%) hue-rotate(155deg) brightness(95%); opacity: 0.85;"
                    >
                    <span style="
                        font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
                        font-size: 1.125rem;
                        font-weight: 800;
                        letter-spacing: -0.02em;
                        color: rgba(255,255,255,0.85);
                        line-height: 1;
                    ">Muezza<span style="color: #03AEC6;">Store</span></span>
                </div>
                <p class="text-gray-400 text-sm">&copy; {{ date('Y') }} {{ config('app.name', 'MuezzaStore') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Floating Button -->
    <div class="wa-container">
        <div class="wa-tooltip">Butuh bantuan? Chat kami!</div>
        <a href="https://wa.me/{{ env('WHATSAPP_NUMBER', '6281234567890') }}?text=Halo%20MuezzaStore,%20saya%20butuh%20bantuan." class="wa-float" target="_blank" rel="noopener noreferrer" aria-label="Chat WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
    </div>

    <script>
        (function () {
            const toggle = document.getElementById('navToggle');
            const menu   = document.getElementById('mobileMenu');
            if (!toggle || !menu) return;

            toggle.addEventListener('click', () => {
                const isOpen = menu.classList.toggle('open');
                toggle.classList.toggle('open', isOpen);
                toggle.setAttribute('aria-label', isOpen ? 'Tutup menu' : 'Buka menu');
                menu.setAttribute('aria-hidden', !isOpen);
            });

            // tutup menu saat klik link di dalam
            menu.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    menu.classList.remove('open');
                    toggle.classList.remove('open');
                });
            });

            // tutup saat klik di luar navbar
            document.addEventListener('click', (e) => {
                const nav = document.getElementById('mainNav');
                if (nav && !nav.contains(e.target)) {
                    menu.classList.remove('open');
                    toggle.classList.remove('open');
                }
            });
        })();
    </script>
</body>
</html>