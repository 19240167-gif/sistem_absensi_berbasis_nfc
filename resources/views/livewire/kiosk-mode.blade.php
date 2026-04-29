<div wire:poll.2s="refreshData" class="kiosk-container">
    {{-- Inline styles for standalone kiosk page --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap');

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        .kiosk-container {
            min-height: 100vh;
            font-family: 'Inter', system-ui, sans-serif;
            color: #e2e8f0;
            background: linear-gradient(135deg, #0c1222 0%, #0f172a 40%, #1a1c2e 100%);
            position: relative;
            overflow: hidden;
        }

        .kiosk-container::before {
            content: '';
            position: fixed;
            top: -200px;
            right: -200px;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(20, 184, 166, 0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        .kiosk-container::after {
            content: '';
            position: fixed;
            bottom: -150px;
            left: -150px;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.06) 0%, transparent 70%);
            pointer-events: none;
        }

        .kiosk-layout {
            display: grid;
            grid-template-columns: 1fr 360px;
            min-height: 100vh;
            gap: 0;
        }

        /* ─── Header Bar ─── */
        .kiosk-header {
            grid-column: 1 / -1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 32px;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(148, 163, 184, 0.08);
        }

        .kiosk-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .kiosk-brand-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #14b8a6, #06b6d4);
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 20px;
            box-shadow: 0 4px 16px rgba(20, 184, 166, 0.3);
        }

        .kiosk-brand h1 {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, #e2e8f0, #94a3b8);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .kiosk-brand small {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 600;
        }

        .kiosk-clock {
            text-align: right;
        }

        .kiosk-clock-time {
            font-family: 'JetBrains Mono', monospace;
            font-size: 36px;
            font-weight: 700;
            letter-spacing: 0.04em;
            background: linear-gradient(135deg, #14b8a6, #38bdf8);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
        }

        .kiosk-clock-date {
            font-size: 13px;
            color: #64748b;
            margin-top: 4px;
            font-weight: 500;
        }

        /* ─── Main Area ─── */
        .kiosk-main {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
        }

        /* ─── Stats Bar ─── */
        .kiosk-stats {
            display: flex;
            gap: 16px;
            margin-bottom: 40px;
            width: 100%;
            max-width: 640px;
        }

        .kiosk-stat-card {
            flex: 1;
            padding: 16px;
            border-radius: 16px;
            background: rgba(30, 41, 59, 0.6);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(148, 163, 184, 0.08);
            text-align: center;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .kiosk-stat-card:hover { transform: translateY(-2px); }

        .kiosk-stat-value {
            font-size: 28px;
            font-weight: 800;
            line-height: 1;
        }

        .kiosk-stat-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #64748b;
            margin-top: 6px;
            font-weight: 600;
        }

        .stat-hadir .kiosk-stat-value { color: #34d399; }
        .stat-hadir { border-color: rgba(52, 211, 153, 0.15); }
        .stat-izin .kiosk-stat-value { color: #fbbf24; }
        .stat-izin { border-color: rgba(251, 191, 36, 0.15); }
        .stat-sakit .kiosk-stat-value { color: #60a5fa; }
        .stat-sakit { border-color: rgba(96, 165, 250, 0.15); }
        .stat-alfa .kiosk-stat-value { color: #f87171; }
        .stat-alfa { border-color: rgba(248, 113, 113, 0.15); }

        /* ─── Waiting State ─── */
        .kiosk-waiting {
            text-align: center;
            padding: 64px 32px;
        }

        .kiosk-waiting-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 32px;
            border-radius: 50%;
            background: rgba(20, 184, 166, 0.08);
            border: 2px dashed rgba(20, 184, 166, 0.25);
            display: grid;
            place-items: center;
            font-size: 48px;
            animation: kioskPulse 2.5s ease-in-out infinite;
            position: relative;
        }

        .kiosk-waiting-icon::before {
            content: '';
            position: absolute;
            inset: -12px;
            border-radius: 50%;
            border: 1px solid rgba(20, 184, 166, 0.1);
            animation: kioskRing 2.5s ease-in-out infinite;
        }

        @keyframes kioskPulse {
            0%, 100% { transform: scale(1); border-color: rgba(20, 184, 166, 0.25); }
            50% { transform: scale(1.06); border-color: rgba(20, 184, 166, 0.5); }
        }

        @keyframes kioskRing {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.12); opacity: 0; }
        }

        .kiosk-waiting h2 {
            font-size: 26px;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 10px;
        }

        .kiosk-waiting p {
            color: #64748b;
            font-size: 15px;
            max-width: 400px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* ─── Student Card (Scan Success) ─── */
        .kiosk-student-card {
            width: 100%;
            max-width: 540px;
            background: rgba(30, 41, 59, 0.5);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(20, 184, 166, 0.2);
            border-radius: 28px;
            padding: 40px;
            box-shadow:
                0 0 60px rgba(20, 184, 166, 0.1),
                0 24px 48px rgba(0, 0, 0, 0.3);
            animation: kioskSlideIn 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes kioskSlideIn {
            from { opacity: 0; transform: translateY(24px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .kiosk-student-success {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 24px;
        }

        .kiosk-student-success.status-hadir {
            background: rgba(52, 211, 153, 0.12);
            color: #34d399;
            border: 1px solid rgba(52, 211, 153, 0.2);
        }

        .kiosk-student-success.status-izin,
        .kiosk-student-success.status-sakit {
            background: rgba(251, 191, 36, 0.12);
            color: #fbbf24;
            border: 1px solid rgba(251, 191, 36, 0.2);
        }

        .kiosk-student-success.status-alfa {
            background: rgba(248, 113, 113, 0.12);
            color: #f87171;
            border: 1px solid rgba(248, 113, 113, 0.2);
        }

        .kiosk-student-success-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: currentColor;
            animation: kioskDot 1.2s ease-in-out infinite;
        }

        @keyframes kioskDot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        .kiosk-student-profile {
            display: flex;
            align-items: center;
            gap: 28px;
        }

        .kiosk-student-photo {
            width: 140px;
            height: 140px;
            border-radius: 24px;
            overflow: hidden;
            background: rgba(51, 65, 85, 0.6);
            border: 3px solid rgba(20, 184, 166, 0.3);
            box-shadow:
                0 0 30px rgba(20, 184, 166, 0.15),
                0 8px 24px rgba(0, 0, 0, 0.3);
            flex-shrink: 0;
        }

        .kiosk-student-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .kiosk-student-photo-placeholder {
            width: 100%;
            height: 100%;
            display: grid;
            place-items: center;
            color: #475569;
            font-size: 48px;
        }

        .kiosk-student-info h2 {
            font-size: 30px;
            font-weight: 800;
            letter-spacing: -0.02em;
            line-height: 1.2;
            margin-bottom: 16px;
            background: linear-gradient(135deg, #f1f5f9, #cbd5e1);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .kiosk-student-meta {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .kiosk-student-meta-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 15px;
            color: #94a3b8;
        }

        .kiosk-student-meta-item .meta-icon {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: rgba(30, 41, 59, 0.8);
            display: grid;
            place-items: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .kiosk-student-meta-item span {
            font-weight: 600;
            color: #e2e8f0;
        }

        /* ─── Sidebar ─── */
        .kiosk-sidebar {
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(12px);
            border-left: 1px solid rgba(148, 163, 184, 0.08);
            padding: 28px 24px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .kiosk-sidebar-title {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.08);
        }

        .kiosk-timeline {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .kiosk-timeline-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 14px;
            transition: background 0.2s ease;
            position: relative;
        }

        .kiosk-timeline-item:first-child {
            background: rgba(20, 184, 166, 0.06);
            border: 1px solid rgba(20, 184, 166, 0.1);
        }

        .kiosk-timeline-item:not(:first-child) {
            border: 1px solid transparent;
        }

        .kiosk-timeline-item:hover {
            background: rgba(30, 41, 59, 0.5);
        }

        .kiosk-timeline-avatar {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, #1e293b, #334155);
            display: grid;
            place-items: center;
            font-weight: 700;
            font-size: 14px;
            color: #94a3b8;
            flex-shrink: 0;
            overflow: hidden;
        }

        .kiosk-timeline-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .kiosk-timeline-meta {
            flex: 1;
            min-width: 0;
        }

        .kiosk-timeline-name {
            font-size: 14px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #e2e8f0;
        }

        .kiosk-timeline-detail {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }

        .kiosk-timeline-time {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: #475569;
            font-weight: 500;
            flex-shrink: 0;
        }

        .kiosk-status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .kiosk-status-dot.dot-hadir { background: #34d399; box-shadow: 0 0 8px rgba(52, 211, 153, 0.4); }
        .kiosk-status-dot.dot-izin { background: #fbbf24; box-shadow: 0 0 8px rgba(251, 191, 36, 0.4); }
        .kiosk-status-dot.dot-sakit { background: #60a5fa; box-shadow: 0 0 8px rgba(96, 165, 250, 0.4); }
        .kiosk-status-dot.dot-alfa { background: #f87171; box-shadow: 0 0 8px rgba(248, 113, 113, 0.4); }

        .kiosk-timeline-empty {
            text-align: center;
            padding: 32px 16px;
            color: #475569;
            font-size: 14px;
        }

        .kiosk-sidebar-footer {
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid rgba(148, 163, 184, 0.08);
            font-size: 12px;
            color: #475569;
            text-align: center;
        }

        /* ─── Responsive ─── */
        @media (max-width: 900px) {
            .kiosk-layout {
                grid-template-columns: 1fr;
            }

            .kiosk-sidebar {
                border-left: none;
                border-top: 1px solid rgba(148, 163, 184, 0.08);
                max-height: 300px;
            }

            .kiosk-student-profile {
                flex-direction: column;
                text-align: center;
            }

            .kiosk-student-meta {
                align-items: center;
            }

            .kiosk-stats {
                flex-wrap: wrap;
            }

            .kiosk-stat-card {
                min-width: calc(50% - 8px);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.001ms !important;
                transition-duration: 0.001ms !important;
            }
        }
    </style>

    <div class="kiosk-layout">
        {{-- Header --}}
        <header class="kiosk-header">
            <div class="kiosk-brand">
                <div class="kiosk-brand-icon">📡</div>
                <div>
                    <h1>Mode Kiosk Absensi</h1>
                    <small>Sistem Informasi Absensi NFC Sekolah</small>
                </div>
            </div>
            <div class="kiosk-clock">
                <div class="kiosk-clock-time">{{ $currentTime }}</div>
                <div class="kiosk-clock-date">{{ $currentDate }}</div>
            </div>
        </header>

        {{-- Main Area --}}
        <main class="kiosk-main">
            {{-- Today Stats --}}
            <div class="kiosk-stats">
                <div class="kiosk-stat-card stat-hadir">
                    <div class="kiosk-stat-value">{{ $todayStats['hadir'] }}</div>
                    <div class="kiosk-stat-label">Hadir</div>
                </div>
                <div class="kiosk-stat-card stat-izin">
                    <div class="kiosk-stat-value">{{ $todayStats['izin'] }}</div>
                    <div class="kiosk-stat-label">Izin</div>
                </div>
                <div class="kiosk-stat-card stat-sakit">
                    <div class="kiosk-stat-value">{{ $todayStats['sakit'] }}</div>
                    <div class="kiosk-stat-label">Sakit</div>
                </div>
                <div class="kiosk-stat-card stat-alfa">
                    <div class="kiosk-stat-value">{{ $todayStats['alfa'] }}</div>
                    <div class="kiosk-stat-label">Alfa</div>
                </div>
            </div>

            @if (!empty($latestScan['student_name']))
                {{-- Student Card --}}
                <div class="kiosk-student-card" wire:key="scan-{{ $latestScan['scanned_at'] ?? 'none' }}">
                    <div class="kiosk-student-success status-{{ $latestScan['status'] ?? 'hadir' }}">
                        <span class="kiosk-student-success-dot"></span>
                        Tap Berhasil — {{ ucfirst($latestScan['status'] ?? 'hadir') }}
                    </div>

                    <div class="kiosk-student-profile">
                        <div class="kiosk-student-photo">
                            @if (!empty($latestScan['photo_url']))
                                <img src="{{ $latestScan['photo_url'] }}" alt="Foto {{ $latestScan['student_name'] }}">
                            @else
                                <div class="kiosk-student-photo-placeholder">
                                    {{ strtoupper(mb_substr($latestScan['student_name'], 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        <div class="kiosk-student-info">
                            <h2>{{ $latestScan['student_name'] }}</h2>

                            <div class="kiosk-student-meta">
                                <div class="kiosk-student-meta-item">
                                    <div class="meta-icon">🎓</div>
                                    Kelas: <span>{{ $latestScan['classroom'] ?? '-' }}</span>
                                </div>
                                <div class="kiosk-student-meta-item">
                                    <div class="meta-icon">🕐</div>
                                    Jam Tap: <span>{{ $latestScan['check_in_at'] ?? '-' }}</span>
                                </div>
                                <div class="kiosk-student-meta-item">
                                    <div class="meta-icon">📋</div>
                                    Status: <span>{{ ucfirst($latestScan['status'] ?? '-') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- Waiting State --}}
                <div class="kiosk-waiting">
                    <div class="kiosk-waiting-icon">📱</div>
                    <h2>Menunggu Tap NFC...</h2>
                    <p>Tempelkan kartu NFC atau handphone siswa ke perangkat pembaca untuk mencatat kehadiran secara otomatis.</p>
                </div>
            @endif
        </main>

        {{-- Sidebar: Recent Scans --}}
        <aside class="kiosk-sidebar">
            <div class="kiosk-sidebar-title">📋 Riwayat Scan Terakhir</div>

            <div class="kiosk-timeline">
                @forelse ($recentScans as $index => $scan)
                    <div class="kiosk-timeline-item">
                        <div class="kiosk-timeline-avatar">
                            @if (!empty($scan['photo_url']))
                                <img src="{{ $scan['photo_url'] }}" alt="">
                            @else
                                {{ strtoupper(mb_substr($scan['student_name'] ?? 'U', 0, 1)) }}
                            @endif
                        </div>
                        <div class="kiosk-timeline-meta">
                            <div class="kiosk-timeline-name">{{ $scan['student_name'] ?? '-' }}</div>
                            <div class="kiosk-timeline-detail">{{ $scan['classroom'] ?? '-' }}</div>
                        </div>
                        <span class="kiosk-status-dot dot-{{ $scan['status'] ?? 'hadir' }}"></span>
                        <div class="kiosk-timeline-time">{{ $scan['check_in_at'] ?? '-' }}</div>
                    </div>
                @empty
                    <div class="kiosk-timeline-empty">
                        Belum ada scan hari ini.
                    </div>
                @endforelse
            </div>

            <div class="kiosk-sidebar-footer">
                Total hari ini: <strong>{{ $todayStats['total'] }}</strong> scan
            </div>
        </aside>
    </div>
</div>
