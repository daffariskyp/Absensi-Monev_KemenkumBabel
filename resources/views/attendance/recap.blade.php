<x-layouts.layout :title="$title" :active="$active">
    <div class="max-w-7xl mx-auto py-6 px-4">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Rekapan Absensi & Monev Pegawai Non-ASN</h1>
                <p class="text-sm text-gray-500 mt-1">Laporan rekapitulasi kehadiran, foto wajah, monev harian, kendala (min 100 kar), dan bukti foto kegiatan.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('attendance.print', request()->all()) }}" target="_blank"
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold shadow flex items-center gap-2 transition">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Rekapitulasi
                </a>
                <a href="{{ route('attendance.index') }}"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold shadow flex items-center gap-1.5 transition">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Absen Hari Ini
                </a>
            </div>
        </div>

        <!-- Summary Stat Cards -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white border rounded-xl p-4 shadow-sm">
                <div class="text-xs font-semibold text-gray-500 uppercase">Total Tercatat</div>
                <div class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</div>
                <div class="text-[11px] text-gray-400 mt-0.5">Hari Kerja</div>
            </div>
            <div class="bg-white border border-emerald-100 rounded-xl p-4 shadow-sm">
                <div class="text-xs font-semibold text-emerald-600 uppercase">Hadir Tepat Waktu</div>
                <div class="text-2xl font-bold text-emerald-700 mt-1">{{ $stats['tepat_waktu'] }}</div>
                <div class="text-[11px] text-emerald-500 mt-0.5">Sesuai Ketentuan Jam</div>
            </div>
            <div class="bg-white border border-amber-100 rounded-xl p-4 shadow-sm">
                <div class="text-xs font-semibold text-amber-600 uppercase">Hadir Terlambat</div>
                <div class="text-2xl font-bold text-amber-700 mt-1">{{ $stats['terlambat'] }}</div>
                <div class="text-[11px] text-amber-500 mt-0.5">Melewati Batas Masuk</div>
            </div>
            <div class="bg-white border border-blue-100 rounded-xl p-4 shadow-sm">
                <div class="text-xs font-semibold text-blue-600 uppercase">Izin / Cuti</div>
                <div class="text-2xl font-bold text-blue-700 mt-1">{{ $stats['izin'] }}</div>
                <div class="text-[11px] text-blue-500 mt-0.5">Disetujui Atasan</div>
            </div>
            <div class="bg-white border border-purple-100 rounded-xl p-4 shadow-sm">
                <div class="text-xs font-semibold text-purple-600 uppercase">Sakit</div>
                <div class="text-2xl font-bold text-purple-700 mt-1">{{ $stats['sakit'] }}</div>
                <div class="text-[11px] text-purple-500 mt-0.5">Surat Dokter</div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white border rounded-xl p-4 shadow-sm mb-6">
            <form action="" method="GET" class="flex flex-wrap items-center gap-4">
                <!-- Filter Bulan -->
                <div class="flex items-center gap-2">
                    <label class="text-xs font-semibold text-gray-700">Bulan:</label>
                    <select name="month" class="text-sm border border-gray-300 rounded-lg p-2 bg-gray-50 focus:outline-none focus:ring-1 focus:ring-blue-500" onchange="this.form.submit()">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ Carbon\Carbon::create(null, $m, 1)->locale('id')->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <!-- Filter Tahun -->
                <div class="flex items-center gap-2">
                    <label class="text-xs font-semibold text-gray-700">Tahun:</label>
                    <select name="year" class="text-sm border border-gray-300 rounded-lg p-2 bg-gray-50 focus:outline-none focus:ring-1 focus:ring-blue-500" onchange="this.form.submit()">
                        @for($y = Carbon\Carbon::now()->year - 2; $y <= Carbon\Carbon::now()->year + 1; $y++)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Filter Pegawai (Admin) -->
                @if(auth()->user()->role === 'admin')
                    <div class="flex items-center gap-2">
                        <label class="text-xs font-semibold text-gray-700">Pegawai Non-ASN:</label>
                        <select name="user_id" class="text-sm border border-gray-300 rounded-lg p-2 bg-gray-50 focus:outline-none focus:ring-1 focus:ring-blue-500" onchange="this.form.submit()">
                            <option value="">Semua Pegawai</option>
                            @foreach($employees as $empUser)
                                <option value="{{ $empUser->id }}" {{ $selectedUserId == $empUser->id ? 'selected' : '' }}>
                                    {{ $empUser->name }} ({{ $empUser->employee?->role?->name ?? 'Non-ASN' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Filter Status -->
                <div class="flex items-center gap-2">
                    <label class="text-xs font-semibold text-gray-700">Status:</label>
                    <select name="status" class="text-sm border border-gray-300 rounded-lg p-2 bg-gray-50 focus:outline-none focus:ring-1 focus:ring-blue-500" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="hadir" {{ $statusFilter === 'hadir' ? 'selected' : '' }}>Hadir</option>
                        <option value="terlambat" {{ $statusFilter === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                        <option value="izin" {{ $statusFilter === 'izin' ? 'selected' : '' }}>Izin</option>
                        <option value="sakit" {{ $statusFilter === 'sakit' ? 'selected' : '' }}>Sakit</option>
                    </select>
                </div>

                <noscript>
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-xs rounded">Filter</button>
                </noscript>
            </form>
        </div>

        <!-- Table Data -->
        <div class="bg-white border rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead class="bg-gray-100 text-gray-700 text-xs uppercase font-semibold">
                        <tr>
                            <th class="py-3 px-3">#</th>
                            <th class="py-3 px-3">Tanggal</th>
                            <th class="py-3 px-3">Pegawai Non-ASN</th>
                            <th class="py-3 px-3 text-center">Absen Masuk</th>
                            <th class="py-3 px-3 text-center">Absen Pulang</th>
                            <th class="py-3 px-3 text-center">Status</th>
                            <th class="py-3 px-3 text-center">Monev & Bukti Foto</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-sm">
                        @forelse($attendances as $index => $att)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-3 px-3">{{ $attendances->firstItem() + $index }}</td>
                                <td class="py-3 px-3 whitespace-nowrap font-medium text-gray-900">
                                    {{ Carbon\Carbon::parse($att->date)->locale('id')->translatedFormat('d M Y') }}
                                    <div class="text-[11px] text-gray-500">
                                        {{ Carbon\Carbon::parse($att->date)->locale('id')->translatedFormat('l') }}
                                    </div>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="font-semibold text-gray-800">{{ $att->user?->name ?? 'User N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $att->user?->employee?->role?->name ?? 'Tenaga Non-ASN' }}</div>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    @if($att->check_in_time)
                                        <div class="flex items-center justify-center gap-2">
                                            @if($att->check_in_photo)
                                                <img src="{{ asset('storage/' . $att->check_in_photo) }}" alt="Foto Masuk" 
                                                    class="size-10 rounded-full object-cover border border-emerald-400 cursor-pointer shadow-sm hover:scale-110 transition"
                                                    onclick="openPhotoModal(this.src, 'Foto Wajah Masuk - {{ $att->user?->name }}')">
                                            @endif
                                            <div class="text-xs text-left">
                                                <div class="font-bold text-gray-800">{{ Carbon\Carbon::parse($att->check_in_time)->format('H:i') }} WIB</div>
                                                @if($att->shift)
                                                    <div class="text-[10px] font-semibold {{ $att->shift === 'malam' ? 'text-indigo-600' : 'text-amber-600' }}">
                                                        {{ $att->shift === 'malam' ? '🌙 Malam' : '☀️ Pagi' }}
                                                    </div>
                                                @endif
                                                <div class="text-[10px] {{ $att->check_in_status === 'tepat_waktu' ? 'text-emerald-600 font-semibold' : 'text-amber-600 font-semibold' }}">
                                                    {{ $att->check_in_status === 'tepat_waktu' ? 'Tepat Waktu' : 'Terlambat' }}
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-center">
                                    @if($att->check_out_time)
                                        <div class="flex items-center justify-center gap-2">
                                            @if($att->check_out_photo)
                                                <img src="{{ asset('storage/' . $att->check_out_photo) }}" alt="Foto Pulang" 
                                                    class="size-10 rounded-full object-cover border border-indigo-400 cursor-pointer shadow-sm hover:scale-110 transition"
                                                    onclick="openPhotoModal(this.src, 'Foto Wajah Pulang - {{ $att->user?->name }}')">
                                            @endif
                                            <div class="text-xs text-left">
                                                <div class="font-bold text-gray-800">{{ Carbon\Carbon::parse($att->check_out_time)->format('H:i') }} WIB</div>
                                                <div class="text-[10px] text-indigo-600 font-semibold">Tervalidasi</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">Belum Pulang</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-center">
                                    @if($att->status === 'hadir')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                            Hadir
                                        </span>
                                    @elseif($att->status === 'terlambat')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                            Terlambat
                                        </span>
                                    @elseif($att->status === 'izin')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                            Izin
                                        </span>
                                    @elseif($att->status === 'sakit')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                            Sakit
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                            Alpha
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-center">
                                    @if($att->monev_activity || $att->monev_obstacle)
                                        <button type="button" onclick="loadMonevDetail({{ $att->id }})"
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-lg border border-indigo-200 transition">
                                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Detail Monev
                                            @if(!empty($att->activity_photos))
                                                <span class="size-4 rounded-full bg-indigo-600 text-white text-[10px] flex items-center justify-center font-bold">
                                                    {{ count($att->activity_photos) }}
                                                </span>
                                            @endif
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-400">Tidak ada monev</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-500">
                                    Tidak ada data rekapan absensi pada periode yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t">
                {{ $attendances->links() }}
            </div>
        </div>
    </div>

    <!-- Monev Detail Modal (Fixed Closing) -->
    <div id="monevModal" style="display: none;" class="fixed inset-0 bg-black/60 z-[100] items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl overflow-y-auto max-h-[90vh] relative" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between pb-3 border-b mb-4">
                <div>
                    <h3 id="mPegawaiName" class="text-lg font-bold text-gray-800">Detail Monev & Laporan Aktivitas</h3>
                    <p id="mPegawaiRole" class="text-xs text-gray-500"></p>
                </div>
                <button type="button" onclick="closeMonevModal()" class="text-gray-400 hover:text-gray-700 text-2xl font-bold w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition">&times;</button>
            </div>

            <div class="space-y-4 text-sm">
                <!-- Info Tanggal & Waktu -->
                <div class="grid grid-cols-2 gap-3 bg-gray-50 p-3 rounded-lg border text-xs">
                    <div>
                        <span class="text-gray-500">Tanggal Pelaksanaan:</span>
                        <div id="mDate" class="font-semibold text-gray-800 mt-0.5"></div>
                    </div>
                    <div>
                        <span class="text-gray-500">Jam Masuk / Jam Pulang:</span>
                        <div id="mTimes" class="font-semibold text-gray-800 mt-0.5"></div>
                    </div>
                </div>

                <!-- Uraian Aktivitas -->
                <div>
                    <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">1. Uraian Aktivitas Pekerjaan Hari Ini:</h4>
                    <div id="mActivity" class="p-3 bg-gray-50 rounded-lg border text-gray-700 whitespace-pre-line leading-relaxed text-xs"></div>
                </div>

                <!-- Kendala yang Dihadapi (Min 100 Karakter) -->
                <div>
                    <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">2. Kendala yang Dihadapi (Minimal 100 Karakter):</h4>
                    <div id="mObstacle" class="p-3 bg-amber-50/60 border border-amber-200 rounded-lg text-amber-900 whitespace-pre-line leading-relaxed text-xs"></div>
                </div>

                <!-- Bukti Foto Kegiatan (Maks 3 Foto) -->
                <div>
                    <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">3. Bukti Foto Kegiatan (Dokumentasi Lapangan):</h4>
                    <div id="mPhotos" class="grid grid-cols-3 gap-3"></div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t flex justify-end">
                <button type="button" onclick="closeMonevModal()" class="px-5 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-xs font-semibold text-gray-700 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Photo Modal Preview (Fixed Closing) -->
    <div id="photoModal" style="display: none;" class="fixed inset-0 bg-black/80 z-[110] items-center justify-center p-4">
        <div class="bg-white rounded-2xl overflow-hidden max-w-lg w-full shadow-2xl relative" onclick="event.stopPropagation()">
            <div class="p-3.5 border-b flex justify-between items-center bg-gray-50">
                <h4 id="photoModalTitle" class="text-sm font-bold text-gray-800">Preview Foto</h4>
                <button type="button" onclick="closePhotoModal()" class="text-gray-400 hover:text-gray-700 text-2xl font-bold w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition">&times;</button>
            </div>
            <div class="p-4 flex justify-center bg-black">
                <img id="photoModalImg" src="" class="max-h-[75vh] object-contain rounded">
            </div>
            <div class="p-3 bg-gray-50 border-t flex justify-end">
                <button type="button" onclick="closePhotoModal()" class="px-4 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-bold rounded-lg transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        function loadMonevDetail(id) {
            fetch(`/attendance/${id}/monev`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('mPegawaiName').innerText = data.name;
                    document.getElementById('mPegawaiRole').innerText = data.role;
                    document.getElementById('mDate').innerText = data.date;
                    document.getElementById('mTimes').innerText = `${data.check_in_time} WIB - ${data.check_out_time} WIB`;
                    document.getElementById('mActivity').innerText = data.monev_activity;
                    document.getElementById('mObstacle').innerText = data.monev_obstacle;

                    const photosContainer = document.getElementById('mPhotos');
                    photosContainer.innerHTML = '';

                    if (data.activity_photos && data.activity_photos.length > 0) {
                        data.activity_photos.forEach(photoUrl => {
                            const img = document.createElement('img');
                            img.src = photoUrl;
                            img.className = "w-full aspect-video object-cover rounded-lg border cursor-pointer hover:opacity-90 shadow-sm transition";
                            img.onclick = () => openPhotoModal(photoUrl, 'Bukti Foto Kegiatan');
                            photosContainer.appendChild(img);
                        });
                    } else {
                        photosContainer.innerHTML = '<span class="text-xs text-gray-400 col-span-3">Tidak ada foto kegiatan yang diunggah.</span>';
                    }

                    document.getElementById('monevModal').style.display = 'flex';
                })
                .catch(err => {
                    alert('Gagal memuat data monev: ' + err.message);
                });
        }

        function closeMonevModal() {
            const modal = document.getElementById('monevModal');
            if (modal) modal.style.display = 'none';
        }

        function openPhotoModal(src, title) {
            const modal = document.getElementById('photoModal');
            document.getElementById('photoModalImg').src = src;
            document.getElementById('photoModalTitle').innerText = title;
            modal.style.display = 'flex';
        }

        function closePhotoModal() {
            const modal = document.getElementById('photoModal');
            if (modal) modal.style.display = 'none';
        }

        // Close modals on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePhotoModal();
                closeMonevModal();
            }
        });

        // Close on backdrop click
        document.addEventListener('DOMContentLoaded', function() {
            const monevEl = document.getElementById('monevModal');
            if (monevEl) {
                monevEl.addEventListener('click', function(e) {
                    if (e.target === this) closeMonevModal();
                });
            }

            const photoEl = document.getElementById('photoModal');
            if (photoEl) {
                photoEl.addEventListener('click', function(e) {
                    if (e.target === this) closePhotoModal();
                });
            }
        });
    </script>
</x-layouts.layout>
