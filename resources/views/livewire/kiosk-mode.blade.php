<div wire:poll.2s="refreshLatestScan" class="min-h-screen bg-slate-950 text-slate-100">
    <div class="mx-auto flex min-h-screen w-full max-w-5xl items-center justify-center p-8">
        <div class="w-full rounded-3xl border border-amber-300/40 bg-slate-900/80 p-10 shadow-2xl">
            <div class="mb-8 text-center">
                <p class="text-sm uppercase tracking-[0.25em] text-amber-300">Sistem Informasi Absensi Sekolah</p>
                <h1 class="mt-3 text-4xl font-bold tracking-tight">Mode Kiosk NFC</h1>
                <p class="mt-3 text-slate-300">Tempelkan kartu NFC siswa ke perangkat pembaca untuk mencatat kehadiran.</p>
            </div>

            @if (!empty($latestScan['student_name']))
                <div class="grid gap-8 md:grid-cols-[220px_1fr] md:items-center">
                    <div class="mx-auto h-52 w-52 overflow-hidden rounded-2xl border border-slate-700 bg-slate-800">
                        @if (!empty($latestScan['photo_url']))
                            <img src="{{ $latestScan['photo_url'] }}" alt="Foto siswa" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full items-center justify-center text-slate-400">Tanpa Foto</div>
                        @endif
                    </div>

                    <div>
                        <p class="text-sm uppercase tracking-[0.2em] text-emerald-300">Tap berhasil</p>
                        <h2 class="mt-2 text-3xl font-semibold">{{ $latestScan['student_name'] }}</h2>
                        <div class="mt-5 space-y-2 text-lg text-slate-200">
                            <p>Kelas: <span class="font-semibold">{{ $latestScan['classroom'] ?? '-' }}</span></p>
                            <p>Status: <span class="font-semibold uppercase">{{ $latestScan['status'] ?? '-' }}</span></p>
                            <p>Jam Tap: <span class="font-semibold">{{ $latestScan['check_in_at'] ?? '-' }}</span></p>
                        </div>
                        <p class="mt-5 text-sm text-slate-400">Terakhir diperbarui: {{ $latestScan['scanned_at'] ?? '-' }}</p>
                    </div>
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-slate-700 bg-slate-900/70 p-10 text-center">
                    <p class="text-2xl font-semibold">Menunggu kartu NFC ditap...</p>
                    <p class="mt-3 text-slate-400">Informasi siswa akan muncul otomatis secara real-time.</p>
                </div>
            @endif
        </div>
    </div>
</div>
