<?php

namespace App\Http\Controllers;

use App\Models\Administration;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\OfficeSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // Karyawan biasa Non-ASN diarahkan langsung ke halaman Absensi
        if ($user->role !== 'admin') {
            return redirect()->route('attendance.index');
        }

        $today = Carbon::today()->toDateString();
        $office = OfficeSetting::getActive();

        // Ambil semua pegawai Non-ASN
        $employees = Employee::with(['user', 'role'])->get();

        // Ambil data absensi hari ini
        $attendancesToday = Attendance::with(['user.employee.role'])
            ->whereDate('date', $today)
            ->get()
            ->keyBy('user_id');

        // Ambil data izin/cuti yang disetujui dan aktif hari ini
        $leavesToday = Administration::with(['user.employee.role'])
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->get()
            ->keyBy('user_id');

        // Klasifikasikan setiap karyawan: Masuk, Izin, atau Tanpa Keterangan (Alpha)
        $listMasuk = [];
        $listIzin = [];
        $listTanpaKeterangan = [];

        foreach ($employees as $emp) {
            $userId = $emp->user_id;
            $att = $attendancesToday->get($userId);
            $leave = $leavesToday->get($userId);

            if ($att && $att->check_in_time) {
                // Pegawai Hadir / Masuk
                $listMasuk[] = [
                    'employee' => $emp,
                    'attendance' => $att,
                    'leave' => null,
                    'status_label' => $att->check_in_status === 'tepat_waktu' ? 'Tepat Waktu' : 'Terlambat',
                ];
            } elseif ($leave || ($att && in_array($att->status, ['izin', 'sakit']))) {
                // Pegawai Izin atau Sakit
                $listIzin[] = [
                    'employee' => $emp,
                    'attendance' => $att,
                    'leave' => $leave,
                    'category' => $leave ? $leave->leave_category : ($att ? $att->status : 'izin'),
                    'reason' => $leave ? $leave->description : ($att ? $att->notes : 'Izin'),
                    'attachment' => $leave ? $leave->attachment : null,
                ];
            } else {
                // Pegawai Tanpa Keterangan (Belum Absen / Alpha)
                $listTanpaKeterangan[] = [
                    'employee' => $emp,
                    'attendance' => null,
                    'leave' => null,
                    'status_label' => 'Tanpa Keterangan',
                ];
            }
        }

        // Statistik
        $stats = [
            'total_karyawan' => $employees->count(),
            'total_masuk' => count($listMasuk),
            'total_izin' => count($listIzin),
            'total_tanpa_keterangan' => count($listTanpaKeterangan),
        ];

        return view('dashboard', [
            'title' => 'Dashboard Utama Admin - Monitoring Aktivitas Non-ASN',
            'active' => 'dashboard',
            'today' => Carbon::now()->locale('id')->translatedFormat('l, d F Y'),
            'todayDate' => $today,
            'office' => $office,
            'stats' => $stats,
            'listMasuk' => $listMasuk,
            'listIzin' => $listIzin,
            'listTanpaKeterangan' => $listTanpaKeterangan,
        ]);
    }

    /**
     * Download / Cetak PDF Rekap Aktivitas Harian
     */
    public function printToday(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $office = OfficeSetting::getActive();
        $employees = Employee::with(['user', 'role'])->get();

        $attendancesToday = Attendance::with(['user.employee.role'])
            ->whereDate('date', $today)
            ->get()
            ->keyBy('user_id');

        $leavesToday = Administration::with(['user.employee.role'])
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->get()
            ->keyBy('user_id');

        $recap = [];
        foreach ($employees as $emp) {
            $userId = $emp->user_id;
            $att = $attendancesToday->get($userId);
            $leave = $leavesToday->get($userId);

            if ($att && $att->check_in_time) {
                $status = 'Hadir (' . ($att->check_in_status === 'tepat_waktu' ? 'Tepat Waktu' : 'Terlambat') . ')';
                $keterangan = 'Masuk: ' . substr($att->check_in_time, 0, 5) . ($att->check_out_time ? ' | Pulang: ' . substr($att->check_out_time, 0, 5) : ' | Belum Pulang');
                if ($att->monev_activity) {
                    $keterangan .= ' - Monev: ' . Str::limit($att->monev_activity, 40);
                }
            } elseif ($leave) {
                $status = 'Izin: ' . ucfirst(str_replace('_', ' ', $leave->leave_category));
                $keterangan = $leave->description;
            } else {
                $status = 'Tanpa Keterangan (Alpha)';
                $keterangan = 'Belum melakukan absensi';
            }

            $recap[] = [
                'name' => $emp->user->name,
                'role' => $emp->role?->name ?? 'Non-ASN',
                'nik' => $emp->nik ?? '-',
                'phone' => $emp->phone_number ?? '-',
                'status' => $status,
                'keterangan' => $keterangan,
                'check_in' => $att?->check_in_time ? substr($att->check_in_time, 0, 5) : '-',
                'check_out' => $att?->check_out_time ? substr($att->check_out_time, 0, 5) : '-',
            ];
        }

        return view('attendance.print_today', [
            'title' => 'Laporan Harian Aktivitas Pegawai Non-ASN',
            'todayFormatted' => Carbon::now()->locale('id')->translatedFormat('l, d F Y'),
            'office' => $office,
            'recap' => $recap,
        ]);
    }
}
