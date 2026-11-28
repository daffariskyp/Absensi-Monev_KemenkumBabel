<x-layouts.layout :title="$title" :active="$active">
    <div class="max-w-7xl mx-auto py-6 px-4">
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-900 text-white rounded-2xl p-6 shadow-xl mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">Aktivitas & Kehadiran Pegawai Hari Ini</h1>
                <p class="text-blue-200 text-sm mt-1.5 flex items-center gap-2 flex-wrap">
                    <span>Hari / Tanggal: <strong class="text-white">{{ $today }}</strong></span>
                    <span>&bull;</span>
                    <span>Lokasi Kantor: <strong class="text-yellow-300">{{ $office->office_name }}</strong></span>
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard.printToday') }}" target="_blank"
                    class="btn-colorful px-5 py-2.5 bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 hover:from-emerald-700 hover:to-teal-800 text-white rounded-xl text-sm font-bold shadow-lg flex items-center gap-2 transition transform active:scale-95 border-2 border-transparent">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Cetak hari ini
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('attendance.print') }}" target="_blank"
            class="dashboard-print-form bg-white border border-blue-200 rounded-xl p-4 mb-6">
            <h2 class="font-bold text-gray-900">Cetak rekap kehadiran &amp; Monev</h2>
            <div>
                <label for="dashboard-print-month">Periode</label>
                <select id="dashboard-print-month" name="month">
                    <option value="all">Setahun penuh</option>
                    @foreach(range(1, 12) as $month)
                        <option value="{{ $month }}" @selected($month === (int) substr($todayDate, 5, 2))>
                            {{ \Carbon\Carbon::create(2000, $month, 1)->locale('id')->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="dashboard-print-year">Tahun</label>
                <input id="dashboard-print-year" type="number" name="year" min="1900" max="9998"
                    value="{{ substr($todayDate, 0, 4) }}" required>
            </div>
            <button type="submit" class="app-action">Buka laporan cetak</button>
        </form>

        <figure class="attendance-chart" data-attendance-chart data-url="{{ route('dashboard') }}">
            <figcaption class="attendance-chart-heading">
                <div>
                    <h2>Status kehadiran hari ini</h2>
                    <p data-chart-status>Pembaruan otomatis setiap 30 detik</p>
                </div>
                <div class="attendance-chart-totals">
                    <span><strong data-chart-value="total_karyawan">{{ $stats['total_karyawan'] }}</strong> pegawai</span>
                    <span><strong data-chart-value="total_monev_kegiatan">{{ $stats['total_monev_kegiatan'] }}</strong> kegiatan Monev</span>
                </div>
            </figcaption>
            @foreach([
                'total_tepat_waktu' => 'Tepat waktu',
                'total_telat' => 'Terlambat',
                'total_sakit' => 'Sakit',
                'total_izin' => 'Izin atau cuti',
                'total_libur_terjadwal' => 'Libur Terjadwal',
                'total_tanpa_keterangan' => 'Tanpa keterangan',
            ] as $key => $label)
                <div class="attendance-chart-row">
                    <span id="chart-{{ $key }}">{{ $label }}</span>
                    <meter aria-labelledby="chart-{{ $key }}" data-chart-meter="{{ $key }}"
                        min="0" max="{{ max(1, $stats['total_karyawan']) }}" value="{{ $stats[$key] ?? 0 }}"></meter>
                    <strong data-chart-value="{{ $key }}">{{ $stats[$key] ?? 0 }}</strong>
                </div>
            @endforeach
        </figure>

        <div class="dashboard-tabs" aria-label="Filter status pegawai" role="group">
            @foreach([
                'semua' => 'Semua pegawai',
                'masuk' => 'Hadir',
                'telat' => 'Terlambat',
                'sakit' => 'Sakit',
                'izin' => 'Izin / cuti',
                'libur' => 'Libur Terjadwal',
                'alpha' => 'Tanpa keterangan',
                'monev' => 'Kegiatan Monev',
            ] as $tab => $label)
                <button type="button" onclick="switchTab('tab-{{ $tab }}')" data-dashboard-tab
                    aria-controls="tab-{{ $tab }}" aria-pressed="{{ $tab === 'semua' ? 'true' : 'false' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div id="tab-semua" class="tab-pane block">
            <div class="bg-white border-2 border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="dashboard-table-heading">
                    <h3 class="font-bold text-gray-900 text-sm">Daftar pegawai</h3>
                    <input type="search" id="employeeSearchInput" oninput="filterPegawaiSearch()"
                        aria-label="Cari nama atau divisi pegawai" placeholder="Cari nama atau divisi"
                        class="table-filter px-3 py-1.5 text-sm">
                </div>

                <div class="overflow-x-auto">
                    <table data-mobile-pages class="w-full border-collapse text-left text-sm" id="tableSemuaPegawai">
                        <thead class="bg-gray-100 text-gray-700 text-xs uppercase font-extrabold tracking-wider">
                            <tr>
                                <th class="py-3 px-4">Pegawai</th>
                                <th class="py-3 px-4">Role / Jabatan</th>
                                <th class="py-3 px-4 text-center">Status Hari Ini</th>
                                <th class="py-3 px-4 text-center">Jam Masuk</th>
                                <th class="py-3 px-4 text-center">Jam Pulang</th>
                                <th class="py-3 px-4 text-center">Foto / Bukti</th>
                                <th class="py-3 px-4 text-center">Aktivitas Monev</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            {{-- Pegawai Masuk / Hadir --}}
                            @foreach($listMasuk as $item)
                                @php
                                    $isTelat = $item['is_late'] ?? ($item['attendance']->check_in_status === 'terlambat');
                                @endphp
                                <tr class="pegawai-row hover:bg-gray-50 transition {{ $isTelat ? 'bg-amber-50/30' : '' }}" data-name="{{ mb_strtolower($item['employee']->user?->name ?? '') }}" data-role="{{ mb_strtolower($item['employee']->role?->name ?? '') }}">
                                    <td class="py-3.5 px-4 font-bold text-gray-900">
                                        <div class="flex items-center gap-2">
                                            <span>{{ $item['employee']->user->name }}</span>
                                            @if($isTelat)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 border border-amber-300">TELAT</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">TEPAT WAKTU</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                            {{ $item['employee']->role?->name ?? 'Non-ASN' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($isTelat)
                                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                                Terlambat
                                            </span>
                                        @else
                                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                Tepat Waktu
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-bold text-gray-800">
                                        {{ substr($item['attendance']->check_in_time, 0, 5) }} WIB
                                    </td>
                                    <td class="py-3.5 px-4 text-center text-gray-600 font-medium">
                                        {{ $item['attendance']->check_out_time ? substr($item['attendance']->check_out_time, 0, 5) . ' WIB' : 'Belum Pulang' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($item['attendance']->check_in_photo)
                                            <img src="{{ asset('storage/' . $item['attendance']->check_in_photo) }}"
                                                class="size-10 mx-auto rounded-full object-cover border-2 {{ $isTelat ? 'border-amber-500' : 'border-emerald-500' }} cursor-pointer shadow hover:scale-110 transition"
                                                onclick="showDashboardPhoto('{{ asset('storage/' . $item['attendance']->check_in_photo) }}', 'Foto Masuk: {{ $item['employee']->user->name }}')">
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if(isset($item['monev_activities']) && $item['monev_activities']->isNotEmpty())
                                            <div class="flex items-center justify-center gap-1.5">
                                                <span class="px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800 font-bold text-[11px]">
                                                    {{ $item['monev_activities']->count() }} Kegiatan
                                                </span>
                                                <button type="button" onclick='showEmployeeActivities(@json($item['employee']->user->name), @json($item['monev_activities']))'
                                                    class="btn-colorful px-2.5 py-1 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white rounded-lg text-[11px] font-bold transition shadow-sm border border-transparent">
                                                    Lihat
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-gray-400 italic text-[11px]">Belum mengisi</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                            {{-- Pegawai Sakit (Surat Dokter) --}}
                            @foreach($listSakit as $item)
                                <tr class="pegawai-row hover:bg-gray-50 transition bg-pink-50/25" data-name="{{ mb_strtolower($item['employee']->user?->name ?? '') }}" data-role="{{ mb_strtolower($item['employee']->role?->name ?? '') }}">
                                    <td class="py-3.5 px-4 font-bold text-gray-900">
                                        <div class="flex items-center gap-2">
                                            <span>{{ $item['employee']->user->name }}</span>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-pink-100 text-pink-800 border border-pink-300">SAKIT</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                            {{ $item['employee']->role?->name ?? 'Non-ASN' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-pink-100 text-pink-800 border border-pink-300">
                                            Sakit (Dokter)
                                        </span>
                                    </td>
                                    <td colspan="2" class="py-3.5 px-4 text-center text-xs text-pink-700 font-medium">
                                        Keterangan: {{ Str::limit($item['reason'], 45) }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($item['attachment'])
                                            <button type="button" onclick="showDashboardPhoto('{{ asset('storage/' . $item['attachment']) }}', 'Surat Sakit: {{ $item['employee']->user->name }}')"
                                                class="btn-colorful px-2.5 py-1 bg-pink-600 hover:bg-pink-700 text-white text-[11px] font-bold rounded-lg shadow-sm transition">
                                                Lihat Surat
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center text-xs text-gray-400 italic">-</td>
                                </tr>
                            @endforeach

                            {{-- Pegawai Izin & Cuti --}}
                            @foreach($listIzin as $item)
                                <tr class="pegawai-row hover:bg-gray-50 transition bg-purple-50/25" data-name="{{ mb_strtolower($item['employee']->user?->name ?? '') }}" data-role="{{ mb_strtolower($item['employee']->role?->name ?? '') }}">
                                    <td class="py-3.5 px-4 font-bold text-gray-900">{{ $item['employee']->user->name }}</td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                            {{ $item['employee']->role?->name ?? 'Non-ASN' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-300">
                                            Izin / Cuti
                                        </span>
                                    </td>
                                    <td colspan="2" class="py-3.5 px-4 text-center text-xs text-purple-700 font-medium">
                                        Keterangan: {{ Str::limit($item['reason'], 45) }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($item['attachment'])
                                            <button type="button" onclick="showDashboardPhoto('{{ asset('storage/' . $item['attachment']) }}', 'Lampiran Izin: {{ $item['employee']->user->name }}')"
                                                class="btn-colorful px-2.5 py-1 bg-purple-600 hover:bg-purple-700 text-white text-[11px] font-bold rounded-lg shadow-sm transition">
                                                Lihat Bukti
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center text-xs text-gray-400 italic">-</td>
                                </tr>
                            @endforeach

                            {{-- Pegawai Tanpa Keterangan (Alpha) --}}
                            @foreach($listTanpaKeterangan as $item)
                                <tr class="pegawai-row hover:bg-gray-50 transition bg-rose-50/20" data-name="{{ mb_strtolower($item['employee']->user?->name ?? '') }}" data-role="{{ mb_strtolower($item['employee']->role?->name ?? '') }}">
                                    <td class="py-3.5 px-4 font-bold text-gray-900">{{ $item['employee']->user->name }}</td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                            {{ $item['employee']->role?->name }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                            Tanpa Keterangan
                                        </span>
                                    </td>
                                    <td colspan="2" class="py-3.5 px-4 text-center text-xs text-rose-600 italic">
                                        Belum melakukan presensi hari ini
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($item['employee']->phone_number)
                                            <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $item['employee']->phone_number)) }}" target="_blank"
                                                class="btn-colorful inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[11px] font-bold shadow-sm transition">
                                                WhatsApp
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center text-xs text-gray-400 italic">-</td>
                                </tr>
                            @endforeach
                            @foreach($listLiburTerjadwal as $item)
                                <tr class="pegawai-row hover:bg-gray-50 transition bg-rose-50/20" data-name="{{ mb_strtolower($item['employee']->user?->name ?? '') }}" data-role="{{ mb_strtolower($item['employee']->role?->name ?? '') }}">
                                    <td class="py-3.5 px-4 font-bold text-gray-900">{{ $item['employee']->user->name }}</td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                            {{ $item['employee']->role?->name }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                            Libur Terjadwal
                                        </span>
                                    </td>
                                    <td colspan="2" class="py-3.5 px-4 text-center text-xs text-rose-600 italic">
                                        Belum melakukan presensi hari ini
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($item['employee']->phone_number)
                                            <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $item['employee']->phone_number)) }}" target="_blank"
                                                class="btn-colorful inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[11px] font-bold shadow-sm transition">
                                                WhatsApp
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center text-xs text-gray-400 italic">-</td>
                                </tr>
                            @endforeach

                            <tr id="noRowsFoundSemua" class="hidden">
                                <td colspan="7" class="py-12 text-center">
                                    <div class="max-w-md mx-auto bg-gray-50 border border-gray-200 rounded-2xl p-6 shadow-sm">
                                        <svg class="size-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <p id="noRowsText" class="text-xs font-bold text-gray-700">Tidak ada pegawai yang cocok dengan pencarian.</p>
                                        <button type="button" onclick="resetPegawaiSearch()" class="mt-3 px-4 py-1.5 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white border-2 border-yellow-300 shadow transition inline-flex items-center gap-1.5 cursor-pointer">
                                            Hapus pencarian
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- Tab 2: Terlambat / Telat (Khusus Rekap Pegawai yang Datang Terlambat)     -->
        <!-- ========================================================================= -->
        <div id="tab-telat" class="tab-pane hidden">
            <div class="bg-white border-2 border-amber-300 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-4 border-b bg-amber-50/70 flex justify-between items-center">
                    <div>
                        <h3 class="font-extrabold text-amber-950 text-sm flex items-center gap-2">
                            <span class="p-1 rounded bg-amber-500 text-white">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </span>
                            Daftar Pegawai yang Terlambat Hadir Hari Ini
                        </h3>
                        <p class="text-xs text-amber-800 mt-0.5">Pegawai yang melakukan absensi masuk setelah batas jam masuk yang ditentukan kantor.</p>
                    </div>
                    <span class="text-xs font-black text-amber-900 bg-amber-200 px-3 py-1 rounded-full border border-amber-300">
                        {{ count($listTelat) }} Pegawai Terlambat
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table data-mobile-pages class="w-full border-collapse text-left text-sm">
                        <thead class="bg-gray-100 text-gray-700 text-xs uppercase font-extrabold">
                            <tr>
                                <th class="py-3 px-4">Pegawai</th>
                                <th class="py-3 px-4">Role / Jabatan</th>
                                <th class="py-3 px-4 text-center">Waktu Masuk</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-center">Foto Wajah</th>
                                <th class="py-3 px-4 text-center">Aksi Cepat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($listTelat as $item)
                                <tr class="hover:bg-amber-50/30 transition">
                                    <td class="py-3.5 px-4 font-bold text-gray-900">{{ $item['employee']->user->name }}</td>
                                    <td class="py-3.5 px-4 text-xs font-semibold text-gray-700">{{ $item['employee']->role?->name }}</td>
                                    <td class="py-3.5 px-4 text-center font-black text-amber-800">
                                        {{ substr($item['attendance']->check_in_time, 0, 5) }} WIB
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-900 border border-amber-300">
                                            Terlambat
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($item['attendance']->check_in_photo)
                                            <img src="{{ asset('storage/' . $item['attendance']->check_in_photo) }}"
                                                class="size-11 mx-auto rounded-full object-cover border-2 border-amber-500 cursor-pointer shadow hover:scale-110 transition"
                                                onclick="showDashboardPhoto('{{ asset('storage/' . $item['attendance']->check_in_photo) }}', 'Foto Wajah Terlambat: {{ $item['employee']->user->name }}')">
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($item['employee']->phone_number)
                                            <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $item['employee']->phone_number)) }}" target="_blank"
                                                class="btn-colorful inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
                                                Hubungi WhatsApp
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-emerald-700 font-bold text-xs bg-emerald-50/20">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="size-8 text-emerald-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            <span>Luar biasa! Tidak ada pegawai yang terlambat hadir hari ini.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- Tab 3: Masuk (Hadir)                                                      -->
        <!-- ========================================================================= -->
        <div id="tab-masuk" class="tab-pane hidden">
            <div class="bg-white border-2 border-emerald-300 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-4 border-b bg-emerald-50/60 flex justify-between items-center">
                    <div>
                        <h3 class="font-extrabold text-emerald-950 text-sm">Daftar Seluruh Pegawai yang Masuk / Hadir Hari Ini</h3>
                        <p class="text-xs text-emerald-700">Termasuk pegawai tepat waktu dan yang terlambat.</p>
                    </div>
                    <span class="text-xs font-black text-emerald-800 bg-emerald-100 px-3 py-1 rounded-full border border-emerald-300">
                        {{ count($listMasuk) }} Pegawai Hadir
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table data-mobile-pages class="w-full border-collapse text-left text-sm">
                        <thead class="bg-gray-100 text-gray-700 text-xs uppercase font-extrabold">
                            <tr>
                                <th class="py-3 px-4">Pegawai</th>
                                <th class="py-3 px-4">Role</th>
                                <th class="py-3 px-4 text-center">Waktu Masuk</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-center">Waktu Pulang</th>
                                <th class="py-3 px-4 text-center">Foto Wajah</th>
                                <th class="py-3 px-4">Aktivitas Monev</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($listMasuk as $item)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-3.5 px-4 font-bold text-gray-900">{{ $item['employee']->user->name }}</td>
                                    <td class="py-3.5 px-4 text-xs font-semibold text-gray-600">{{ $item['employee']->role?->name }}</td>
                                    <td class="py-3.5 px-4 text-center font-bold text-gray-800">
                                        {{ substr($item['attendance']->check_in_time, 0, 5) }} WIB
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $item['attendance']->check_in_status === 'tepat_waktu' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-amber-100 text-amber-800 border border-amber-300' }}">
                                            {{ $item['status_label'] }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center text-xs">
                                        {{ $item['attendance']->check_out_time ? substr($item['attendance']->check_out_time, 0, 5) . ' WIB' : 'Belum Pulang' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($item['attendance']->check_in_photo)
                                            <img src="{{ asset('storage/' . $item['attendance']->check_in_photo) }}"
                                                class="size-11 mx-auto rounded-full object-cover border-2 border-emerald-500 cursor-pointer shadow hover:scale-110 transition"
                                                onclick="showDashboardPhoto('{{ asset('storage/' . $item['attendance']->check_in_photo) }}', 'Foto Wajah Masuk: {{ $item['employee']->user->name }}')">
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-xs">
                                        @if(isset($item['monev_activities']) && $item['monev_activities']->isNotEmpty())
                                            <div class="flex items-center gap-2">
                                                <span class="px-2 py-0.5 rounded bg-indigo-100 text-indigo-800 font-bold text-[11px]">
                                                    {{ $item['monev_activities']->count() }} Kegiatan
                                                </span>
                                                <button type="button" onclick='showEmployeeActivities(@json($item['employee']->user->name), @json($item['monev_activities']))'
                                                    class="btn-colorful px-2.5 py-1 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white rounded text-[11px] font-bold transition shadow-sm border border-transparent">
                                                    Lihat Kegiatan
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-gray-400 italic text-[11px]">Belum mengisi kegiatan</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-gray-400 text-xs">Belum ada pegawai yang melakukan absensi masuk hari ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- Tab 4: Tanpa Keterangan (Alpha)                                           -->
        <!-- ========================================================================= -->
        <div id="tab-alpha" class="tab-pane hidden">
            <div class="bg-white border-2 border-rose-300 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-4 border-b bg-rose-50/70 flex justify-between items-center">
                    <div>
                        <h3 class="font-extrabold text-rose-950 text-sm">Daftar Pegawai yang Belum Hadir / Tanpa Keterangan Hari Ini</h3>
                        <p class="text-xs text-rose-700">Dapat segera dihubungi untuk konfirmasi kehadiran atau penugasan lapangan.</p>
                    </div>
                    <span class="text-xs font-black text-rose-800 bg-rose-100 px-3 py-1 rounded-full border border-rose-300">
                        {{ count($listTanpaKeterangan) }} Pegawai
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table data-mobile-pages class="w-full border-collapse text-left text-sm">
                        <thead class="bg-gray-100 text-gray-700 text-xs uppercase font-extrabold">
                            <tr>
                                <th class="py-3 px-4">Pegawai</th>
                                <th class="py-3 px-4">Role / Jabatan</th>
                                <th class="py-3 px-4">NIK</th>
                                <th class="py-3 px-4">No. HP / WhatsApp</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-center">Aksi Cepat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($listTanpaKeterangan as $item)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-3.5 px-4 font-bold text-gray-900">{{ $item['employee']->user->name }}</td>
                                    <td class="py-3.5 px-4 text-xs font-semibold text-gray-700">{{ $item['employee']->role?->name }}</td>
                                    <td class="py-3.5 px-4 text-xs text-gray-500">{{ $item['employee']->nik ?? '-' }}</td>
                                    <td class="py-3.5 px-4 text-xs font-medium text-gray-800">{{ $item['employee']->phone_number ?? '-' }}</td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                            Belum Hadir
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($item['employee']->phone_number)
                                            <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $item['employee']->phone_number)) }}" target="_blank"
                                                class="btn-colorful inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
                                                Hubungi WhatsApp
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-emerald-600 font-bold text-xs">Semua pegawai telah hadir atau memiliki keterangan izin resmi hari ini!</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- Tab 5: Sakit (Surat Dokter)                                               -->
        <!-- ========================================================================= -->
        <div id="tab-sakit" class="tab-pane hidden">
            <div class="bg-white border-2 border-pink-300 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-4 border-b bg-pink-50/70 flex justify-between items-center">
                    <div>
                        <h3 class="font-extrabold text-pink-950 text-sm flex items-center gap-2">
                            <span class="p-1 rounded bg-pink-500 text-white">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                            </span>
                            Daftar Pegawai yang Sakit Hari Ini (Surat Keterangan Dokter)
                        </h3>
                        <p class="text-xs text-pink-700 mt-0.5">Pegawai yang mengajukan izin sakit disertai lampiran surat dokter yang telah diverifikasi.</p>
                    </div>
                    <span class="text-xs font-black text-pink-800 bg-pink-100 px-3 py-1 rounded-full border border-pink-300">
                        {{ count($listSakit) }} Pegawai Sakit
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table data-mobile-pages class="w-full border-collapse text-left text-sm">
                        <thead class="bg-gray-100 text-gray-700 text-xs uppercase font-extrabold">
                            <tr>
                                <th class="py-3 px-4">Pegawai</th>
                                <th class="py-3 px-4">Role / Jabatan</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4">Diagnosa / Alasan Sakit</th>
                                <th class="py-3 px-4 text-center">Bukti Surat Dokter</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($listSakit as $item)
                                <tr class="hover:bg-pink-50/30 transition">
                                    <td class="py-3.5 px-4 font-bold text-gray-900">{{ $item['employee']->user->name }}</td>
                                    <td class="py-3.5 px-4 text-xs font-semibold text-gray-600">{{ $item['employee']->role?->name }}</td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-pink-100 text-pink-800 border border-pink-300">
                                            Sakit (Surat Dokter)
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-xs text-gray-700 max-w-sm">{{ $item['reason'] }}</td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($item['attachment'])
                                            <button type="button" onclick="showDashboardPhoto('{{ asset('storage/' . $item['attachment']) }}', 'Surat Sakit Dokter: {{ $item['employee']->user->name }}')"
                                                class="btn-colorful px-3 py-1.5 bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-700 hover:to-rose-700 text-white rounded-xl text-xs font-bold transition shadow-sm border border-transparent">
                                                Lihat Surat Dokter
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Tidak ada lampiran</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-400 text-xs">Alhamdulillah, tidak ada pegawai yang sedang sakit hari ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- Tab 6: Izin & Cuti Resmi                                                  -->
        <!-- ========================================================================= -->
        <div id="tab-izin" class="tab-pane hidden">
            <div class="bg-white border-2 border-purple-300 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-4 border-b bg-purple-50/60 flex justify-between items-center">
                    <div>
                        <h3 class="font-extrabold text-purple-950 text-sm">Daftar Pegawai yang Sedang Izin / Cuti Hari Ini</h3>
                        <p class="text-xs text-purple-700">Permohonan izin atau cuti resmi yang telah disetujui atasan.</p>
                    </div>
                    <span class="text-xs font-black text-purple-800 bg-purple-100 px-3 py-1 rounded-full border border-purple-300">
                        {{ count($listIzin) }} Pegawai Izin/Cuti
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table data-mobile-pages class="w-full border-collapse text-left text-sm">
                        <thead class="bg-gray-100 text-gray-700 text-xs uppercase font-extrabold">
                            <tr>
                                <th class="py-3 px-4">Pegawai</th>
                                <th class="py-3 px-4">Role</th>
                                <th class="py-3 px-4">Kategori Izin</th>
                                <th class="py-3 px-4">Alasan Izin</th>
                                <th class="py-3 px-4 text-center">Bukti / Lampiran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($listIzin as $item)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-3.5 px-4 font-bold text-gray-900">{{ $item['employee']->user->name }}</td>
                                    <td class="py-3.5 px-4 text-xs font-semibold text-gray-600">{{ $item['employee']->role?->name }}</td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-300">
                                            {{ ucfirst(str_replace('_', ' ', $item['category'])) }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-xs text-gray-700 max-w-sm">{{ $item['reason'] }}</td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($item['attachment'])
                                            <button type="button" onclick="showDashboardPhoto('{{ asset('storage/' . $item['attachment']) }}', 'Lampiran Izin: {{ $item['employee']->user->name }}')"
                                                class="btn-colorful px-3 py-1.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-sm border border-transparent">
                                                Buka Bukti Izin
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Tidak ada lampiran</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-400 text-xs">Tidak ada pegawai yang sedang izin atau cuti hari ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- Tab 6: Laporan Monev Hari Ini                                             -->
        <!-- ========================================================================= -->
        <div id="tab-libur" class="tab-pane hidden">
            <div class="bg-white border rounded-2xl shadow-sm overflow-hidden">
                <div class="p-4 border-b bg-slate-50 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm">Daftar Pegawai yang Libur Terjadwal Hari Ini</h3>
                        <p class="text-xs text-slate-500">Pegawai Non-ASN dengan hari kerja Senin - Kamis libur setiap Jumat. Satpam tetap masuk pada hari Jumat.</p>
                    </div>
                    <span class="text-xs font-bold text-slate-700 bg-slate-200 px-3 py-1 rounded-full">{{ count($listLiburTerjadwal) }} Pegawai</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-sm">
                        <thead class="bg-gray-100 text-gray-700 text-xs uppercase font-bold">
                            <tr>
                                <th class="py-3 px-4">Pegawai</th>
                                <th class="py-3 px-4">Role / Jabatan</th>
                                <th class="py-3 px-4 text-center">Hari Kerja Wajib</th>
                                <th class="py-3 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($listLiburTerjadwal as $item)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-3.5 px-4 font-bold text-gray-900">{{ $item['employee']->user->name }}</td>
                                    <td class="py-3.5 px-4 text-xs font-semibold text-gray-700">{{ $item['employee']->role?->name }}</td>
                                    <td class="py-3.5 px-4 text-xs text-center text-gray-600">{{ $item['employee']->workDaysLabel() }}</td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-200 text-slate-700">
                                            Libur Terjadwal (Off Dinas)
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-gray-400 text-xs">Tidak ada pegawai yang libur terjadwal hari ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="tab-monev" class="tab-pane hidden">
            <div class="bg-white border-2 border-indigo-300 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-4 border-b bg-indigo-50/60 flex flex-col md:flex-row md:items-center justify-between gap-3">
                    <div>
                        <h3 class="font-extrabold text-indigo-950 text-base flex items-center gap-2">
                            <span class="p-1.5 rounded-lg bg-indigo-600 text-white">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                            </span>
                            Monitoring Laporan Monev & Aktivitas Pegawai Non-ASN Hari Ini
                        </h3>
                        <p class="text-xs text-indigo-700 mt-0.5">Seluruh rincian tugas dan kendala lapangan yang dilaporkan oleh Satpam, Driver, dan Petugas Kebersihan.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-3.5 py-1 rounded-full text-xs font-black bg-indigo-100 text-indigo-900 border border-indigo-300">
                            Total: {{ $allMonevActivitiesToday->count() }} Kegiatan Tercatat
                        </span>
                    </div>
                </div>

                @if($allMonevActivitiesToday->isEmpty())
                    <div class="text-center py-12 text-gray-400 text-xs">
                        <svg class="size-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="font-semibold text-gray-600 text-sm">Belum ada pegawai yang mengirimkan aktivitas kegiatan monev hari ini.</p>
                        <p class="text-gray-400 mt-1">Kegiatan yang dicatat oleh Satpam, Driver, atau Petugas Kebersihan akan langsung muncul di sini secara real-time.</p>
                    </div>
                @else
                    <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($allMonevActivitiesToday as $idx => $act)
                            <div class="bg-gray-50 hover:bg-white transition border-2 border-indigo-100 rounded-2xl p-4 shadow-sm hover:shadow-md">
                                <div class="flex items-start justify-between gap-3 pb-3 border-b border-gray-200 mb-3">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-bold text-gray-900 text-sm">{{ $act->user->name }}</h4>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                                {{ $act->user->employee?->role?->name ?? 'Pegawai Non-ASN' }}
                                            </span>
                                        </div>
                                        <span class="text-[11px] text-gray-500 mt-0.5 block">
                                            Jam Pengisian: <strong class="text-indigo-700">{{ Carbon\Carbon::parse($act->time)->format('H:i') }} WIB</strong>
                                        </span>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-xl text-xs font-black bg-indigo-600 text-white shadow-sm">
                                        #{{ $idx + 1 }}
                                    </span>
                                </div>

                                <div class="space-y-2 text-xs">
                                    <div>
                                        <span class="font-bold text-gray-700 block mb-0.5">Uraian Aktivitas:</span>
                                        <p class="text-gray-800 leading-relaxed bg-white p-2.5 rounded-xl border border-gray-200">{{ $act->activity_description }}</p>
                                    </div>

                                    @if($act->obstacle && $act->obstacle !== 'Tidak ada kendala')
                                        <div>
                                            <span class="font-bold text-amber-800 block mb-0.5">Kendala / Hambatan:</span>
                                            <p class="text-amber-900 bg-amber-50 p-2.5 rounded-xl border border-amber-200 italic">{{ $act->obstacle }}</p>
                                        </div>
                                    @endif

                                    @if(!empty($act->photos))
                                        <div class="pt-1">
                                            <span class="font-bold text-gray-700 block mb-1">Bukti Foto Dokumentasi ({{ count($act->photos) }} Foto):</span>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($act->photos as $p)
                                                    <img src="{{ asset('storage/' . $p) }}" alt="Foto Monev"
                                                        class="size-16 rounded-xl object-cover border-2 border-indigo-200 cursor-pointer hover:scale-105 shadow transition"
                                                        onclick="showDashboardPhoto('{{ asset('storage/' . $p) }}', 'Bukti Kegiatan: {{ $act->user->name }}')">
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Daftar Kegiatan Monev Pegawai (Admin View) -->
    <div id="employeeActivitiesModal" style="display: none;" class="fixed inset-0 bg-black/60 z-[100] items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl overflow-y-auto max-h-[85vh] relative" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between pb-3 border-b mb-4">
                <div>
                    <h3 id="actModalEmployeeName" class="text-base font-bold text-gray-800">Daftar Kegiatan Monev Pegawai</h3>
                    <p id="actModalCount" class="text-xs text-indigo-600 font-semibold mt-0.5"></p>
                </div>
                <button type="button" onclick="closeEmployeeActivities()" class="text-gray-400 hover:text-gray-700 text-2xl font-bold w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition">&times;</button>
            </div>

            <div id="actModalContent" class="space-y-3"></div>

            <div class="mt-6 pt-3 border-t flex justify-end">
                <button type="button" onclick="closeEmployeeActivities()" class="btn-colorful px-5 py-2 bg-gradient-to-r from-gray-700 to-gray-800 hover:from-gray-800 hover:to-gray-900 rounded-xl text-xs font-bold text-white transition shadow">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Pratinjau Foto -->
    <div id="dashPhotoModal" style="display: none;" class="fixed inset-0 bg-black/80 z-[110] items-center justify-center p-4">
        <div class="bg-white rounded-2xl overflow-hidden max-w-xl w-full shadow-2xl relative">
            <div class="p-4 border-b flex justify-between items-center bg-gray-50">
                <h4 id="dashPhotoTitle" class="text-sm font-bold text-gray-800">Pratinjau Foto</h4>
                <button type="button" onclick="closeDashboardPhoto()" class="text-gray-400 hover:text-gray-700 text-2xl font-bold w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-200 transition">&times;</button>
            </div>
            <div class="p-4 flex justify-center bg-black">
                <img id="dashPhotoImg" src="" class="max-h-[70vh] object-contain rounded">
            </div>
            <div class="p-3 bg-gray-50 border-t flex justify-end">
                <button type="button" onclick="closeDashboardPhoto()" class="btn-colorful px-4 py-2 bg-gradient-to-r from-gray-700 to-gray-800 text-white text-xs font-bold rounded-xl transition shadow">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.toggle('hidden', pane.id !== tabId);
            });
            document.querySelectorAll('[data-dashboard-tab]').forEach(button => {
                button.setAttribute('aria-pressed', String(button.getAttribute('aria-controls') === tabId));
            });
        }

        function filterPegawaiSearch() {
            const table = document.getElementById('tableSemuaPegawai');
            const search = document.getElementById('employeeSearchInput').value.toLowerCase().trim();
            let matches = 0;
            table.querySelectorAll('.pegawai-row').forEach(row => {
                const found = row.dataset.name.includes(search) || row.dataset.role.includes(search);
                row.style.display = found ? '' : 'none';
                if (found) matches++;
            });
            document.getElementById('noRowsFoundSemua')?.classList.toggle('hidden', matches > 0);
            table.dispatchEvent(new Event('table-filtered'));
        }

        function resetPegawaiSearch() {
            const search = document.getElementById('employeeSearchInput');
            search.value = '';
            filterPegawaiSearch();
            search.focus();
        }

        function showDashboardPhoto(src, title) {
            const modal = document.getElementById('dashPhotoModal');
            document.getElementById('dashPhotoImg').src = src;
            document.getElementById('dashPhotoTitle').innerText = title;
            modal.style.display = 'flex';
        }

        function closeDashboardPhoto() {
            const modal = document.getElementById('dashPhotoModal');
            modal.style.display = 'none';
        }

        function showEmployeeActivities(name, activities) {
            document.getElementById('actModalEmployeeName').innerText = "Kegiatan Monev: " + name;
            document.getElementById('actModalCount').innerText = "Total: " + activities.length + " dari maksimal 10 kegiatan tercatat hari ini";

            const container = document.getElementById('actModalContent');
            container.innerHTML = '';

            if (!activities || activities.length === 0) {
                container.innerHTML = '<div class="text-center py-8 text-gray-400 text-xs">Pegawai belum mencatat kegiatan pekerjaan hari ini.</div>';
            } else {
                activities.forEach((act, idx) => {
                    let photosHtml = '';
                    if (act.photos && act.photos.length > 0) {
                        photosHtml = `
                            <div class="pt-2">
                                <span class="font-semibold text-gray-600 text-xs block mb-1">Bukti Foto (${act.photos.length} Foto):</span>
                                <div class="flex flex-wrap gap-2">
                                    ${act.photos.map(p => `
                                        <img src="/storage/${p}" class="size-16 rounded-lg object-cover border-2 border-indigo-200 cursor-pointer hover:opacity-90 shadow-sm"
                                             onclick="showDashboardPhoto('/storage/${p}', 'Bukti Foto Kegiatan #${idx + 1}')">
                                    `).join('')}
                                </div>
                            </div>
                        `;
                    }

                    const card = document.createElement('div');
                    card.className = "bg-gray-50 rounded-xl p-3.5 border border-gray-200 text-xs space-y-1.5";
                    card.innerHTML = `
                        <div class="flex items-center justify-between pb-1 border-b border-gray-200">
                            <span class="font-bold px-2.5 py-0.5 rounded-lg bg-indigo-600 text-white">Kegiatan #${idx + 1}</span>
                            <span class="text-indigo-900 font-bold">Jam ${act.time ? act.time.substring(0, 5) : '-'} WIB</span>
                        </div>
                        <div class="text-gray-800">
                            <span class="font-bold text-gray-700">Uraian Tugas:</span>
                            <p class="mt-0.5 leading-relaxed bg-white p-2 rounded-lg border border-gray-200">${act.activity_description}</p>
                        </div>
                        ${act.obstacle && act.obstacle !== 'Tidak ada kendala' ? `
                            <div class="text-amber-900 bg-amber-50/70 p-2 rounded-lg border border-amber-200">
                                <span class="font-bold">Kendala:</span> ${act.obstacle}
                            </div>
                        ` : ''}
                        ${photosHtml}
                    `;
                    container.appendChild(card);
                });
            }

            document.getElementById('employeeActivitiesModal').style.display = 'flex';
        }

        function closeEmployeeActivities() {
            const modal = document.getElementById('employeeActivitiesModal');
            if (modal) modal.style.display = 'none';
        }

        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDashboardPhoto();
                closeEmployeeActivities();
            }
        });

        // Close on backdrop click
        document.getElementById('dashPhotoModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDashboardPhoto();
            }
        });

        document.getElementById('employeeActivitiesModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEmployeeActivities();
            }
        });
    </script>
</x-layouts.layout>
