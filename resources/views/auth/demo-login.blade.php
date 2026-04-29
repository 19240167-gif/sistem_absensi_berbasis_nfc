<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Demo Login — Absensi NFC Sekolah</title>
    <meta name="description" content="Halaman demo login untuk Sistem Informasi Absensi Sekolah berbasis NFC">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        :root {
            --ink: #0f172a;
            --muted: #5b6470;
            --card: #ffffff;
            --accent: #14b8a6;
            --accent-dark: #0d9488;
            --accent-glow: rgba(20, 184, 166, 0.15);
            --sky: #38bdf8;
            --warm: #f97316;
            --bg-start: #f0fdfa;
            --bg-end: #f8fafc;
            --shadow: 0 16px 40px rgba(15, 23, 42, 0.1);
            --border: rgba(15, 23, 42, 0.06);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Inter', system-ui, sans-serif;
            color: var(--ink);
            background:
                radial-gradient(1200px 600px at 10% -10%, rgba(20, 184, 166, 0.08) 0%, transparent 60%),
                radial-gradient(900px 500px at 95% 10%, rgba(56, 189, 248, 0.06) 0%, transparent 60%),
                linear-gradient(160deg, var(--bg-start) 0%, var(--bg-end) 100%);
        }

        body::before,
        body::after {
            content: '';
            position: fixed;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            z-index: -1;
            pointer-events: none;
        }

        body::before {
            top: -120px;
            left: -140px;
            background: var(--accent-glow);
            filter: blur(2px);
        }

        body::after {
            bottom: -140px;
            right: -120px;
            background: rgba(56, 189, 248, 0.08);
            filter: blur(2px);
        }

        .page {
            max-width: 1120px;
            margin: 0 auto;
            padding: 48px 24px 80px;
        }

        /* Hero */
        .hero {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 28px;
            align-items: end;
            margin-bottom: 24px;
            animation: fadeInUp 0.6s ease both;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.18em;
            font-size: 12px;
            font-weight: 700;
            color: var(--accent-dark);
        }

        .eyebrow::before {
            content: '';
            width: 24px;
            height: 2px;
            background: var(--accent);
            border-radius: 2px;
        }

        h1 {
            font-size: clamp(30px, 4vw, 42px);
            font-weight: 800;
            letter-spacing: -0.03em;
            margin: 14px 0 14px;
            background: linear-gradient(135deg, var(--ink) 0%, #334155 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            color: var(--muted);
            font-size: 15px;
            max-width: 480px;
            line-height: 1.7;
            margin: 0 0 20px;
        }

        .notice {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 999px;
            background: rgba(20, 184, 166, 0.1);
            border: 1px solid rgba(20, 184, 166, 0.15);
            color: var(--accent-dark);
            font-weight: 600;
            font-size: 13px;
        }

        .notice .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent);
            animation: noticePulse 1.5s ease-in-out infinite;
        }

        @keyframes noticePulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.3); }
        }

        /* Hero panel */
        .hero-panel {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid var(--border);
            border-radius: 22px;
            padding: 22px;
            box-shadow: var(--shadow);
            backdrop-filter: blur(8px);
            animation: fadeInUp 0.6s 0.15s ease both;
        }

        .panel-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
        }

        .panel-item {
            background: white;
            border-radius: 14px;
            padding: 14px 16px;
            border: 1px solid var(--border);
            transition: transform 0.18s ease;
        }

        .panel-item:hover { transform: translateY(-1px); }

        .panel-label {
            color: var(--muted);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 600;
        }

        .panel-value {
            font-size: 24px;
            font-weight: 800;
            margin-top: 4px;
            color: var(--ink);
        }

        .panel-note {
            margin-top: 14px;
            font-size: 12px;
            color: var(--muted);
            line-height: 1.5;
        }

        /* Filters */
        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 16px 0 24px;
            animation: fadeInUp 0.6s 0.25s ease both;
        }

        .chip {
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.9);
            padding: 9px 16px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 13px;
            color: var(--muted);
            cursor: pointer;
            transition: all 0.25s ease;
            font-family: inherit;
        }

        .chip.is-active,
        .chip:hover {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 4px 14px rgba(20, 184, 166, 0.3);
        }

        /* Grid */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 14px;
        }

        /* Card */
        .card {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 14px;
            align-items: center;
            padding: 16px 18px;
            border-radius: 18px;
            background: var(--card);
            border: 1px solid var(--border);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
            animation: cardEntrance 0.5s ease both;
        }

        @keyframes cardEntrance {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card:hover {
            transform: translateY(-3px);
            border-color: rgba(20, 184, 166, 0.35);
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
        }

        .avatar {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-weight: 800;
            font-size: 19px;
            color: #ffffff;
        }

        .avatar--admin_tu {
            background: linear-gradient(135deg, #f97316, #ea580c);
        }

        .avatar--guru {
            background: linear-gradient(135deg, #14b8a6, #0d9488);
        }

        .avatar--siswa {
            background: linear-gradient(135deg, #38bdf8, #0284c7);
        }

        .avatar--other {
            background: linear-gradient(135deg, #94a3b8, #64748b);
        }

        .meta { min-width: 0; }

        .name {
            font-weight: 700;
            font-size: 15px;
            margin-bottom: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .email {
            font-size: 12px;
            color: var(--muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .badge-row {
            margin-top: 8px;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .badge {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .badge--admin_tu { background: #fff7ed; color: #c2410c; }
        .badge--guru { background: #f0fdfa; color: #0d9488; }
        .badge--siswa { background: #eff6ff; color: #1d4ed8; }
        .badge--other { background: #f1f5f9; color: #475569; }

        .btn {
            border: none;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #ffffff;
            padding: 10px 18px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 6px 16px rgba(20, 184, 166, 0.3);
            font-family: inherit;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(20, 184, 166, 0.4);
        }

        .btn:active {
            transform: translateY(0);
        }

        .empty {
            background: rgba(255, 255, 255, 0.85);
            border-radius: 18px;
            padding: 28px;
            text-align: center;
            color: var(--muted);
            border: 1px dashed var(--border);
            grid-column: 1 / -1;
        }

        .foot {
            margin-top: 32px;
            font-size: 12px;
            color: var(--muted);
            text-align: center;
            animation: fadeInUp 0.6s 0.5s ease both;
        }

        @media (max-width: 900px) {
            .hero { grid-template-columns: 1fr; }
            .card { grid-template-columns: auto 1fr; }
            .btn { grid-column: 1 / -1; width: 100%; text-align: center; }
        }

        @media (prefers-reduced-motion: reduce) {
            * { animation: none !important; transition: none !important; }
        }
    </style>
</head>
<body>
    @php
        $roleCounts = [
            'admin_tu' => $users->filter(fn ($user) => $user->role?->slug === 'admin_tu')->count(),
            'guru' => $users->filter(fn ($user) => $user->role?->slug === 'guru')->count(),
            'siswa' => $users->filter(fn ($user) => $user->role?->slug === 'siswa')->count(),
        ];
    @endphp
    <main class="page">
        <header class="hero">
            <div>
                <span class="eyebrow">Absensi NFC Sekolah</span>
                <h1>Demo Login</h1>
                <p>
                    Pilih akun untuk masuk tanpa password. Halaman ini disiapkan untuk demo dan
                    mempercepat pengujian fitur sistem absensi sekolah.
                </p>
                <div class="notice"><span class="dot"></span>Mode demo aktif</div>
            </div>
            <div class="hero-panel">
                <div class="panel-grid">
                    <div class="panel-item">
                        <div class="panel-label">Total Akun</div>
                        <div class="panel-value">{{ $users->count() }}</div>
                    </div>
                    <div class="panel-item">
                        <div class="panel-label">Pengelola</div>
                        <div class="panel-value">{{ $roleCounts['admin_tu'] + $roleCounts['guru'] }}</div>
                    </div>
                    <div class="panel-item">
                        <div class="panel-label">Admin TU</div>
                        <div class="panel-value">{{ $roleCounts['admin_tu'] }}</div>
                    </div>
                    <div class="panel-item">
                        <div class="panel-label">Guru</div>
                        <div class="panel-value">{{ $roleCounts['guru'] }}</div>
                    </div>
                </div>
                <div class="panel-note">Gunakan filter "Pengelola" untuk fokus ke admin TU dan guru.</div>
            </div>
        </header>

        <section class="filters" aria-label="Filter role">
            <button class="chip is-active" type="button" data-role-filter="staff">Pengelola</button>
            <button class="chip" type="button" data-role-filter="all">Semua</button>
            <button class="chip" type="button" data-role-filter="admin_tu">Admin TU</button>
            <button class="chip" type="button" data-role-filter="guru">Guru</button>
            <button class="chip" type="button" data-role-filter="siswa">Siswa</button>
        </section>

        <section class="grid">
            @forelse($users as $i => $user)
                @php
                    $roleSlug = $user->role?->slug ?? 'other';
                    $roleName = $user->role?->name ?? 'Tanpa role';
                @endphp
                <form method="POST" action="{{ route('demo.login.post', $user->id) }}" class="card" data-role="{{ $roleSlug }}" style="animation-delay: {{ $i * 60 }}ms">
                    @csrf
                    <div class="avatar avatar--{{ $roleSlug }}">{{ strtoupper(substr($user->name, 0, 1) ?: 'U') }}</div>
                    <div class="meta">
                        <div class="name">{{ $user->name }}</div>
                        <div class="email">{{ $user->email }}</div>
                        <div class="badge-row">
                            <span class="badge badge--{{ $roleSlug }}">{{ $roleName }}</span>
                        </div>
                    </div>
                    <button class="btn" type="submit">Masuk →</button>
                </form>
            @empty
                <div class="empty">Belum ada akun demo. Jalankan seeder untuk menambahkan akun.</div>
            @endforelse
        </section>

        <div class="foot">Tip: Setelah logout, Anda akan kembali ke halaman demo login ini.</div>
    </main>

    <script>
        (() => {
            const chips = Array.from(document.querySelectorAll('[data-role-filter]'));
            const cards = Array.from(document.querySelectorAll('[data-role]'));

            const isMatch = (filter, role) => {
                if (filter === 'all') return true;
                if (filter === 'staff') return role === 'admin_tu' || role === 'guru';
                return role === filter;
            };

            const setActive = (filter) => {
                chips.forEach(chip => chip.classList.toggle('is-active', chip.dataset.roleFilter === filter));
                cards.forEach((card, i) => {
                    const show = isMatch(filter, card.dataset.role);
                    card.style.display = show ? '' : 'none';
                    if (show) {
                        card.style.animationDelay = `${i * 40}ms`;
                        card.style.animation = 'none';
                        card.offsetHeight; // trigger reflow
                        card.style.animation = '';
                    }
                });
            };

            const active = chips.find(chip => chip.classList.contains('is-active'))?.dataset.roleFilter || 'all';
            setActive(active);
            chips.forEach(chip => chip.addEventListener('click', () => setActive(chip.dataset.roleFilter)));
        })();
    </script>
</body>
</html>
