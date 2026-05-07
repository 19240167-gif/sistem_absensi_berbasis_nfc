<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceReportExportController extends Controller
{
    public function __invoke(Request $request)
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
            ->when(
                $request->filled('attendance_date'),
                fn ($query) => $query->whereDate('attendance_date', $request->date('attendance_date'))
            )
            ->when(
                $request->filled('classroom'),
                fn ($query, string $classroom) => $query->whereHas('student.studentProfile.classroom', fn ($query) => $query->where('name', $classroom))
            )
            ->orderByDesc('attendance_date')
            ->get();

        if ($request->input('type') === 'pdf') {
            $classroom = $request->input('classroom', 'Semua Kelas');
            $attendanceDate = $request->input('attendance_date', today()->toDateString());
            $fileName = sprintf('absensi_kelas_%s_%s.pdf', str_replace(' ', '_', strtolower($classroom)), now()->format('Ymd_His'));

            $pdf = Pdf::loadView('reports.attendance_pdf', compact('attendances', 'classroom', 'attendanceDate'))
                ->setPaper('a4', 'portrait');

            return $pdf->download($fileName);
        }

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
                    $this->sanitizeCsv($attendance->student?->name),
                    $this->sanitizeCsv($attendance->student?->studentProfile?->classroom?->name),
                    $attendance->status,
                    optional($attendance->check_in_at)->format('H:i:s'),
                    $attendance->source,
                    $this->sanitizeCsv($attendance->approvedBy?->name),
                    $this->sanitizeCsv($attendance->note),
                ]);
            }

            fclose($output);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
