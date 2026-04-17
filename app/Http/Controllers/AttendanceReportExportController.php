<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceReportExportController extends Controller
{
    public function __invoke(Request $request): StreamedResponse
    {
        $attendances = Attendance::query()
            ->with([
                'student.studentProfile.classroom',
                'approvedBy',
            ])
            ->when(
                $request->filled('start_date'),
                fn ($query) => $query->whereDate('attendance_date', '>=', $request->date('start_date'))
            )
            ->when(
                $request->filled('end_date'),
                fn ($query) => $query->whereDate('attendance_date', '<=', $request->date('end_date'))
            )
            ->orderByDesc('attendance_date')
            ->get();

        $fileName = 'laporan_absensi_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($attendances) {
            $output = fopen('php://output', 'wb');

            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, [
                'Tanggal',
                'Nama Siswa',
                'Kelas',
                'Status',
                'Jam Tap',
                'Sumber',
                'Disetujui Oleh',
                'Catatan',
            ]);

            foreach ($attendances as $attendance) {
                fputcsv($output, [
                    optional($attendance->attendance_date)->format('Y-m-d'),
                    $attendance->student?->name,
                    $attendance->student?->studentProfile?->classroom?->name,
                    $attendance->status,
                    optional($attendance->check_in_at)->format('H:i:s'),
                    $attendance->source,
                    $attendance->approvedBy?->name,
                    $attendance->note,
                ]);
            }

            fclose($output);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
