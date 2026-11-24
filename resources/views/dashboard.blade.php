<x-layouts.layout :title="$title" :active="$active">
    <div class="max-w-7xl mx-auto py-6 px-4">
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-900 text-white rounded-2xl p-6 shadow-xl mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="inline-flex items-center gap-2 bg-blue-500/20 text-blue-200 text-xs px-3 py-1 rounded-full font-medium mb-2 backdrop-blur-sm border border-blue-400/30">
                    <span class="size-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Dashboard Monitoring Aktivitas Pegawai Non-ASN (Satpam, Driver, Kebersihan)
                </div>
                <h1 class="text-2xl font-bold">Aktivitas & Kehadiran Pegawai Hari Ini</h1>
                <p class="text-blue-200 text-sm mt-1">
                    Hari / Tanggal: <span class="font-semibold text-white">{{ $today }}</span> | Lokasi: {{ $office->office_name }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard.printToday') }}" target="_blank"
                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold shadow-lg flex items-center gap-2 transition hover:shadow-emerald-500/30">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Download PDF / Cetak Rekap
                </a>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <!-- Total Karyawan -->
            <div class="bg-white border rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between text-gray-500 text-xs font-bold uppercase tracking-wider">
                    <span>Total Pegawai Non-ASN</span>
                    <span class="p-2 rounded-xl bg-gray-100 text-gray-700">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </span>
                </div>
                <div class="text-3xl font-extrabold text-gray-900 mt-2">{{ $stats['total_karyawan'] }}</div>
                <div class="text-xs text-gray-400 mt-1">Satpam, Driver, Kebersihan</div>
            </div>

            <!-- Masuk / Hadir -->
            <div class="bg-white border border-emerald-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between text-emerald-700 text-xs font-bold uppercase tracking-wider">
                    <span>Masuk (Hadir)</span>
                    <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </span>
                </div>
                <div class="text-3xl font-extrabold text-emerald-700 mt-2">{{ $stats['total_masuk'] }}</div>
                <div class="text-xs text-emerald-600 mt-1">Sudah Absen Wajah Hari Ini</div>
            </div>

            <!-- Izin / Cuti / Sakit -->
            <div class="bg-white border border-blue-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between text-blue-700 text-xs font-bold uppercase tracking-wider">
                    <span>Izin / Sakit / Cuti</span>
                    <span class="p-2 rounded-xl bg-blue-50 text-blue-600">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </span>
                </div>
                <div class="text-3xl font-extrabold text-blue-700 mt-2">{{ $stats['total_izin'] }}</div>
                <div class="text-xs text-blue-600 mt-1">Izin Disetujui Atasan</div>
            </div>

            <!-- Tanpa Keterangan / Alpha -->
            <div class="bg-white border border-rose-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between text-rose-700 text-xs font-bold uppercase tracking-wider">
                    <span>Tanpa Keterangan</span>
                    <span class="p-2 rounded-xl bg-rose-50 text-rose-600">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </span>
                </div>
                <div class="text-3xl font-extrabold text-rose-700 mt-2">{{ $stats['total_tanpa_keterangan'] }}</div>
                <div class="text-xs text-rose-600 mt-1">Belum Hadir / Belum Ada Data</div>
            </div>
        </div>

        <!-- Filter Tab Buttons -->
        <div class="bg-white border rounded-2xl p-2 shadow-sm mb-6 flex flex-wrap gap-2">
            <button type="button" onclick="switchTab('tab-semua')" id="btn-tab-semua"
                class="tab-btn px-5 py-2.5 rounded-xl font-bold text-xs bg-slate-900 text-white transition flex items-center gap-2">
                <span>Semua Aktivitas</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-700">{{ $stats['total_karyawan'] }}</span>
            </button>

            <button type="button" onclick="switchTab('tab-masuk')" id="btn-tab-masuk"
                class="tab-btn px-5 py-2.5 rounded-xl font-bold text-xs bg-gray-100 text-gray-700 hover:bg-gray-200 transition flex items-center gap-2">
                <span class="size-2 rounded-full bg-emerald-500"></span>
                <span>Masuk (Hadir)</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-100 text-emerald-800">{{ $stats['total_masuk'] }}</span>
            </button>

            <button type="button" onclick="switchTab('tab-izin')" id="btn-tab-izin"
                class="tab-btn px-5 py-2.5 rounded-xl font-bold text-xs bg-gray-100 text-gray-700 hover:bg-gray-200 transition flex items-center gap-2">
                <span class="size-2 rounded-full bg-blue-500"></span>
                <span>Izin & Sakit</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-blue-100 text-blue-800">{{ $stats['total_izin'] }}</span>
            </button>

            <button type="button" onclick="switchTab('tab-alpha')" id="btn-tab-alpha"
                class="tab-btn px-5 py-2.5 rounded-xl font-bold text-xs bg-gray-100 text-gray-700 hover:bg-gray-200 transition flex items-center gap-2">
                <span class="size-2 rounded-full bg-rose-500"></span>
                <span>Tanpa Keterangan (Alpha)</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-rose-100 text-rose-800">{{ $stats['total_tanpa_keterangan'] }}</span>
            </button>
        </div>

        <!-- Tab 1: Semua Pegawai -->
        <div id="tab-semua" class="tab-pane block">
            <div class="bg-white border rounded-2xl shadow-sm overflow-hidden">
                <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 text-sm">Status Seluruh Pegawai Non-ASN Hari Ini</h3>
                    <span class="text-xs text-gray-500">{{ $today }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-sm">
                        <thead class="bg-gray-100 text-gray-700 text-xs uppercase font-bold">
                            <tr>
                                <th class="py-3 px-4">Pegawai</th>
                                <th class="py-3 px-4">Role / Jabatan</th>
                                <th class="py-3 px-4 text-center">Status Hari Ini</th>
                                <th class="py-3 px-4 text-center">Jam Masuk</th>
                                <th class="py-3 px-4 text-center">Jam Pulang</th>
                                <th class="py-3 px-4">Foto / Bukti</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            {{-- Masuk --}}
                            @foreach($listMasuk as $item)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-3.5 px-4 font-bold text-gray-900">{{ $item['employee']->user->name }}</td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                            {{ $item['employee']->role?->name }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $item['attendance']->check_in_status === 'tepat_waktu' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                            Hadir ({{ $item['status_label'] }})
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-semibold text-gray-800">
                                        {{ substr($item['attendance']->check_in_time, 0, 5) }} WIB
                                    </td>
                                    <td class="py-3.5 px-4 text-center text-gray-600">
                                        {{ $item['attendance']->check_out_time ? substr($item['attendance']->check_out_time, 0, 5) . ' WIB' : 'Belum Pulang' }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($item['attendance']->check_in_photo)
                                            <img src="{{ asset('storage/' . $item['attendance']->check_in_photo) }}"
                                                class="size-10 rounded-full object-cover border-2 border-emerald-500 cursor-pointer shadow hover:scale-110 transition"
                                                onclick="showDashboardPhoto('{{ asset('storage/' . $item['attendance']->check_in_photo) }}', 'Foto Masuk: {{ $item['employee']->user->name }}')">
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                            {{-- Izin --}}
                            @foreach($listIzin as $item)
                                <tr class="hover:bg-gray-50 transition bg-blue-50/30">
                                    <td class="py-3.5 px-4 font-bold text-gray-900">{{ $item['employee']->user->name }}</td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                            {{ $item['employee']->role?->name }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                            Izin / Sakit
                                        </span>
                                    </td>
                                    <td colspan="2" class="py-3.5 px-4 text-center text-xs text-blue-700 font-medium">
                                        Alasan: {{ Str::limit($item['reason'], 50) }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($item['attachment'])
                                            <button type="button" onclick="showDashboardPhoto('{{ asset('storage/' . $item['attachment']) }}', 'Surat Sakit: {{ $item['employee']->user->name }}')"
                                                class="text-xs text-purple-700 underline font-semibold flex items-center gap-1">
                                                Lihat Surat Sakit
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                            {{-- Alpha --}}
                            @foreach($listTanpaKeterangan as $item)
                                <tr class="hover:bg-gray-50 transition bg-rose-50/20">
                                    <td class="py-3.5 px-4 font-bold text-gray-900">{{ $item['employee']->user->name }}</td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                            {{ $item['employee']->role?->name }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                            Tanpa Keterangan
                                        </span>
                                    </td>
                                    <td colspan="2" class="py-3.5 px-4 text-center text-xs text-rose-600 italic">
                                        Belum melakukan presensi hari ini
                                    </td>
                                    <td class="py-3.5 px-4 text-xs text-gray-500">
                                        No. HP: {{ $item['employee']->phone_number ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab 2: Masuk / Hadir -->
        <div id="tab-masuk" class="tab-pane hidden">
            <div class="bg-white border rounded-2xl shadow-sm overflow-hidden">
                <div class="p-4 border-b bg-emerald-50/50 flex justify-between items-center">
                    <h3 class="font-bold text-emerald-900 text-sm">Daftar Pegawai yang Masuk / Hadir Hari Ini</h3>
                    <span class="text-xs font-bold text-emerald-700 bg-emerald-100 px-3 py-1 rounded-full">{{ count($listMasuk) }} Pegawai</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-sm">
                        <thead class="bg-gray-100 text-gray-700 text-xs uppercase font-bold">
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
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $item['attendance']->check_in_status === 'tepat_waktu' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
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
                                    <td class="py-3.5 px-4 text-xs max-w-xs truncate text-gray-600">
                                        {{ $item['attendance']->monev_activity ?? 'Belum mengisi monev pulang' }}
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

        <!-- Tab 3: Izin & Sakit -->
        <div id="tab-izin" class="tab-pane hidden">
            <div class="bg-white border rounded-2xl shadow-sm overflow-hidden">
                <div class="p-4 border-b bg-blue-50/50 flex justify-between items-center">
                    <h3 class="font-bold text-blue-900 text-sm">Daftar Pegawai yang Sedang Izin / Sakit Hari Ini</h3>
                    <span class="text-xs font-bold text-blue-700 bg-blue-100 px-3 py-1 rounded-full">{{ count($listIzin) }} Pegawai</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-sm">
                        <thead class="bg-gray-100 text-gray-700 text-xs uppercase font-bold">
                            <tr>
                                <th class="py-3 px-4">Pegawai</th>
                                <th class="py-3 px-4">Role</th>
                                <th class="py-3 px-4">Kategori Izin</th>
                                <th class="py-3 px-4">Alasan Izin</th>
                                <th class="py-3 px-4 text-center">Bukti Surat Sakit / Lampiran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($listIzin as $item)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-3.5 px-4 font-bold text-gray-900">{{ $item['employee']->user->name }}</td>
                                    <td class="py-3.5 px-4 text-xs font-semibold text-gray-600">{{ $item['employee']->role?->name }}</td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800">
                                            {{ ucfirst(str_replace('_', ' ', $item['category'])) }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-xs text-gray-700 max-w-sm">{{ $item['reason'] }}</td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($item['attachment'])
                                            <button type="button" onclick="showDashboardPhoto('{{ asset('storage/' . $item['attachment']) }}', 'Surat Sakit: {{ $item['employee']->user->name }}')"
                                                class="px-3 py-1 bg-purple-100 hover:bg-purple-200 text-purple-800 rounded-lg text-xs font-bold transition">
                                                Buka Surat Sakit
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Tidak ada lampiran</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-400 text-xs">Tidak ada pegawai yang izin atau sakit hari ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab 4: Tanpa Keterangan (Alpha) -->
        <div id="tab-alpha" class="tab-pane hidden">
            <div class="bg-white border rounded-2xl shadow-sm overflow-hidden">
                <div class="p-4 border-b bg-rose-50/50 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-rose-900 text-sm">Daftar Pegawai yang Belum Hadir / Tanpa Keterangan Hari Ini</h3>
                        <p class="text-xs text-rose-600">Dapat segera dihubungi untuk konfirmasi kehadiran atau penugasan lapangan.</p>
                    </div>
                    <span class="text-xs font-bold text-rose-700 bg-rose-100 px-3 py-1 rounded-full">{{ count($listTanpaKeterangan) }} Pegawai</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-sm">
                        <thead class="bg-gray-100 text-gray-700 text-xs uppercase font-bold">
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
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                            Belum Hadir
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($item['employee']->phone_number)
                                            <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $item['employee']->phone_number)) }}" target="_blank"
                                                class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-500 hover:bg-emerald-600 text-white rounded text-xs font-semibold shadow-sm transition">
                                                Hubungi WA
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
    </div>

    <!-- Modal Pratinjau Foto (Fixed Closing) -->
    <div id="dashPhotoModal" style="display: none;" class="fixed inset-0 bg-black/80 z-[100] items-center justify-center p-4">
        <div class="bg-white rounded-2xl overflow-hidden max-w-xl w-full shadow-2xl relative">
            <div class="p-4 border-b flex justify-between items-center bg-gray-50">
                <h4 id="dashPhotoTitle" class="text-sm font-bold text-gray-800">Pratinjau Foto</h4>
                <button type="button" onclick="closeDashboardPhoto()" class="text-gray-400 hover:text-gray-700 text-2xl font-bold w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-200 transition">&times;</button>
            </div>
            <div class="p-4 flex justify-center bg-black">
                <img id="dashPhotoImg" src="" class="max-h-[70vh] object-contain rounded">
            </div>
            <div class="p-3 bg-gray-50 border-t flex justify-end">
                <button type="button" onclick="closeDashboardPhoto()" class="px-4 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-bold rounded-lg transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-pane').forEach(el => el.classList.add('hidden'));
            document.getElementById(tabId).classList.remove('hidden');

            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.className = "tab-btn px-5 py-2.5 rounded-xl font-bold text-xs bg-gray-100 text-gray-700 hover:bg-gray-200 transition flex items-center gap-2";
            });

            const activeBtn = document.getElementById('btn-' + tabId);
            if (activeBtn) {
                activeBtn.className = "tab-btn px-5 py-2.5 rounded-xl font-bold text-xs bg-slate-900 text-white transition flex items-center gap-2";
            }
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

        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDashboardPhoto();
            }
        });

        // Close on backdrop click
        document.getElementById('dashPhotoModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDashboardPhoto();
            }
        });
    </script>
</x-layouts.layout>
