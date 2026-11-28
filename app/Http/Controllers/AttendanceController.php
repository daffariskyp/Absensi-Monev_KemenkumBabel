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
use Illuminate\Validation\Rule;

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
        $now = Carbon::now(Attendance::TIMEZONE);
        $today = $now->toDateString();
        $yesterday = $now->copy()->subDay()->toDateString();
        $office = OfficeSetting::getActive();
        $isSatpam = $user->employee ? $user->employee->isSatpam() : false;
        $isWorkDayToday = $user->employee ? $user->employee->isWorkDay(Carbon::now(Attendance::TIMEZONE)) : true;

        // Cek apakah Satpam memiliki tugas Shift Malam kemarin yang belum Absen Pulang (kasus lintas hari)
        $attendance = null;
        $isOvernight = false;
        if ($isSatpam) {
            $yesterdayNightShift = Attendance::where('user_id', $user->id)
                ->whereDate('date', $yesterday)
                ->where('shift', 'malam')
                ->whereNotNull('check_in_time')
                ->whereNull('check_out_time')
                ->first();

            if ($yesterdayNightShift) {
                $attendance = $yesterdayNightShift;
                $isOvernight = true;
            }
        }

        // Jika tidak ada pending overnight shift malam kemarin, ambil absensi hari ini
        if (!$attendance) {
            $attendance = Attendance::where('user_id', $user->id)
                ->whereDate('date', $today)
                ->first();
        }

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
            'isSatpam' => $isSatpam,
            'isWorkDayToday' => $isWorkDayToday,
            'isOvernight' => $isOvernight,
            'needsCheckoutMonev' => $attendance?->check_in_time && !$attendance->check_out_time
                && !$this->hasShiftMonev($attendance),
        ]);
    }

    /**
     * Absen Masuk dengan Foto Wajah, Titik Lokasi Kantor, dan Pilihan Shift (khusus Satpam)
     */
    public function checkIn(Request $request)
    {
        $user = auth()->user();
        $isSatpam = $user->employee ? $user->employee->isSatpam() : false;

        $rules = [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'required|string', // Base64 data URL
        ];
        $messages = [
            'photo.required' => 'Foto wajah wajib diambil menggunakan kamera.',
            'latitude.required' => 'Titik lokasi GPS diperlukan untuk absensi.',
            'longitude.required' => 'Titik lokasi GPS diperlukan untuk absensi.',
        ];

        if ($isSatpam) {
            $rules['shift'] = 'required|in:pagi,malam';
            $messages['shift.required'] = 'Silakan pilih shift dinas jaga (Shift Pagi atau Shift Malam).';
            $messages['shift.in'] = 'Pilihan shift tidak valid.';
        }

        $request->validate($rules, $messages);

        $now = Carbon::now(Attendance::TIMEZONE);
        $today = Carbon::today(Attendance::TIMEZONE)->toDateString();
        $office = OfficeSetting::getActive();

        // Validasi jadwal hari kerja wajib (informasi jika bukan hari kerja resmi)
        if ($user->employee && !$user->employee->isWorkDay($now)) {
            $workDays = $user->employee->workDaysLabel();
            $roleName = $user->employee->role?->name ?? 'Pegawai';
            $dayName = $now->locale('id')->translatedFormat('l');
            $msg = "Hari ini ({$dayName}) bukan jadwal hari kerja wajib untuk {$roleName} (Jadwal: {$workDays}).";
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            notify()->warning($msg, 'Bukan Hari Kerja');
            return redirect()->back()->withInput();
        }

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

        // Validasi radius kantor (diabaikan jika radius_meters = 0 untuk mode testing/fleksibel)
        if ($office->radius_meters > 0 && $distance > $office->radius_meters) {
            $msg = "Absen Ditolak! Anda berada di luar radius kantor ({$distance} meter dari kantor). Batas toleransi maksimal adalah {$office->radius_meters} meter. Absensi hanya diterima di lokasi kantor.";
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            notify()->error($msg, 'Di Luar Area Kantor');
            return redirect()->back()->withInput();
        }

        // Simpan foto wajah dari base64
        $photoPath = $this->saveBase64Image($request->photo, 'attendance_checkin');

        // Tentukan batas jam masuk dan status tepat waktu atau terlambat berdasarkan shift
        $shift = $isSatpam ? $request->input('shift') : null;

        if ($isSatpam && $shift === 'malam') {
            $limitTimeStr = $office->satpam_malam_in_end ?? '20:00:00';
        } elseif ($isSatpam && $shift === 'pagi') {
            $limitTimeStr = $office->satpam_pagi_in_end ?? '08:00:00';
        } else {
            $limitTimeStr = $office->check_in_end ?? '08:00:00';
        }

        $checkInLimit = $now->copy()->setTimeFromTimeString($limitTimeStr);
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
                'shift' => $shift,
                'check_in_time' => $now->toTimeString(),
                'check_in_photo' => $photoPath,
                'check_in_lat' => $request->latitude,
                'check_in_lng' => $request->longitude,
                'check_in_distance' => $distance,
                'check_in_status' => $checkInStatus,
                'status' => $status,
            ]
        );

        $shiftLabel = $shift ? ($shift === 'malam' ? ' (Shift Malam)' : ' (Shift Pagi)') : '';
        $statusMsg = $checkInStatus === 'terlambat' ? ' (Terlambat)' : ' (Tepat Waktu)';
        $successMsg = 'Absen masuk' . $shiftLabel . ' berhasil direkam' . $statusMsg . ' pada jam ' . $now->format('H:i');

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
        $now = Carbon::now(Attendance::TIMEZONE);
        $today = $now->toDateString();
        $yesterday = $now->copy()->subDay()->toDateString();
        $office = OfficeSetting::getActive();
        $isSatpam = $user->employee ? $user->employee->isSatpam() : false;

        // Cari absensi: Jika Satpam, cek apakah ada Shift Malam kemarin yang belum checkout (lintas hari)
        $attendance = null;
        if ($isSatpam) {
            $attendance = Attendance::where('user_id', $user->id)
                ->whereDate('date', $yesterday)
                ->where('shift', 'malam')
                ->whereNotNull('check_in_time')
                ->whereNull('check_out_time')
                ->first();
        }

        // Jika bukan overnight shift malam, ambil record absensi hari ini
        if (!$attendance) {
            $attendance = Attendance::where('user_id', $user->id)->whereDate('date', $today)->first();
        }

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

        $checkoutMonev = $this->hasShiftMonev($attendance) ? null : $request->validate([
            'monev_activity' => 'required|string|min:10',
            'monev_obstacle' => 'required|string|min:100',
            'activity_photos' => 'nullable|array|max:3',
            'activity_photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:3072',
        ], [
            'monev_activity.required' => 'Uraian aktivitas wajib diisi.',
            'monev_activity.min' => 'Uraian aktivitas minimal 10 karakter.',
            'monev_obstacle.required' => 'Kendala yang dihadapi wajib diisi.',
            'monev_obstacle.min' => 'Kendala yang dihadapi minimal 100 karakter.',
        ]);

        // Cek Geofencing lokasi kantor saat pulang
        $distance = $this->calculateDistance(
            (float) $request->latitude,
            (float) $request->longitude,
            (float) $office->latitude,
            (float) $office->longitude
        );

        // Cek Geofencing lokasi kantor saat pulang (diabaikan jika radius_meters = 0 untuk mode testing/fleksibel)
        if ($office->radius_meters > 0 && $distance > $office->radius_meters) {
            $msg = "Absen Pulang Ditolak! Anda berada di luar radius kantor ({$distance} meter dari kantor). Absen pulang dan monev hanya diterima di lokasi kantor.";
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            notify()->error($msg, 'Di Luar Area Kantor');
            return redirect()->back()->withInput();
        }

        // Simpan foto wajah pulang
        $photoPath = $this->saveBase64Image($request->photo, 'attendance_checkout');

        $photos = $checkoutMonev ? $this->storeMonevPhotos($request) : [];
        DB::transaction(function () use ($attendance, $now, $photoPath, $request, $distance, $checkoutMonev, $photos) {
            $attendance->update([
                'check_out_time' => $now->toTimeString(),
                'check_out_photo' => $photoPath,
                'check_out_lat' => $request->latitude,
                'check_out_lng' => $request->longitude,
                'check_out_distance' => $distance,
            ]);

            if ($checkoutMonev) {
                MonevActivity::create([
                    'user_id' => $attendance->user_id,
                    'date' => $attendance->date,
                    'time' => $now->toTimeString(),
                    'activity_description' => $checkoutMonev['monev_activity'],
                    'obstacle' => $checkoutMonev['monev_obstacle'],
                    'photos' => $photos,
                ]);
            }
        });

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
        $now = Carbon::now(Attendance::TIMEZONE);
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

        MonevActivity::create([
            'user_id' => $user->id,
            'date' => $today,
            'time' => $now->toTimeString(),
            'activity_description' => $validated['activity_description'],
            'obstacle' => $request->input('obstacle', 'Tidak ada kendala'),
            'photos' => $this->storeMonevPhotos($request),
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
        $period = $this->recapPeriod($request);
        $statsQuery = $this->recapQuery(new Attendance, $period);
        $query = (clone $statsQuery)->with('user.employee.role');

        if ($period['statusFilter']) {
            $query->where('status', $period['statusFilter']);
        }

        $attendances = $query->orderByDesc('date')->orderBy('id')->paginate(15);
        $activities = $this->recapQuery(new MonevActivity, $period)
            ->with('user.employee.role')->orderByDesc('date')->orderByDesc('time')->orderByDesc('id')
            ->paginate(15, ['*'], 'activity_page');

        // Attach kegiatan monev harian ke setiap baris absensi
        $userIds = $attendances->pluck('user_id')->unique();
        $dates = $attendances->pluck('date')->map(fn($d) => Carbon::parse($d)->toDateString())->unique();

        $allMonev = MonevActivity::whereIn('user_id', $userIds)
            ->whereIn(DB::raw('DATE(date)'), $dates)
            ->get()
            ->groupBy(fn($m) => $m->user_id . '_' . Carbon::parse($m->date)->toDateString());

        foreach ($attendances as $att) {
            $key = $att->user_id . '_' . Carbon::parse($att->date)->toDateString();
            $att->daily_monev_activities = $allMonev->get($key, collect());
        }

        $totalRecords = (clone $statsQuery)->count();
        $totalHadir = (clone $statsQuery)->whereIn('status', ['hadir', 'terlambat'])->count();
        $totalTepatWaktu = (clone $statsQuery)->where('check_in_status', 'tepat_waktu')->count();
        $totalTerlambat = (clone $statsQuery)->where('check_in_status', 'terlambat')->count();
        $totalIzin = (clone $statsQuery)->where('status', 'izin')->count();
        $totalSakit = (clone $statsQuery)->where('status', 'sakit')->count();

        $employees = $period['canManageAll'] ? User::with('employee.role')->orderBy('name')->get() : collect();

        return view('attendance.recap', [
            ...$period,
            'title' => 'Rekapan Absensi & Monev Non-ASN',
            'active' => 'attendance_recap',
            'attendances' => $attendances,
            'activities' => $activities,
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
        $period = $this->recapPeriod($request, allowYearly: true);
        $targetUser = $period['selectedUserId'] ? User::with('employee.role')->find($period['selectedUserId']) : null;
        $attendances = $this->recapQuery(new Attendance, $period)->with('user')
            ->when($period['statusFilter'], fn ($query, $status) => $query->where('status', $status))
            ->orderBy('date')->orderBy('id')->get();
        $activities = $this->recapQuery(new MonevActivity, $period)->with('user')
            ->orderBy('date')->orderBy('time')->orderBy('id')->get();
        $office = OfficeSetting::getActive();

        // Tanda Tangan QR Code Digital untuk Rekap Absensi
        $periodLabel = $period['monthName'];
        $qrData = "KEMENTERIAN HUKUM RI\n" .
                  "DOKUMEN RESMI REKAPITULASI ABSENSI PEGAWAI NON-ASN\n" .
                  "PERIODE: {$periodLabel}\n" .
                  "LOKASI: " . ($office->office_name ?? 'Kantor Wilayah') . "\n" .
                  "TOTAL REKAP: " . $attendances->count() . " catatan kehadiran, " . $activities->count() . " kegiatan Monev\n" .
                  "STATUS: TERVERIFIKASI SISTEM ELEKTRONIK\n" .
                  "PENANGGUNG JAWAB: Kasubag Kepegawaian & Tata Usaha\n" .
                  "WAKTU CETAK: " . Carbon::now()->format('d/m/Y H:i') . " WIB";
        $qrSvg = \App\Helpers\QrCodeHelper::generateSvg($qrData, 120);

        return view('attendance.print', [
            ...$period,
            'title' => $period['yearly'] ? 'Cetak Rekap Tahunan Kehadiran & Monev' : 'Cetak Rekap Bulanan Kehadiran & Monev',
            'attendances' => $attendances,
            'activities' => $activities,
            'office' => $office,
            'targetUser' => $targetUser,
            'qrSvg' => $qrSvg,
        ]);
    }

    private function hasShiftMonev(Attendance $attendance): bool
    {
        return (bool) $attendance->monev_activity || MonevActivity::query()
            ->where('user_id', $attendance->user_id)
            ->whereDate('date', $attendance->date)
            ->exists();
    }

    private function storeMonevPhotos(Request $request): array
    {
        $paths = [];
        foreach ($request->file('activity_photos', []) as $photo) {
            $paths[] = $photo->store('monev_activity_photos', 'public');
        }

        return $paths;
    }

    private function recapPeriod(Request $request, bool $allowYearly = false): array
    {
        $validated = $request->validate([
            'month' => ['nullable', Rule::in($allowYearly ? [...range(1, 12), 'all'] : range(1, 12))],
            'year' => 'nullable|integer|between:1900,9998',
            'user_id' => 'nullable|integer|min:1',
            'status' => 'nullable|in:hadir,terlambat,izin,sakit',
        ]);
        $now = Carbon::now();
        $yearly = ($validated['month'] ?? null) === 'all';
        $month = $yearly ? null : (int) ($validated['month'] ?? $now->month);
        $year = (int) ($validated['year'] ?? $now->year);
        $start = Carbon::create($year, $month ?? 1, 1);
        $user = $request->user();
        $canManageAll = in_array($user->role, ['admin', 'supervisor'], true);

        return [
            'month' => $month,
            'year' => $year,
            'yearly' => $yearly,
            'monthName' => $yearly ? 'Januari–Desember ' . $year : $start->locale('id')->translatedFormat('F Y'),
            'startDate' => $start->toDateString(),
            'endDate' => ($yearly ? $start->copy()->endOfYear() : $start->copy()->endOfMonth())->toDateString(),
            'canManageAll' => $canManageAll,
            'selectedUserId' => $canManageAll ? ($validated['user_id'] ?? null) : $user->id,
            'statusFilter' => $validated['status'] ?? null,
        ];
    }

    private function recapQuery(Attendance|MonevActivity $model, array $period): \Illuminate\Database\Eloquent\Builder
    {
        return $model->newQuery()->whereDate('date', '>=', $period['startDate'])->whereDate('date', '<=', $period['endDate'])
            ->when($period['selectedUserId'], fn ($query, $userId) => $query->where('user_id', $userId));
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
            'radius_meters' => 'required|integer|min:0|max:50000000',
            'check_in_start' => 'required',
            'check_in_end' => 'required',
            'check_out_start' => 'required',
            'satpam_pagi_in_start' => 'nullable',
            'satpam_pagi_in_end' => 'nullable',
            'satpam_pagi_out_start' => 'nullable',
            'satpam_malam_in_start' => 'nullable',
            'satpam_malam_in_end' => 'nullable',
            'satpam_malam_out_start' => 'nullable',
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
        abort_unless($request->user()->role === 'admin', 403);
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

        if (!in_array(auth()->user()->role, ['admin', 'supervisor'], true) && $attendance->user_id !== auth()->id()) {
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
