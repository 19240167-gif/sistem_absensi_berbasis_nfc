<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Sistem Informasi Absensi Sekolah — Berbasis NFC</title>
    <meta name="description" content="Sistem absensi sekolah modern berbasis NFC dengan dashboard real-time, multi-role, dan kiosk mode untuk pencatatan kehadiran otomatis.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --teal-50: #f0fdfa;
            --teal-100: #ccfbf1;
            --teal-200: #99f6e4;
            --teal-400: #2dd4bf;
            --teal-500: #14b8a6;
            --teal-600: #0d9488;
            --teal-700: #0f766e;
            --teal-800: #115e59;
            --teal-900: #134e4a;
            --sky-400: #38bdf8;
            --sky-500: #0ea5e9;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-300: #cbd5e1;
            --slate-400: #94a3b8;
            --slate-500: #64748b;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
            --slate-950: #020617;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: var(--slate-800);
            background: var(--slate-50);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* ═══════ Navbar ═══════ */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            padding: 16px 0;
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(148, 163, 184, 0.1);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        }

        .navbar-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--slate-900);
            font-weight: 800;
            font-size: 17px;
        }

        .navbar-logo {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--teal-500), var(--sky-500));
            display: grid;
            place-items: center;
            color: white;
            font-size: 18px;
            box-shadow: 0 4px 12px rgba(20, 184, 166, 0.3);
        }

        .navbar-links {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .navbar-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .navbar-link--ghost {
            color: var(--slate-600);
            background: transparent;
        }

        .navbar-link--ghost:hover {
            color: var(--teal-700);
            background: var(--teal-50);
        }

        .navbar-link--primary {
            color: white;
            background: linear-gradient(135deg, var(--teal-500), var(--teal-600));
            box-shadow: 0 4px 14px rgba(20, 184, 166, 0.3);
        }

        .navbar-link--primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(20, 184, 166, 0.4);
        }

        /* ═══════ Hero ═══════ */
        .hero {
            position: relative;
            padding: 160px 32px 100px;
            overflow: hidden;
            background:
                radial-gradient(900px 500px at 20% 30%, rgba(20, 184, 166, 0.06) 0%, transparent 60%),
                radial-gradient(700px 400px at 80% 20%, rgba(56, 189, 248, 0.05) 0%, transparent 60%),
                linear-gradient(180deg, var(--slate-50) 0%, white 100%);
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 60px;
            right: -100px;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(20, 184, 166, 0.05) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 64px;
            align-items: center;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px 6px 8px;
            border-radius: 999px;
            background: var(--teal-50);
            border: 1px solid var(--teal-200);
            font-size: 13px;
            font-weight: 600;
            color: var(--teal-700);
            margin-bottom: 20px;
            animation: fadeInUp 0.6s ease both;
        }

        .hero-badge-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--teal-500);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        .hero-title {
            font-size: clamp(36px, 5vw, 56px);
            font-weight: 900;
            letter-spacing: -0.04em;
            line-height: 1.1;
            margin-bottom: 20px;
            animation: fadeInUp 0.6s 0.1s ease both;
        }

        .hero-title .highlight {
            background: linear-gradient(135deg, var(--teal-500), var(--sky-500));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-description {
            font-size: 17px;
            color: var(--slate-500);
            max-width: 500px;
            line-height: 1.7;
            margin-bottom: 32px;
            animation: fadeInUp 0.6s 0.2s ease both;
        }

        .hero-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            animation: fadeInUp 0.6s 0.3s ease both;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 15px;
            text-decoration: none;
            transition: all 0.25s ease;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }

        .btn--primary {
            color: white;
            background: linear-gradient(135deg, var(--teal-500), var(--teal-600));
            box-shadow: 0 8px 24px rgba(20, 184, 166, 0.3);
        }

        .btn--primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(20, 184, 166, 0.4);
        }

        .btn--secondary {
            color: var(--slate-700);
            background: white;
            border: 1px solid var(--slate-200);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }

        .btn--secondary:hover {
            border-color: var(--teal-300);
            color: var(--teal-700);
            transform: translateY(-1px);
        }

        /* Hero Card Visual */
        .hero-visual {
            position: relative;
            animation: fadeInUp 0.8s 0.3s ease both;
        }

        .hero-card {
            background: var(--slate-900);
            border-radius: 24px;
            padding: 32px;
            color: white;
            box-shadow:
                0 32px 64px rgba(15, 23, 42, 0.2),
                0 0 0 1px rgba(148, 163, 184, 0.08);
            transform: perspective(800px) rotateY(-4deg) rotateX(2deg);
            transition: transform 0.4s ease;
        }

        .hero-card:hover {
            transform: perspective(800px) rotateY(0deg) rotateX(0deg);
        }

        .hero-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .hero-card-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--slate-400);
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .hero-card-live {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--teal-400);
            font-weight: 600;
        }

        .hero-card-live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--teal-400);
            animation: pulse 1.5s ease-in-out infinite;
        }

        .hero-card-student {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 20px;
            background: rgba(30, 41, 59, 0.8);
            border-radius: 16px;
            border: 1px solid rgba(148, 163, 184, 0.1);
            margin-bottom: 20px;
        }

        .hero-card-avatar {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--teal-500), var(--sky-500));
            display: grid;
            place-items: center;
            font-weight: 800;
            font-size: 22px;
            flex-shrink: 0;
        }

        .hero-card-student-name {
            font-size: 18px;
            font-weight: 700;
        }

        .hero-card-student-class {
            font-size: 13px;
            color: var(--slate-400);
            margin-top: 2px;
        }

        .hero-card-status {
            margin-left: auto;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            background: rgba(52, 211, 153, 0.15);
            color: #34d399;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .hero-card-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .hero-card-stat {
            text-align: center;
            padding: 12px;
            border-radius: 12px;
            background: rgba(30, 41, 59, 0.6);
        }

        .hero-card-stat-value {
            font-size: 22px;
            font-weight: 800;
        }

        .hero-card-stat-label {
            font-size: 11px;
            color: var(--slate-500);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-top: 2px;
        }

        .stat-green { color: #34d399; }
        .stat-yellow { color: #fbbf24; }
        .stat-red { color: #f87171; }

        /* ═══════ Features ═══════ */
        .features {
            padding: 100px 32px;
            background: white;
        }

        .features-inner {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-label {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--teal-600);
            margin-bottom: 12px;
            text-align: center;
        }

        .section-title {
            font-size: clamp(28px, 3.5vw, 40px);
            font-weight: 800;
            letter-spacing: -0.03em;
            text-align: center;
            margin-bottom: 12px;
        }

        .section-description {
            font-size: 16px;
            color: var(--slate-500);
            text-align: center;
            max-width: 540px;
            margin: 0 auto 56px;
            line-height: 1.7;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .feature-card {
            padding: 32px;
            border-radius: 20px;
            background: var(--slate-50);
            border: 1px solid rgba(148, 163, 184, 0.1);
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
            border-color: rgba(20, 184, 166, 0.2);
        }

        .feature-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .feature-icon--teal {
            background: linear-gradient(135deg, rgba(20, 184, 166, 0.15), rgba(20, 184, 166, 0.05));
        }

        .feature-icon--sky {
            background: linear-gradient(135deg, rgba(56, 189, 248, 0.15), rgba(56, 189, 248, 0.05));
        }

        .feature-icon--amber {
            background: linear-gradient(135deg, rgba(251, 191, 36, 0.15), rgba(251, 191, 36, 0.05));
        }

        .feature-icon--rose {
            background: linear-gradient(135deg, rgba(251, 113, 133, 0.15), rgba(251, 113, 133, 0.05));
        }

        .feature-icon--violet {
            background: linear-gradient(135deg, rgba(167, 139, 250, 0.15), rgba(167, 139, 250, 0.05));
        }

        .feature-icon--emerald {
            background: linear-gradient(135deg, rgba(52, 211, 153, 0.15), rgba(52, 211, 153, 0.05));
        }

        .feature-title {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--slate-900);
        }

        .feature-description {
            font-size: 14px;
            color: var(--slate-500);
            line-height: 1.6;
        }

        /* ═══════ Roles Section ═══════ */
        .roles {
            padding: 100px 32px;
            background:
                radial-gradient(800px at 50% 0%, rgba(20, 184, 166, 0.04) 0%, transparent 60%),
                var(--slate-50);
        }

        .roles-inner {
            max-width: 1200px;
            margin: 0 auto;
        }

        .roles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
        }

        .role-card {
            padding: 36px;
            border-radius: 24px;
            background: white;
            border: 1px solid rgba(148, 163, 184, 0.1);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
        }

        .role-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 48px rgba(0, 0, 0, 0.08);
        }

        .role-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            font-size: 26px;
            margin-bottom: 20px;
        }

        .role-icon--admin {
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: white;
            box-shadow: 0 6px 16px rgba(249, 115, 22, 0.3);
        }

        .role-icon--guru {
            background: linear-gradient(135deg, var(--teal-500), var(--teal-600));
            color: white;
            box-shadow: 0 6px 16px rgba(20, 184, 166, 0.3);
        }

        .role-icon--siswa {
            background: linear-gradient(135deg, var(--sky-400), var(--sky-500));
            color: white;
            box-shadow: 0 6px 16px rgba(56, 189, 248, 0.3);
        }

        .role-title {
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .role-description {
            font-size: 14px;
            color: var(--slate-500);
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .role-features {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .role-features li {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: var(--slate-600);
        }

        .role-features li::before {
            content: '✓';
            width: 20px;
            height: 20px;
            border-radius: 6px;
            background: var(--teal-50);
            color: var(--teal-600);
            display: grid;
            place-items: center;
            font-size: 11px;
            font-weight: 800;
            flex-shrink: 0;
        }

        /* ═══════ CTA ═══════ */
        .cta {
            padding: 80px 32px;
            background: linear-gradient(135deg, var(--slate-900), var(--slate-800));
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta::before {
            content: '';
            position: absolute;
            top: -200px;
            left: 50%;
            transform: translateX(-50%);
            width: 800px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(20, 184, 166, 0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        .cta-inner {
            max-width: 600px;
            margin: 0 auto;
            position: relative;
        }

        .cta-title {
            font-size: clamp(26px, 3vw, 36px);
            font-weight: 800;
            color: white;
            letter-spacing: -0.02em;
            margin-bottom: 16px;
        }

        .cta-description {
            font-size: 16px;
            color: var(--slate-400);
            margin-bottom: 32px;
            line-height: 1.7;
        }

        .cta-actions {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn--white {
            color: var(--slate-900);
            background: white;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        }

        .btn--white:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.3);
        }

        .btn--outline {
            color: var(--slate-300);
            background: transparent;
            border: 1px solid rgba(148, 163, 184, 0.2);
        }

        .btn--outline:hover {
            border-color: var(--teal-500);
            color: var(--teal-400);
        }

        /* ═══════ Footer ═══════ */
        .footer {
            padding: 32px;
            background: var(--slate-950);
            text-align: center;
        }

        .footer p {
            font-size: 13px;
            color: var(--slate-600);
        }

        .footer a {
            color: var(--teal-500);
            text-decoration: none;
        }

        /* ═══════ Animations ═══════ */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ═══════ Responsive ═══════ */
        @media (max-width: 900px) {
            .hero-inner {
                grid-template-columns: 1fr;
                gap: 48px;
            }

            .hero-card {
                transform: none;
            }

            .hero-card:hover {
                transform: none;
            }

            .navbar-links {
                gap: 4px;
            }

            .navbar-link--ghost span { display: none; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.001ms !important;
                transition-duration: 0.001ms !important;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar" id="navbar">
        <div class="navbar-inner">
            <a href="/" class="navbar-brand">
                <div class="navbar-logo">📡</div>
                Absensi NFC
            </a>
            <div class="navbar-links">
                <a href="#features" class="navbar-link navbar-link--ghost"><span>Fitur</span></a>
                <a href="#roles" class="navbar-link navbar-link--ghost"><span>Role</span></a>
                @auth
                    @if(auth()->user()->isSiswa())
                        <a href="/student" class="navbar-link navbar-link--primary">Dashboard →</a>
                    @else
                        <a href="/admin" class="navbar-link navbar-link--primary">Dashboard →</a>
                    @endif
                @else
                    <a href="/admin/login" class="navbar-link navbar-link--primary">Masuk →</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-inner">
            <div>
                <div class="hero-badge">
                    <span class="hero-badge-dot"></span>
                    Sistem Absensi Modern
                </div>
                <h1 class="hero-title">
                    Absensi Sekolah <span class="highlight">Berbasis NFC</span> yang Cerdas
                </h1>
                <p class="hero-description">
                    Pencatatan kehadiran otomatis dengan teknologi NFC — cukup tempel kartu atau handphone siswa. 
                    Dashboard real-time untuk admin, guru, dan siswa.
                </p>
                <div class="hero-actions">
                    @auth
                        @if(auth()->user()->isSiswa())
                            <a href="/student" class="btn btn--primary">Buka Dashboard →</a>
                        @else
                            <a href="/admin" class="btn btn--primary">Buka Dashboard →</a>
                        @endif
                    @else
                        <a href="/admin/login" class="btn btn--primary">Mulai Sekarang →</a>
                    @endauth
                    <a href="#features" class="btn btn--secondary">Pelajari Fitur</a>
                </div>
            </div>

            <!-- Hero Visual -->
            <div class="hero-visual">
                <div class="hero-card">
                    <div class="hero-card-header">
                        <div class="hero-card-title">Mode Kiosk NFC</div>
                        <div class="hero-card-live">
                            <span class="hero-card-live-dot"></span>
                            Live
                        </div>
                    </div>
                    <div class="hero-card-student">
                        <div class="hero-card-avatar">S</div>
                        <div>
                            <div class="hero-card-student-name">Siti Aminah</div>
                            <div class="hero-card-student-class">Kelas X IPA 1</div>
                        </div>
                        <div class="hero-card-status">✓ Hadir</div>
                    </div>
                    <div class="hero-card-stats">
                        <div class="hero-card-stat">
                            <div class="hero-card-stat-value stat-green">28</div>
                            <div class="hero-card-stat-label">Hadir</div>
                        </div>
                        <div class="hero-card-stat">
                            <div class="hero-card-stat-value stat-yellow">3</div>
                            <div class="hero-card-stat-label">Izin</div>
                        </div>
                        <div class="hero-card-stat">
                            <div class="hero-card-stat-value stat-red">1</div>
                            <div class="hero-card-stat-label">Alfa</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="features-inner">
            <div class="section-label">Fitur Utama</div>
            <h2 class="section-title">Semua yang Anda Butuhkan</h2>
            <p class="section-description">
                Sistem absensi lengkap yang dirancang untuk kemudahan operasional sekolah sehari-hari.
            </p>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon feature-icon--teal">📡</div>
                    <h3 class="feature-title">Tap NFC Instan</h3>
                    <p class="feature-description">Siswa cukup menempelkan kartu NFC atau handphone ke reader. Kehadiran langsung tercatat dalam hitungan detik.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon feature-icon--sky">📊</div>
                    <h3 class="feature-title">Dashboard Real-time</h3>
                    <p class="feature-description">Pantau kehadiran secara langsung melalui dashboard admin. Grafik statistik dan tabel data yang informatif.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon feature-icon--amber">🖥️</div>
                    <h3 class="feature-title">Mode Kiosk</h3>
                    <p class="feature-description">Tampilkan layar kiosk di dekat pintu masuk. Nama dan foto siswa muncul otomatis saat kartu di-tap.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon feature-icon--rose">👥</div>
                    <h3 class="feature-title">Multi-Role</h3>
                    <p class="feature-description">Tiga level akses: Admin TU (superadmin), Guru (wali kelas), dan Siswa. Masing-masing dengan fitur sesuai kebutuhan.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon feature-icon--violet">📄</div>
                    <h3 class="feature-title">Export Laporan</h3>
                    <p class="feature-description">Export data absensi ke format CSV kapan saja. Filter berdasarkan tanggal, kelas, atau status kehadiran.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon feature-icon--emerald">🔗</div>
                    <h3 class="feature-title">API untuk Hardware</h3>
                    <p class="feature-description">Endpoint API khusus untuk perangkat ESP32 atau komputer pembaca NFC. Integrasi hardware yang mudah.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Roles Section -->
    <section class="roles" id="roles">
        <div class="roles-inner">
            <div class="section-label">Akses Pengguna</div>
            <h2 class="section-title">Tiga Role, Satu Sistem</h2>
            <p class="section-description">
                Setiap pengguna mendapat akses sesuai perannya di lingkungan sekolah.
            </p>
            <div class="roles-grid">
                <div class="role-card">
                    <div class="role-icon role-icon--admin">🛡️</div>
                    <h3 class="role-title">Admin TU</h3>
                    <p class="role-description">Superadmin dengan akses penuh ke seluruh sistem.</p>
                    <ul class="role-features">
                        <li>Kelola data siswa, guru, dan kelas</li>
                        <li>Daftarkan & mapping kartu NFC</li>
                        <li>Lihat & export laporan absensi</li>
                        <li>Akses mode kiosk</li>
                    </ul>
                </div>
                <div class="role-card">
                    <div class="role-icon role-icon--guru">📚</div>
                    <h3 class="role-title">Guru / Wali Kelas</h3>
                    <p class="role-description">Pemantau kehadiran siswa di kelas yang diampu.</p>
                    <ul class="role-features">
                        <li>Monitor absensi kelas wali</li>
                        <li>Ubah status (alfa → sakit/izin)</li>
                        <li>Lihat grafik rekap kehadiran</li>
                        <li>Akses mode kiosk</li>
                    </ul>
                </div>
                <div class="role-card">
                    <div class="role-icon role-icon--siswa">🎓</div>
                    <h3 class="role-title">Siswa</h3>
                    <p class="role-description">Portal pribadi untuk melihat riwayat kehadiran.</p>
                    <ul class="role-features">
                        <li>Dashboard statistik kehadiran</li>
                        <li>Riwayat absensi lengkap</li>
                        <li>Grafik distribusi kehadiran</li>
                        <li>Persentase kehadiran</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="cta-inner">
            <h2 class="cta-title">Siap Memulai?</h2>
            <p class="cta-description">
                Masuk ke dashboard untuk mulai mengelola absensi sekolah Anda dengan cara modern dan efisien.
            </p>
            <div class="cta-actions">
                @auth
                    @if(auth()->user()->isSiswa())
                        <a href="/student" class="btn btn--white">Buka Dashboard →</a>
                    @else
                        <a href="/admin" class="btn btn--white">Buka Dashboard →</a>
                    @endif
                @else
                    <a href="/admin/login" class="btn btn--white">Masuk ke Sistem →</a>
                @endauth
                <a href="#features" class="btn btn--outline">Lihat Fitur</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>© {{ date('Y') }} Sistem Informasi Absensi Sekolah Berbasis NFC — Dibangun dengan <a href="https://laravel.com" target="_blank">Laravel</a> + <a href="https://filamentphp.com" target="_blank">Filament</a></p>
    </footer>

    <script>
        // Navbar scroll effect
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 40);
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>
</body>
</html>
