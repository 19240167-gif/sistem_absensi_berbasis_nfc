<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Demo Login</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap');

        :root {
            --ink: #0f172a;
            --muted: #5b6470;
            --card: #ffffff;
            --accent: #0ea5a4;
            --accent-strong: #0f766e;
            --accent-warm: #f97316;
            --bg: #f7f1e8;
            --shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
            --border: rgba(15, 23, 42, 0.08);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Space Grotesk', 'Segoe UI', Arial, sans-serif;
            color: var(--ink);
            background:
                radial-gradient(1200px 600px at 10% -10%, #fff3e0 0%, transparent 60%),
                radial-gradient(900px 500px at 95% 10%, #e0f2f1 0%, transparent 60%),
                linear-gradient(120deg, #f9fafb 0%, #f7efe4 60%, #f4f1ec 100%);
        }

        body::before,
        body::after {
            content: '';
            position: fixed;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            background: rgba(14, 165, 164, 0.12);
            filter: blur(0px);
            z-index: -1;
        }

        body::before { top: -120px; left: -140px; }
        body::after { bottom: -140px; right: -120px; background: rgba(249, 115, 22, 0.14); }

        .page {
            max-width: 1100px;
            margin: 0 auto;
            padding: 56px 24px 72px;
        }

        .hero {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 24px;
            align-items: end;
            margin-bottom: 28px;
        }

        .eyebrow {
            text-transform: uppercase;
            letter-spacing: 0.18em;
            font-size: 12px;
            font-weight: 600;
            color: var(--accent-strong);
        }

        h1 {
            font-family: 'Fraunces', Georgia, serif;
            font-size: clamp(32px, 4vw, 44px);
            margin: 12px 0 12px;
        }

        .hero p {
            color: var(--muted);
            font-size: 16px;
            max-width: 520px;
            line-height: 1.6;
            margin: 0 0 20px;
        }

        .notice {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(14, 165, 164, 0.12);
            color: var(--accent-strong);
            font-weight: 600;
            font-size: 13px;
        }

        .notice .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent);
        }

        .hero-panel {
            background: rgba(255, 255, 255, 0.75);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 20px;
            box-shadow: var(--shadow);
            backdrop-filter: blur(6px);
        }

        .panel-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .panel-item {
            background: white;
            border-radius: 14px;
            padding: 12px 14px;
            border: 1px solid var(--border);
        }

        .panel-label {
            color: var(--muted);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .panel-value {
            font-size: 20px;
            font-weight: 700;
            margin-top: 6px;
        }

        .panel-note {
            margin-top: 14px;
            font-size: 13px;
            color: var(--muted);
        }

        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 16px 0 24px;
        }

        .chip {
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.9);
            padding: 8px 14px;
            border-radius: 999px;
            font-weight: 600;
            color: var(--muted);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .chip.is-active,
        .chip:hover {
            background: var(--ink);
            color: #ffffff;
            border-color: transparent;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
        }

        .card {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 14px;
            align-items: center;
            padding: 16px;
            border-radius: 18px;
            background: var(--card);
            border: 1px solid var(--border);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            border-color: rgba(14, 165, 164, 0.5);
            box-shadow: 0 14px 28px rgba(15, 23, 42, 0.14);
        }

        .avatar {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            font-weight: 700;
            font-size: 20px;
            color: #ffffff;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-warm) 100%);
        }

        .meta {
            min-width: 0;
        }

        .name {
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .email {
            font-size: 13px;
            color: var(--muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .badge-row {
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .badge {
            font-size: 12px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 999px;
            background: #f1f5f9;
            color: #1f2937;
        }

        .badge--admin_tu { background: #fff7ed; color: #c2410c; }
        .badge--guru { background: #ecfeff; color: #0e7490; }
        .badge--siswa { background: #eff6ff; color: #1d4ed8; }
        .badge--other { background: #f1f5f9; color: #1f2937; }

        .btn {
            border: none;
            background: var(--accent);
            color: #ffffff;
            padding: 10px 16px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
            box-shadow: 0 10px 18px rgba(14, 165, 164, 0.3);
        }

        .btn:hover {
            background: var(--accent-strong);
            transform: translateY(-1px);
        }

        .empty {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 18px;
            padding: 24px;
            text-align: center;
            color: var(--muted);
            border: 1px dashed var(--border);
        }

        .foot {
            margin-top: 28px;
            font-size: 13px;
            color: var(--muted);
        }

        @media (max-width: 900px) {
            .hero { grid-template-columns: 1fr; }
            .card { grid-template-columns: auto 1fr; }
            .btn { grid-column: 1 / -1; width: 100%; }
        }

        @media (prefers-reduced-motion: reduce) {
            * { transition: none !important; }
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
                    mempercepat pengujian fitur untuk pengelola sekolah.
                </p>
                <div class="notice"><span class="dot"></span>Mode demo aktif</div>
            </div>
            <div class="hero-panel">
                <div class="panel-grid">
                    <div class="panel-item">
                        <div class="panel-label">Total akun</div>
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
            @forelse($users as $user)
                @php
                    $roleSlug = $user->role?->slug ?? 'other';
                    $roleName = $user->role?->name ?? 'Tanpa role';
                @endphp
                <form method="POST" action="{{ route('demo.login.post', $user->id) }}" class="card" data-role="{{ $roleSlug }}">
                    @csrf
                    <div class="avatar">{{ strtoupper(substr($user->name, 0, 1) ?: 'U') }}</div>
                    <div class="meta">
                        <div class="name">{{ $user->name }}</div>
                        <div class="email">{{ $user->email }}</div>
                        <div class="badge-row">
                            <span class="badge badge--{{ $roleSlug }}">{{ $roleName }}</span>
                        </div>
                    </div>
                    <button class="btn" type="submit">Masuk</button>
                </form>
            @empty
                <div class="empty">Belum ada akun demo. Jalankan seeder untuk menambahkan akun.</div>
            @endforelse
        </section>

        <div class="foot">Tip: Setelah logout, Anda akan kembali ke halaman demo login.</div>
    </main>

    <script>
        (() => {
            const chips = Array.from(document.querySelectorAll('[data-role-filter]'));
            const cards = Array.from(document.querySelectorAll('[data-role]'));

            const isMatch = (filter, role) => {
                if (filter === 'all') {
                    return true;
                }
                if (filter === 'staff') {
                    return role === 'admin_tu' || role === 'guru';
                }
                return role === filter;
            };

            const setActive = (filter) => {
                chips.forEach((chip) => {
                    chip.classList.toggle('is-active', chip.dataset.roleFilter === filter);
                });
                cards.forEach((card) => {
                    const show = isMatch(filter, card.dataset.role);
                    card.style.display = show ? '' : 'none';
                });
            };

            const active = chips.find((chip) => chip.classList.contains('is-active'))?.dataset.roleFilter || 'all';
            setActive(active);
            chips.forEach((chip) => chip.addEventListener('click', () => setActive(chip.dataset.roleFilter)));
        })();
    </script>
</body>
</html>
