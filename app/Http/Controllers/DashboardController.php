<?php

namespace App\Http\Controllers;

use App\Models\Administration;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\MonevActivity;
use App\Models\OfficeSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = $request->user();

        // Karyawan biasa Non-ASN diarahkan langsung ke halaman Absensi
        // Role 'admin' dan 'supervisor' diperbolehkan mengakses Dashboard
        if (!in_array($user->role, ['admin', 'supervisor'])) {
            return redirect()->route('attendance.index');
        }

        $today = Carbon::today()->toDateString();
        $statsOnly = $request->expectsJson();

        // Ambil semua pegawai Non-ASN
        $employees = $statsOnly ? Employee::get(['user_id']) : Employee::with(['user', 'role'])->get();

        // Ambil data absensi hari ini
        $attendancesToday = Attendance::query()->when(!$statsOnly, fn ($query) => $query->with('user.employee.role'))
            ->whereDate('date', $today)
            ->get()
            ->keyBy('user_id');

        // Ambil data izin/cuti yang disetujui dan aktif hari ini
        $leavesToday = Administration::query()->when(!$statsOnly, fn ($query) => $query->with('user.employee.role'))
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->get()
            ->keyBy('user_id');

        // Ambil semua aktivitas monev hari ini dikelompokkan per user
        $monevQuery = MonevActivity::whereDate('date', $today);
        $allMonevActivitiesToday = $statsOnly ? collect() : $monevQuery->with('user.employee.role')
            ->orderByDesc('time')->get();

        $monevActivitiesToday = $allMonevActivitiesToday->groupBy('user_id');

        // Klasifikasikan setiap karyawan: Masuk (Tepat Waktu & Telat), Sakit, Izin, atau Tanpa Keterangan (Alpha)
        $listMasuk = [];
        $listTepatWaktu = [];
        $listTelat = [];
        $listSakit = [];
        $listIzin = [];
        $listTanpaKeterangan = [];

        foreach ($employees as $emp) {
            $userId = $emp->user_id;
            $att = $attendancesToday->get($userId);
            $leave = $leavesToday->get($userId);
            $empActivities = $monevActivitiesToday->get($userId, collect());

            if ($att && $att->check_in_time) {
                // Pegawai Hadir / Masuk
                $isLate = ($att->check_in_status === 'terlambat' || $att->status === 'terlambat');
                $item = [
                    'employee' => $emp,
                    'attendance' => $att,
                    'leave' => null,
                    'is_late' => $isLate,
                    'status_label' => $isLate ? 'Terlambat' : 'Tepat Waktu',
                    'monev_activities' => $empActivities,
                ];

                $listMasuk[] = $item;
                if ($isLate) {
                    $listTelat[] = $item;
                } else {
                    $listTepatWaktu[] = $item;
                }
            } else {
                // Cek apakah Sakit atau Izin/Cuti
                // Prioritas: attendance status sakit/izin lebih spesifik dari leave category
                $attSakit = ($att && $att->status === 'sakit');
                $attIzin  = ($att && $att->status === 'izin');
                $leaveSakit = ($leave && $leave->leave_category === 'cuti_sakit');
                $leaveIzin  = ($leave && $leave->leave_category !== 'cuti_sakit');

                // Sakit didahulukan: att->status=sakit ATAU leave cuti_sakit (selama bukan override izin)
                $isSakit = $attSakit || ($leaveSakit && !$attIzin);
                // Izin: att->status=izin ATAU leave non-sakit (selama tidak sakit)
                $isIzin  = !$isSakit && ($attIzin || $leaveIzin);

                if ($isSakit) {
                    $listSakit[] = [
                        'employee' => $emp,
                        'attendance' => $att,
                        'leave' => $leave,
                        'category' => 'cuti_sakit',
                        'reason' => $leave ? $leave->description : ($att ? $att->notes : 'Izin Sakit (Surat Dokter)'),
                        'attachment' => $leave ? $leave->attachment : null,
                        'monev_activities' => $empActivities,
                    ];
                } elseif ($isIzin) {
                    $listIzin[] = [
                        'employee' => $emp,
                        'attendance' => $att,
                        'leave' => $leave,
                        'category' => $leave ? $leave->leave_category : ($att ? $att->status : 'izin'),
                        'reason' => $leave ? $leave->description : ($att ? $att->notes : 'Izin'),
                        'attachment' => $leave ? $leave->attachment : null,
                        'monev_activities' => $empActivities,
                    ];
                } else {
                    // Pegawai Tanpa Keterangan (Belum Absen / Alpha)
                    $listTanpaKeterangan[] = [
                        'employee' => $emp,
                        'attendance' => null,
                        'leave' => null,
                        'status_label' => 'Tanpa Keterangan',
                        'monev_activities' => $empActivities,
                    ];
                }
            }
        }

        // Statistik
        $stats = [
            'total_karyawan' => $employees->count(),
            'total_masuk' => count($listMasuk),
            'total_tepat_waktu' => count($listTepatWaktu),
            'total_telat' => count($listTelat),
            'total_sakit' => count($listSakit),
            'total_izin' => count($listIzin),
            'total_tanpa_keterangan' => count($listTanpaKeterangan),
            'total_monev_kegiatan' => $statsOnly ? $monevQuery->count() : $allMonevActivitiesToday->count(),
        ];

        if ($statsOnly) {
            return response()->json($stats)->header('Cache-Control', 'no-store');
        }

        return view('dashboard', [
            'title' => 'Dashboard Monitoring Aktivitas Pegawai Non-ASN',
            'active' => 'dashboard',
            'today' => Carbon::now()->locale('id')->translatedFormat('l, d F Y'),
            'todayDate' => $today,
            'office' => OfficeSetting::getActive(),
            'stats' => $stats,
            'listMasuk' => $listMasuk,
            'listTepatWaktu' => $listTepatWaktu,
            'listTelat' => $listTelat,
            'listSakit' => $listSakit,
            'listIzin' => $listIzin,
            'listTanpaKeterangan' => $listTanpaKeterangan,
            'allMonevActivitiesToday' => $allMonevActivitiesToday,
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

        $monevActivitiesToday = MonevActivity::whereDate('date', $today)
            ->orderBy('time', 'asc')
            ->get()
            ->groupBy('user_id');

        $recap = [];
        foreach ($employees as $emp) {
            $userId = $emp->user_id;
            $att = $attendancesToday->get($userId);
            $leave = $leavesToday->get($userId);
            $empActivities = $monevActivitiesToday->get($userId, collect());

            if ($att && $att->check_in_time) {
                $status = 'Hadir (' . ($att->check_in_status === 'tepat_waktu' ? 'Tepat Waktu' : 'Terlambat') . ')';
                $keterangan = 'Masuk: ' . substr($att->check_in_time, 0, 5) . ($att->check_out_time ? ' | Pulang: ' . substr($att->check_out_time, 0, 5) : ' | Belum Pulang');
                if ($empActivities->isNotEmpty()) {
                    $keterangan .= ' - Monev: ' . $empActivities->count() . ' kegiatan tercatat';
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
                'activities' => $empActivities,
            ];
        }

        // Tanda Tangan QR Code Digital untuk Laporan Harian
        $todayText = Carbon::now()->locale('id')->translatedFormat('l, d F Y');
        $qrData = "KEMENTERIAN HUKUM RI\n" .
                  "DOKUMEN RESMI LAPORAN AKTIVITAS HARIAN NON-ASN\n" .
                  "TANGGAL: {$todayText}\n" .
                  "LOKASI: " . ($office->office_name ?? 'Kantor Wilayah') . "\n" .
                  "TOTAL PEGAWAI: " . count($recap) . " Orang\n" .
                  "STATUS: TERVERIFIKASI SISTEM ELEKTRONIK\n" .
                  "PENANGGUNG JAWAB: Kasubag Kepegawaian & Tata Usaha\n" .
                  "WAKTU CETAK: " . Carbon::now()->format('d/m/Y H:i') . " WIB";
        $qrSvg = \App\Helpers\QrCodeHelper::generateSvg($qrData, 120);

        return view('attendance.print_today', [
            'title' => 'Laporan Harian Aktivitas Pegawai Non-ASN',
            'todayFormatted' => $todayText,
            'office' => $office,
            'recap' => $recap,
            'qrSvg' => $qrSvg,
        ]);
    }
}
