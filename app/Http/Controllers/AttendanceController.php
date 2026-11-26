<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\MonevActivity;
use App\Models\OfficeSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Dashboard Presensi Harian Non-ASN
     */
    public function index()
    {
        $user = auth()->user();
        $now = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();
        $office = OfficeSetting::getActive();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        // Ambil semua entri kegiatan monev hari ini
        if ($user->role === 'admin') {
            $todayActivities = MonevActivity::with(['user.employee.role'])
                ->whereDate('date', $today)
                ->orderBy('time', 'desc')
                ->get();
        } else {
            $todayActivities = MonevActivity::where('user_id', $user->id)
                ->whereDate('date', $today)
                ->orderBy('time', 'asc')
                ->get();
        }

        $activityCount = $todayActivities->count();
        $isBeforeClosingTime = $now->format('H:i') <= '14:30';
        $canSubmitMonev = ($user->role !== 'admin') && $isBeforeClosingTime && ($activityCount < 10);

        // Cek pengajuan cuti pending atau yang sedang aktif hari ini untuk user
        $userPendingLeave = \App\Models\Administration::where('user_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        $todayApprovedLeave = \App\Models\Administration::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->first();

        return view('attendance.index', [
            'title' => 'Absensi & Monev Non-ASN',
            'active' => 'attendance',
            'user' => $user,
            'attendance' => $attendance,
            'office' => $office,
            'today' => $now->locale('id')->translatedFormat('l, d F Y'),
            'todayActivities' => $todayActivities,
            'activityCount' => $activityCount,
            'isBeforeClosingTime' => $isBeforeClosingTime,
            'canSubmitMonev' => $canSubmitMonev,
            'monevClosingTime' => '14:30',
            'userPendingLeave' => $userPendingLeave,
            'todayApprovedLeave' => $todayApprovedLeave,
        ]);
    }

    /**
     * Absen Masuk dengan Foto Wajah & Titik Lokasi Kantor
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'required|string', // Base64 data URL
        ], [
            'photo.required' => 'Foto wajah wajib diambil menggunakan kamera.',
            'latitude.required' => 'Titik lokasi GPS diperlukan untuk absensi.',
            'longitude.required' => 'Titik lokasi GPS diperlukan untuk absensi.',
        ]);

        $user = auth()->user();
        $today = Carbon::today()->toDateString();
        $office = OfficeSetting::getActive();

        // Cek apakah sudah absen masuk hari ini
        $existing = Attendance::where('user_id', $user->id)->whereDate('date', $today)->first();
        if ($existing && $existing->check_in_time) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan absen masuk hari ini.'
                ], 422);
            }
            notify()->warning('Anda sudah melakukan absen masuk hari ini.', 'Info');
            return redirect()->back();
        }

        // Hitung jarak ke lokasi kantor (Haversine formula dalam meter)
        $distance = $this->calculateDistance(
            (float) $request->latitude,
            (float) $request->longitude,
            (float) $office->latitude,
            (float) $office->longitude
        );

        // Validasi radius kantor
        if ($distance > $office->radius_meters) {
            $msg = "Absen Ditolak! Anda berada di luar radius kantor ({$distance} meter dari kantor). Batas toleransi maksimal adalah {$office->radius_meters} meter. Absensi hanya diterima di lokasi kantor.";
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            notify()->error($msg, 'Di Luar Area Kantor');
            return redirect()->back()->withInput();
        }

        // Simpan foto wajah dari base64
        $photoPath = $this->saveBase64Image($request->photo, 'attendance_checkin');

        // Tentukan status tepat waktu atau terlambat
        $now = Carbon::now();
        $checkInLimit = Carbon::parse($office->check_in_end);
        $checkInStatus = $now->gt($checkInLimit) ? 'terlambat' : 'tepat_waktu';
        $status = $now->gt($checkInLimit) ? 'terlambat' : 'hadir';

        $employeeId = $user->employee ? $user->employee->id : null;

        Attendance::updateOrCreate(
            [
                'user_id' => $user->id,
                'date' => $today,
            ],
            [
                'employee_id' => $employeeId,
                'check_in_time' => $now->toTimeString(),
                'check_in_photo' => $photoPath,
                'check_in_lat' => $request->latitude,
                'check_in_lng' => $request->longitude,
                'check_in_distance' => $distance,
                'check_in_status' => $checkInStatus,
                'status' => $status,
            ]
        );

        $statusMsg = $checkInStatus === 'terlambat' ? ' (Terlambat)' : ' (Tepat Waktu)';
        $successMsg = 'Absen masuk berhasil direkam' . $statusMsg . ' pada jam ' . $now->format('H:i');

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $successMsg]);
        }

        notify()->success($successMsg, 'Berhasil');
        return redirect()->route('attendance.index');
    }

    /**
     * Absen Pulang Hanya dengan Foto Wajah & Validasi Titik Lokasi Kantor
     */
    public function checkOut(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'required|string', // Foto wajah pulang (base64)
        ], [
            'photo.required' => 'Foto wajah saat pulang wajib diambil menggunakan kamera.',
            'latitude.required' => 'Titik lokasi GPS diperlukan untuk absensi pulang.',
            'longitude.required' => 'Titik lokasi GPS diperlukan untuk absensi pulang.',
        ]);

        $user = auth()->user();
        $now = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();
        $office = OfficeSetting::getActive();

        $attendance = Attendance::where('user_id', $user->id)->whereDate('date', $today)->first();

        if (!$attendance || !$attendance->check_in_time) {
            $msg = 'Anda belum melakukan absen masuk hari ini.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            notify()->error($msg, 'Gagal');
            return redirect()->back();
        }

        if ($attendance->check_out_time) {
            $msg = 'Anda sudah melakukan absen pulang hari ini.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            notify()->warning($msg, 'Info');
            return redirect()->back();
        }

        // Cek Geofencing lokasi kantor saat pulang
        $distance = $this->calculateDistance(
            (float) $request->latitude,
            (float) $request->longitude,
            (float) $office->latitude,
            (float) $office->longitude
        );

        if ($distance > $office->radius_meters) {
            $msg = "Absen Pulang Ditolak! Anda berada di luar radius kantor ({$distance} meter dari kantor). Batas toleransi adalah {$office->radius_meters} meter. Absen pulang hanya diterima di lokasi kantor.";
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            notify()->error($msg, 'Di Luar Area Kantor');
            return redirect()->back()->withInput();
        }

        // Simpan foto wajah pulang
        $photoPath = $this->saveBase64Image($request->photo, 'attendance_checkout');

        $attendance->update([
            'check_out_time' => $now->toTimeString(),
            'check_out_photo' => $photoPath,
            'check_out_lat' => $request->latitude,
            'check_out_lng' => $request->longitude,
            'check_out_distance' => $distance,
        ]);

        $successMsg = 'Absen pulang berhasil diverifikasi dan disimpan pada jam ' . $now->format('H:i') . ' WIB.';

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $successMsg]);
        }

        notify()->success($successMsg, 'Berhasil Pulang');
        return redirect()->route('attendance.index');
    }

    /**
     * Simpan Kegiatan Monev Harian Pegawai (Maks 10 kegiatan, batas jam 14:30 WIB)
     */
    public function storeMonev(Request $request)
    {
        $now = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();
        $user = auth()->user();

        // 1. Validasi Batas Waktu Pengisian (Maksimal jam 14:30 WIB)
        if ($now->format('H:i') > '14:30') {
            $msg = 'Batas waktu pengisian monev kegiatan hari ini adalah sampai pukul 14:30 WIB. Form pengisian telah ditutup.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            notify()->error($msg, 'Waktu Berakhir');
            return redirect()->back();
        }

        // 2. Validasi Kuota Maksimal 10 Kegiatan Per Hari
        $existingCount = MonevActivity::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->count();

        if ($existingCount >= 10) {
            $msg = 'Anda telah mencapai batas kuota maksimal 10 kegiatan untuk hari ini.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            notify()->error($msg, 'Batas Kuota Tercapai');
            return redirect()->back();
        }

        $validated = $request->validate([
            'activity_description' => 'required|string|min:5',
            'obstacle' => 'nullable|string',
            'activity_photos' => 'nullable|array|max:3',
            'activity_photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:3072',
        ], [
            'activity_description.required' => 'Uraian aktivitas kegiatan pekerjaan wajib diisi.',
            'activity_description.min' => 'Uraian aktivitas minimal 5 karakter.',
            'activity_photos.max' => 'Maksimal hanya dapat melampirkan 3 foto kegiatan.',
        ]);

        // Simpan bukti foto kegiatan (maksimal 3 foto)
        $photoPaths = [];
        if ($request->hasFile('activity_photos')) {
            $files = array_slice($request->file('activity_photos'), 0, 3);
            foreach ($files as $file) {
                $path = $file->store('monev_activity_photos', 'public');
                $photoPaths[] = $path;
            }
        }

        MonevActivity::create([
            'user_id' => $user->id,
            'date' => $today,
            'time' => $now->toTimeString(),
            'activity_description' => $validated['activity_description'],
            'obstacle' => $request->input('obstacle', 'Tidak ada kendala'),
            'photos' => $photoPaths,
        ]);

        $newCount = $existingCount + 1;
        $successMsg = "Kegiatan #{$newCount} berhasil dicatat (Total: {$newCount} dari 10 kegiatan).";

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $successMsg]);
        }

        notify()->success($successMsg, 'Tersimpan');
        return redirect()->route('attendance.index');
    }

    /**
     * Hapus Entri Kegiatan Monev Harian
     */
    public function destroyMonev($id)
    {
        $activity = MonevActivity::findOrFail($id);
        $user = auth()->user();

        if ($user->role !== 'admin' && $activity->user_id !== $user->id) {
            abort(403);
        }

        // Hapus foto jika ada
        if (!empty($activity->photos)) {
            foreach ($activity->photos as $p) {
                if (Storage::disk('public')->exists($p)) {
                    Storage::disk('public')->delete($p);
                }
            }
        }

        $activity->delete();
        notify()->success('Entri kegiatan berhasil dihapus.', 'Berhasil');
        return redirect()->back();
    }

    /**
     * Rekapan Absensi Karyawan Non-ASN
     */
    public function recap(Request $request)
    {
        $user = auth()->user();
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        $selectedUserId = $request->input('user_id');
        $statusFilter = $request->input('status');

        $query = Attendance::with(['user.employee.role'])
            ->whereMonth('date', $month)
            ->whereYear('date', $year);

        // Admin & Supervisor bisa lihat semua pegawai; role lain hanya bisa lihat milik sendiri
        if (!in_array($user->role, ['admin', 'supervisor'])) {
            $query->where('user_id', $user->id);
        } else {
            if ($selectedUserId) {
                $query->where('user_id', $selectedUserId);
            }
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(15);

        // Attach kegiatan monev harian ke setiap baris absensi
        $userIds = $attendances->pluck('user_id')->unique();
        $dates = $attendances->pluck('date')->map(fn($d) => Carbon::parse($d)->toDateString())->unique();

        $allMonev = MonevActivity::whereIn('user_id', $userIds)
            ->whereIn('date', $dates)
            ->get()
            ->groupBy(fn($m) => $m->user_id . '_' . Carbon::parse($m->date)->toDateString());

        foreach ($attendances as $att) {
            $key = $att->user_id . '_' . Carbon::parse($att->date)->toDateString();
            $att->daily_monev_activities = $allMonev->get($key, collect());
        }

        $canManageAll = in_array($user->role, ['admin', 'supervisor']);

        // Ringkasan Statistik
        $statsQuery = Attendance::whereMonth('date', $month)->whereYear('date', $year);
        if (!$canManageAll) {
            $statsQuery->where('user_id', $user->id);
        } elseif ($selectedUserId) {
            $statsQuery->where('user_id', $selectedUserId);
        }

        $totalRecords = (clone $statsQuery)->count();
        $totalHadir = (clone $statsQuery)->whereIn('status', ['hadir', 'terlambat'])->count();
        $totalTepatWaktu = (clone $statsQuery)->where('check_in_status', 'tepat_waktu')->count();
        $totalTerlambat = (clone $statsQuery)->where('check_in_status', 'terlambat')->count();
        $totalIzin = (clone $statsQuery)->where('status', 'izin')->count();
        $totalSakit = (clone $statsQuery)->where('status', 'sakit')->count();

        $employees = $canManageAll ? User::with('employee.role')->get() : collect([$user]);

        return view('attendance.recap', [
            'title' => 'Rekapan Absensi & Monev Non-ASN',
            'active' => 'attendance_recap',
            'attendances' => $attendances,
            'month' => $month,
            'year' => $year,
            'selectedUserId' => $selectedUserId,
            'statusFilter' => $statusFilter,
            'employees' => $employees,
            'stats' => [
                'total' => $totalRecords,
                'hadir' => $totalHadir,
                'tepat_waktu' => $totalTepatWaktu,
                'terlambat' => $totalTerlambat,
                'izin' => $totalIzin,
                'sakit' => $totalSakit,
            ],
        ]);
    }

    /**
     * Cetak Rekapan Absensi (Print View)
     */
    public function printRecap(Request $request)
    {
        $user = auth()->user();
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        $selectedUserId = $request->input('user_id');

        $query = Attendance::with(['user.employee.role'])
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'asc');

        $canManageAll = in_array($user->role, ['admin', 'supervisor']);

        if (!$canManageAll) {
            $query->where('user_id', $user->id);
            $targetUser = $user;
        } else {
            if ($selectedUserId) {
                $query->where('user_id', $selectedUserId);
                $targetUser = User::find($selectedUserId);
            } else {
                $targetUser = null;
            }
        }

        $attendances = $query->get();
        $office = OfficeSetting::getActive();

        // Tanda Tangan QR Code Digital untuk Rekap Absensi
        $monthName = Carbon::createFromDate($year, $month, 1)->locale('id')->translatedFormat('F Y');
        $qrData = "KEMENTERIAN HUKUM RI\n" .
                  "DOKUMEN RESMI REKAPITULASI ABSENSI PEGAWAI NON-ASN\n" .
                  "PERIODE: {$monthName}\n" .
                  "LOKASI: " . ($office->office_name ?? 'Kantor Wilayah') . "\n" .
                  "TOTAL REKAP: " . $attendances->count() . " Catatan Kehadiran\n" .
                  "STATUS: TERVERIFIKASI SISTEM ELEKTRONIK\n" .
                  "PENANGGUNG JAWAB: Kasubag Kepegawaian & Tata Usaha\n" .
                  "WAKTU CETAK: " . Carbon::now()->format('d/m/Y H:i') . " WIB";
        $qrSvg = \App\Helpers\QrCodeHelper::generateSvg($qrData, 120);

        return view('attendance.print', [
            'title' => 'Cetak Rekap Absensi Non-ASN',
            'attendances' => $attendances,
            'office' => $office,
            'month' => $month,
            'year' => $year,
            'targetUser' => $targetUser,
            'qrSvg' => $qrSvg,
        ]);
    }

    /**
     * Pengaturan Titik Lokasi Kantor
     */
    public function settings()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $office = OfficeSetting::getActive();

        return view('attendance.settings', [
            'title' => 'Pengaturan Lokasi Kantor Non-ASN',
            'active' => 'attendance_settings',
            'office' => $office,
        ]);
    }

    /**
     * Simpan Pengaturan Titik Lokasi Kantor
     */
    public function updateSettings(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'office_name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_meters' => 'required|integer|min:20|max:5000',
            'check_in_start' => 'required',
            'check_in_end' => 'required',
            'check_out_start' => 'required',
        ]);

        $office = OfficeSetting::getActive();
        $office->update($validated);

        notify()->success('Titik lokasi kantor dan ketentuan presensi berhasil diperbarui.', 'Berhasil');
        return redirect()->route('attendance.settings');
    }

    /**
     * Kalibrasi cepat titik lokasi kantor ke koordinat GPS perangkat saat ini
     */
    public function calibrateOffice(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_meters' => 'nullable|integer|min:50|max:5000',
        ]);

        $office = OfficeSetting::getActive();
        $office->update([
            'office_name' => 'Kanwil Kemenkumham Kepulauan Bangka Belitung',
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'radius_meters' => $request->input('radius_meters', max(500, $office->radius_meters)),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Titik lokasi kantor berhasil disesuaikan dengan posisi GPS Anda.',
            'office' => $office,
        ]);
    }

    /**
     * Detail Modal Monev
     */
    public function getMonevDetail($id)
    {
        $attendance = Attendance::with(['user.employee.role'])->findOrFail($id);

        if (auth()->user()->role !== 'admin' && $attendance->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $activities = MonevActivity::where('user_id', $attendance->user_id)
            ->whereDate('date', $attendance->date)
            ->orderBy('time', 'asc')
            ->get();

        $formattedActivities = $activities->map(function ($act, $idx) {
            return [
                'index' => $idx + 1,
                'time' => Carbon::parse($act->time)->format('H:i'),
                'activity_description' => $act->activity_description,
                'obstacle' => $act->obstacle ?? 'Tidak ada kendala',
                'photos' => collect($act->photos ?? [])->map(fn($p) => asset('storage/' . $p))->values(),
            ];
        });

        return response()->json([
            'id' => $attendance->id,
            'name' => $attendance->user->name,
            'role' => $attendance->user->employee?->role?->name ?? 'Pegawai Non-ASN',
            'date' => Carbon::parse($attendance->date)->translatedFormat('l, d F Y'),
            'check_in_time' => $attendance->check_in_time ? Carbon::parse($attendance->check_in_time)->format('H:i') : '-',
            'check_in_photo' => $attendance->check_in_photo ? asset('storage/' . $attendance->check_in_photo) : null,
            'check_in_distance' => $attendance->check_in_distance,
            'check_out_time' => $attendance->check_out_time ? Carbon::parse($attendance->check_out_time)->format('H:i') : '-',
            'check_out_photo' => $attendance->check_out_photo ? asset('storage/' . $attendance->check_out_photo) : null,
            'check_out_distance' => $attendance->check_out_distance,
            'monev_activity' => $attendance->monev_activity ?? ($activities->first()?->activity_description ?? 'Belum ada uraian aktivitas.'),
            'monev_obstacle' => $attendance->monev_obstacle ?? ($activities->first()?->obstacle ?? 'Tidak ada kendala.'),
            'activity_photos' => collect($attendance->activity_photos ?? [])->map(fn($p) => asset('storage/' . $p))->values(),
            'status' => $attendance->status,
            'total_activities' => $activities->count(),
            'activities' => $formattedActivities,
        ]);
    }

    /**
     * Hitung Jarak dua koordinat GPS menggunakan formula Haversine (hasil dalam Meter)
     */
    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): int
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return (int) round($earthRadius * $c);
    }

    /**
     * Simpan base64 string gambar ke storage disk public
     */
    private function saveBase64Image(string $base64Data, string $folder): string
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
            $data = substr($base64Data, strpos($base64Data, ',') + 1);
            $type = strtolower($type[1]);
        } else {
            $data = $base64Data;
            $type = 'jpg';
        }

        $decodedData = base64_decode($data);
        $fileName = $folder . '/' . Str::random(30) . '.' . $type;

        Storage::disk('public')->put($fileName, $decodedData);

        return $fileName;
    }
}
