<?php

namespace App\Http\Controllers;

use App\Models\Administration;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use DateTime;
use Carbon\Carbon;

class AdministrationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public static function getCategories(): array
    {
        return [
            'cuti_tahunan' => 'Cuti Tahunan Non-ASN',
            'cuti_sakit' => 'Izin Sakit (Surat Dokter)',
            'izin_pribadi' => 'Izin Kepentingan Pribadi',
            'izin_dinas_luar' => 'Izin Tugas / Dinas Luar',
            'cuti_melahirkan' => 'Cuti Melahirkan',
            'cuti_keguguran' => 'Cuti Bersalin / Keguguran',
            'cuti_berduka' => 'Cuti Berduka / Kematian',
            'cuti_hari_raya' => 'Cuti Bersama / Hari Raya',
            'cuti_alasan_penting' => 'Cuti Karena Alasan Penting',
        ];
    }

    public function create()
    {
        $user = auth()->user();

        // Jika bukan admin dan masih punya pengajuan pending, arahkan ke Halaman Tunggu
        if ($user->role !== 'admin') {
            $pendingLeave = Administration::with(['approver'])
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->latest()
                ->first();

            if ($pendingLeave) {
                notify()->info('Anda masih memiliki permohonan izin yang sedang menunggu persetujuan atasan.', 'Halaman Tunggu');
                return redirect()->route('administration.index');
            }
        }

        $pastLeaves = Administration::with(['approver'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('administration.create', [
            'title' => 'Pengajuan Izin & Cuti Non-ASN',
            'active' => 'administration',
            'categories' => self::getCategories(),
            'pastLeaves' => $pastLeaves,
        ]);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();

        // ALUR PEGAWAI NON-ASN: Langsung Form Pengajuan atau Halaman Tunggu Persetujuan
        if ($user->role !== 'admin') {
            $pendingLeave = Administration::with(['approver'])
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->latest()
                ->first();

            if ($pendingLeave) {
                // Tampilkan Halaman Tunggu
                $pastLeaves = Administration::with(['approver'])
                    ->where('user_id', $user->id)
                    ->where('id', '!=', $pendingLeave->id)
                    ->latest()
                    ->take(10)
                    ->get();

                return view('administration.waiting', [
                    'title' => 'Halaman Tunggu - Pengajuan Izin Non-ASN',
                    'active' => 'administration',
                    'pendingLeave' => $pendingLeave,
                    'categories' => self::getCategories(),
                    'pastLeaves' => $pastLeaves,
                ]);
            }

            // Jika tidak ada yang pending, tampilkan langsung Form Pengajuan Izin
            $pastLeaves = Administration::with(['approver'])
                ->where('user_id', $user->id)
                ->latest()
                ->take(10)
                ->get();

            return view('administration.create', [
                'title' => 'Pengajuan Izin & Cuti Non-ASN',
                'active' => 'administration',
                'categories' => self::getCategories(),
                'pastLeaves' => $pastLeaves,
            ]);
        }

        // ALUR ADMIN: Dashboard Pengajuan Izin, Cuti & Surat Sakit Pegawai
        $query = Administration::with(['user.employee.role', 'approver']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter berdasarkan kategori izin/cuti
        if ($request->filled('leave_category')) {
            $query->where('leave_category', $request->leave_category);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $administrations = $query->latest()->paginate(10);

        // Hitung statistik untuk Dashboard Izin
        $baseStatsQuery = Administration::query();

        $stats = [
            'total' => (clone $baseStatsQuery)->count(),
            'pending' => (clone $baseStatsQuery)->where('status', 'pending')->count(),
            'approved' => (clone $baseStatsQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $baseStatsQuery)->where('status', 'rejected')->count(),
            'sakit' => (clone $baseStatsQuery)->where('leave_category', 'cuti_sakit')->count(),
            'cuti' => (clone $baseStatsQuery)->where('leave_category', 'cuti_tahunan')->count(),
            'izin' => (clone $baseStatsQuery)->whereIn('leave_category', ['izin_pribadi', 'izin_dinas_luar', 'cuti_alasan_penting'])->count(),
        ];

        // Daftar pegawai yang sedang izin/sakit hari ini (status approved)
        $activeLeavesToday = Administration::with(['user.employee.role'])
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->get();

        return view('administration.index', [
            'administrations' => $administrations,
            'title' => 'Dashboard Pengajuan Izin & Cuti Non-ASN',
            'active' => 'administration',
            'categories' => self::getCategories(),
            'stats' => $stats,
            'activeLeavesToday' => $activeLeavesToday,
            'todayFormatted' => Carbon::now()->locale('id')->translatedFormat('l, d F Y'),
        ]);
    }

    public function store(Request $request)
    {
        Log::info('Store Leave Request:', $request->all());

        $validated = $request->validate([
            'leave_category' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'required|string|min:10',
            'bring_laptop' => 'nullable|boolean',
            'contacted' => 'nullable|boolean',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3072',
        ], [
            'description.required' => 'Keterangan alasan izin/cuti wajib diisi.',
            'description.min' => 'Keterangan minimal 10 karakter.',
            'attachment.max' => 'Ukuran berkas lampiran maksimal 3MB.',
        ]);

        try {
            DB::beginTransaction();

            $startDate = new DateTime($validated['start_date']);
            $endDate = new DateTime($validated['end_date']);
            $lamaCuti = $startDate->diff($endDate)->days + 1;

            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('leave_attachments', 'public');
            }

            $administration = new Administration();
            $administration->fill([
                'user_id' => auth()->id(),
                'leave_category' => $validated['leave_category'],
                'lama_hari_cuti' => $lamaCuti,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'description' => $validated['description'],
                'bring_laptop' => $request->boolean('bring_laptop'),
                'contacted' => $request->boolean('contacted'),
                'status' => 'pending',
                'attachment' => $attachmentPath,
            ]);
            $administration->save();

            DB::commit();
            notify()->success('Pengajuan izin berhasil diajukan, menunggu persetujuan atasan.', 'Berhasil');
            return redirect()->route('administration.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving leave submission: ' . $e->getMessage());

            notify()->error('Gagal mengajukan izin: ' . $e->getMessage(), 'Error');
            return redirect()->back()->withInput();
        }
    }

    public function edit($id)
    {
        $administration = Administration::findOrFail($id);

        // Hanya pemilik atau admin yang bisa edit jika status pending
        if (auth()->user()->role !== 'admin' && $administration->user_id !== auth()->id()) {
            abort(403);
        }

        return view('administration.edit', [
            'administration' => $administration,
            'title' => 'Edit Pengajuan Izin',
            'active' => 'administration',
            'categories' => self::getCategories(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $administration = Administration::findOrFail($id);

        if (auth()->user()->role !== 'admin' && $administration->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'leave_category' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'required|string|min:10',
            'bring_laptop' => 'nullable|boolean',
            'contacted' => 'nullable|boolean',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3072',
        ]);

        try {
            DB::beginTransaction();

            $startDate = new DateTime($validated['start_date']);
            $endDate = new DateTime($validated['end_date']);
            $lamaCuti = $startDate->diff($endDate)->days + 1;

            if ($request->hasFile('attachment')) {
                if ($administration->attachment && Storage::disk('public')->exists($administration->attachment)) {
                    Storage::disk('public')->delete($administration->attachment);
                }
                $administration->attachment = $request->file('attachment')->store('leave_attachments', 'public');
            }

            $administration->update([
                'leave_category' => $validated['leave_category'],
                'lama_hari_cuti' => $lamaCuti,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'description' => $validated['description'],
                'bring_laptop' => $request->boolean('bring_laptop'),
                'contacted' => $request->boolean('contacted'),
            ]);

            DB::commit();
            notify()->success('Pengajuan izin berhasil diperbarui.', 'Berhasil');
            return redirect()->route('administration.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating leave submission: ' . $e->getMessage());

            notify()->error('Gagal memperbarui izin: ' . $e->getMessage(), 'Error');
            return redirect()->back()->withInput();
        }
    }

    public function approve(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Hanya admin / atasan yang dapat menyetujui izin.');
        }

        $administration = Administration::findOrFail($id);
        $administration->status = 'approved';
        $administration->approver_id = auth()->id();
        $administration->approver_note = $request->input('approver_note', 'Disetujui oleh atasan.');
        $administration->save();

        // Catat hari izin/cuti ke data kehadiran (Attendance)
        try {
            $start = Carbon::parse($administration->start_date);
            $end = Carbon::parse($administration->end_date);
            $statusAttendance = ($administration->leave_category === 'cuti_sakit') ? 'sakit' : 'izin';

            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                if ($date->isWeekend()) continue;

                Attendance::updateOrCreate(
                    [
                        'user_id' => $administration->user_id,
                        'date' => $date->toDateString(),
                    ],
                    [
                        'status' => $statusAttendance,
                        'notes' => 'Izin/Cuti: ' . self::getCategories()[$administration->leave_category] ?? $administration->leave_category,
                    ]
                );
            }
        } catch (\Exception $e) {
            Log::warning('Could not auto-sync attendance for leave: ' . $e->getMessage());
        }

        notify()->success('Pengajuan izin berhasil disetujui.', 'Disetujui');
        return redirect()->back();
    }

    public function reject(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Hanya admin / atasan yang dapat menolak izin.');
        }

        $request->validate([
            'approver_note' => 'required|string|max:255',
        ], [
            'approver_note.required' => 'Harap berikan alasan penolakan.',
        ]);

        $administration = Administration::findOrFail($id);
        $administration->status = 'rejected';
        $administration->approver_id = auth()->id();
        $administration->approver_note = $request->input('approver_note');
        $administration->save();

        notify()->success('Pengajuan izin telah ditolak.', 'Ditolak');
        return redirect()->back();
    }

    public function destroy($id)
    {
        $administration = Administration::findOrFail($id);

        if (auth()->user()->role !== 'admin' && $administration->user_id !== auth()->id()) {
            abort(403);
        }

        if ($administration->attachment && Storage::disk('public')->exists($administration->attachment)) {
            Storage::disk('public')->delete($administration->attachment);
        }

        $administration->delete();
        notify()->success('Pengajuan izin berhasil dihapus.', 'Berhasil');
        return redirect()->route('administration.index');
    }
}
