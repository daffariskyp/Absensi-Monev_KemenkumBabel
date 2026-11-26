<x-layouts.layout :title="$title" :active="$active">
    <div class="max-w-7xl mx-auto py-6 px-4">
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-900 text-white rounded-2xl p-6 shadow-xl mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="inline-flex items-center gap-2 bg-blue-500/25 text-blue-200 text-xs px-3.5 py-1.5 rounded-full font-semibold mb-2.5 backdrop-blur-sm border border-blue-400/30">
                    <span class="size-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Dashboard Monitoring Aktivitas Pegawai Non-ASN (Satpam, Driver, Kebersihan)
                </div>
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
                    Download PDF / Cetak Rekap
                </a>
            </div>
        </div>

        <!-- Banner Pengajuan Cuti Pending (SweetAlert Triggers & Visual Banner) -->
        @if(isset($stats['total_pending_cuti']) && $stats['total_pending_cuti'] > 0)
            <div class="bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600 text-white rounded-2xl p-4 mb-6 shadow-lg flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-2 border-yellow-300 animate-pulse-subtle">
                <div class="flex items-center gap-3">
                    <span class="p-2.5 bg-white/20 rounded-xl text-white backdrop-blur-sm">
                        <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </span>
                    <div>
                        <div class="font-extrabold text-base flex items-center gap-2">
                            <span>Perhatian Admin: Ada {{ $stats['total_pending_cuti'] }} Pengajuan Cuti / Izin Menunggu Persetujuan!</span>
                            <span class="px-2 py-0.5 rounded-full text-xs bg-white text-orange-700 font-black">BARU</span>
                        </div>
                        <p class="text-amber-100 text-xs mt-0.5">Pegawai Non-ASN telah mengajukan permohonan izin/cuti yang memerlukan tindakan persetujuan atasan.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('administration.index', ['status' => 'pending']) }}"
                        class="btn-colorful px-4 py-2 bg-white text-orange-800 hover:bg-yellow-50 rounded-xl text-xs font-black shadow-md transition flex items-center gap-1.5 border-2 border-yellow-400">
                        <svg class="size-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        Tinjau Pengajuan Sekarang
                    </a>
                </div>
            </div>
        @endif

        <!-- Metric Stat Cards (Total Pegawai dengan Fitur Filter Langsung) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-3 mb-6">
            <!-- 1. Total Pegawai (Interaktif Filter: Masuk, Telat, Sakit, Alpha, Izin) -->
            <div class="bg-gradient-to-br from-white to-blue-50/50 border-2 border-blue-300 hover:border-blue-500 rounded-2xl p-4 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between text-blue-900 text-xs font-extrabold uppercase tracking-wider">
                    <span>Total Pegawai</span>
                    <span class="p-1.5 rounded-xl bg-blue-100 text-blue-700">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-gray-900 mt-2">{{ $stats['total_karyawan'] }} <span class="text-xs font-semibold text-gray-500">Orang</span></div>
                
                <!-- Quick Filter Badges di dalam kartu Total Pegawai -->
                <div class="mt-2 pt-2 border-t border-blue-100 flex flex-col gap-1">
                    <span class="text-[10px] font-bold text-gray-500 uppercase">Filter Cepat:</span>
                    <div class="flex flex-wrap gap-1">
                        <button type="button" onclick="filterPegawaiInline('masuk')"
                            class="px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800 hover:bg-emerald-200 border border-emerald-300 transition flex items-center gap-1"
                            title="Filter siapa yang masuk hari ini">
                            <span class="size-1.5 rounded-full bg-emerald-500"></span> Masuk: {{ $stats['total_masuk'] }}
                        </button>
                        <button type="button" onclick="filterPegawaiInline('telat')"
                            class="px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-800 hover:bg-amber-200 border border-amber-300 transition flex items-center gap-1"
                            title="Filter siapa yang telat hari ini">
                            <span class="size-1.5 rounded-full bg-amber-500"></span> Telat: {{ $stats['total_telat'] }}
                        </button>
                        <button type="button" onclick="filterPegawaiInline('sakit')"
                            class="px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-pink-100 text-pink-800 hover:bg-pink-200 border border-pink-300 transition flex items-center gap-1"
                            title="Filter siapa yang sakit hari ini">
                            <span class="size-1.5 rounded-full bg-pink-500"></span> Sakit: {{ $stats['total_sakit'] }}
                        </button>
                        <button type="button" onclick="filterPegawaiInline('alpha')"
                            class="px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-rose-100 text-rose-800 hover:bg-rose-200 border border-rose-300 transition flex items-center gap-1"
                            title="Filter siapa yang tidak ada keterangan">
                            <span class="size-1.5 rounded-full bg-rose-500"></span> Alpha: {{ $stats['total_tanpa_keterangan'] }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- 2. Masuk / Hadir (Total yang Hadir Hari Ini) -->
            <div class="bg-gradient-to-br from-white to-emerald-50/50 border-2 border-emerald-300 hover:border-emerald-500 rounded-2xl p-4 shadow-sm hover:shadow-md transition cursor-pointer" onclick="filterPegawaiInline('masuk')">
                <div class="flex items-center justify-between text-emerald-800 text-xs font-extrabold uppercase tracking-wider">
                    <span>Masuk (Hadir)</span>
                    <span class="p-1.5 rounded-xl bg-emerald-100 text-emerald-700">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-emerald-700 mt-2">{{ $stats['total_masuk'] }} <span class="text-xs font-semibold text-emerald-600">Pegawai</span></div>
                <div class="text-[11px] text-emerald-600 mt-1 font-medium flex items-center gap-1">
                    <span class="size-2 rounded-full bg-emerald-500"></span> {{ $stats['total_tepat_waktu'] }} Tepat Waktu
                </div>
            </div>

            <!-- 3. Terlambat / Telat -->
            <div class="bg-gradient-to-br from-white to-amber-50/50 border-2 border-amber-300 hover:border-amber-500 rounded-2xl p-4 shadow-sm hover:shadow-md transition cursor-pointer" onclick="filterPegawaiInline('telat')">
                <div class="flex items-center justify-between text-amber-800 text-xs font-extrabold uppercase tracking-wider">
                    <span>Terlambat (Telat)</span>
                    <span class="p-1.5 rounded-xl bg-amber-100 text-amber-700">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-amber-700 mt-2">{{ $stats['total_telat'] }} <span class="text-xs font-semibold text-amber-600">Pegawai</span></div>
                <div class="text-[11px] text-amber-600 mt-1 font-medium flex items-center gap-1">
                    <span class="size-2 rounded-full bg-amber-500"></span> Absen Masuk Lewat Jam
                </div>
            </div>

            <!-- 4. Sakit (Surat Dokter) -->
            <div class="bg-gradient-to-br from-white to-pink-50/50 border-2 border-pink-300 hover:border-pink-500 rounded-2xl p-4 shadow-sm hover:shadow-md transition cursor-pointer" onclick="filterPegawaiInline('sakit')">
                <div class="flex items-center justify-between text-pink-800 text-xs font-extrabold uppercase tracking-wider">
                    <span>Sakit (Dokter)</span>
                    <span class="p-1.5 rounded-xl bg-pink-100 text-pink-700">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-pink-700 mt-2">{{ $stats['total_sakit'] }} <span class="text-xs font-semibold text-pink-600">Pegawai</span></div>
                <div class="text-[11px] text-pink-600 mt-1 font-medium flex items-center gap-1">
                    <span class="size-2 rounded-full bg-pink-500"></span> Surat Dokter Resmi
                </div>
            </div>

            <!-- 5. Izin & Cuti -->
            <div class="bg-gradient-to-br from-white to-purple-50/50 border-2 border-purple-300 hover:border-purple-500 rounded-2xl p-4 shadow-sm hover:shadow-md transition cursor-pointer" onclick="filterPegawaiInline('izin')">
                <div class="flex items-center justify-between text-purple-800 text-xs font-extrabold uppercase tracking-wider">
                    <span>Izin & Cuti</span>
                    <span class="p-1.5 rounded-xl bg-purple-100 text-purple-700">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-purple-700 mt-2">{{ $stats['total_izin'] }} <span class="text-xs font-semibold text-purple-600">Pegawai</span></div>
                <div class="text-[11px] text-purple-600 mt-1 font-medium flex items-center gap-1">
                    <span class="size-2 rounded-full bg-purple-500"></span> Disetujui Atasan
                </div>
            </div>

            <!-- 6. Tanpa Keterangan / Alpha -->
            <div class="bg-gradient-to-br from-white to-rose-50/50 border-2 border-rose-300 hover:border-rose-500 rounded-2xl p-4 shadow-sm hover:shadow-md transition cursor-pointer" onclick="filterPegawaiInline('alpha')">
                <div class="flex items-center justify-between text-rose-800 text-xs font-extrabold uppercase tracking-wider">
                    <span>Tanpa Keterangan</span>
                    <span class="p-1.5 rounded-xl bg-rose-100 text-rose-700">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-rose-700 mt-2">{{ $stats['total_tanpa_keterangan'] }} <span class="text-xs font-semibold text-rose-600">Pegawai</span></div>
                <div class="text-[11px] text-rose-600 mt-1 font-medium flex items-center gap-1">
                    <span class="size-2 rounded-full bg-rose-500"></span> Belum Presensi / Alpha
                </div>
            </div>

            <!-- 7. Monev Kegiatan -->
            <div class="bg-gradient-to-br from-white to-indigo-50/50 border-2 border-indigo-300 hover:border-indigo-500 rounded-2xl p-4 shadow-sm hover:shadow-md transition cursor-pointer" onclick="switchTab('tab-monev')">
                <div class="flex items-center justify-between text-indigo-800 text-xs font-extrabold uppercase tracking-wider">
                    <span>Kegiatan Monev</span>
                    <span class="p-1.5 rounded-xl bg-indigo-100 text-indigo-700">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-indigo-700 mt-2">{{ $stats['total_monev_kegiatan'] }} <span class="text-xs font-semibold text-indigo-600">Tugas</span></div>
                <div class="text-[11px] text-indigo-600 mt-1 font-medium flex items-center gap-1">
                    <span class="size-2 rounded-full bg-indigo-500"></span> Laporan Lapangan
                </div>
            </div>
        </div>

        <!-- Filter Tab Buttons: Berwarna Cerah & Menarik (Biru dengan Border Kuning saat Aktif / Diklik) -->
        <div class="bg-white border-2 border-gray-200 rounded-2xl p-2.5 shadow-sm mb-6 flex flex-wrap gap-2 items-center">
            <!-- Tab Semua Pegawai -->
            <button type="button" onclick="switchTab('tab-semua'); filterPegawaiTable('all');" id="btn-tab-semua"
                class="tab-btn px-4 py-2.5 rounded-xl font-bold text-xs bg-blue-50 text-blue-800 hover:bg-blue-100 border-2 border-blue-200 transition flex items-center gap-2">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                <span>Semua Pegawai</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] bg-blue-200 text-blue-900 font-extrabold">{{ $stats['total_karyawan'] }}</span>
            </button>

            <!-- Tab Masuk (Hadir) -->
            <button type="button" onclick="switchTab('tab-masuk')" id="btn-tab-masuk"
                class="tab-btn px-4 py-2.5 rounded-xl font-bold text-xs bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border-2 border-emerald-300 transition flex items-center gap-2">
                <span class="size-2 rounded-full bg-emerald-500"></span>
                <span>Masuk (Hadir)</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] bg-emerald-200 text-emerald-900 font-extrabold">{{ $stats['total_masuk'] }}</span>
            </button>

            <!-- Tab Terlambat / Telat (Spesifik) -->
            <button type="button" onclick="switchTab('tab-telat')" id="btn-tab-telat"
                class="tab-btn px-4 py-2.5 rounded-xl font-bold text-xs bg-amber-50 text-amber-800 hover:bg-amber-100 border-2 border-amber-300 transition flex items-center gap-2">
                <span class="size-2 rounded-full bg-amber-500"></span>
                <span>Terlambat (Telat)</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] bg-amber-200 text-amber-900 font-extrabold">{{ $stats['total_telat'] }}</span>
            </button>

            <!-- Tab Sakit (Surat Dokter) -->
            <button type="button" onclick="switchTab('tab-sakit')" id="btn-tab-sakit"
                class="tab-btn px-4 py-2.5 rounded-xl font-bold text-xs bg-pink-50 text-pink-800 hover:bg-pink-100 border-2 border-pink-300 transition flex items-center gap-2">
                <span class="size-2 rounded-full bg-pink-500"></span>
                <span>Sakit (Dokter)</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] bg-pink-200 text-pink-900 font-extrabold">{{ $stats['total_sakit'] }}</span>
            </button>

            <!-- Tab Izin & Cuti -->
            <button type="button" onclick="switchTab('tab-izin')" id="btn-tab-izin"
                class="tab-btn px-4 py-2.5 rounded-xl font-bold text-xs bg-purple-50 text-purple-800 hover:bg-purple-100 border-2 border-purple-300 transition flex items-center gap-2">
                <span class="size-2 rounded-full bg-purple-500"></span>
                <span>Izin & Cuti</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] bg-purple-200 text-purple-900 font-extrabold">{{ $stats['total_izin'] }}</span>
            </button>

            <!-- Tab Tanpa Keterangan (Alpha) -->
            <button type="button" onclick="switchTab('tab-alpha')" id="btn-tab-alpha"
                class="tab-btn px-4 py-2.5 rounded-xl font-bold text-xs bg-rose-50 text-rose-800 hover:bg-rose-100 border-2 border-rose-300 transition flex items-center gap-2">
                <span class="size-2 rounded-full bg-rose-500"></span>
                <span>Tanpa Keterangan (Alpha)</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] bg-rose-200 text-rose-900 font-extrabold">{{ $stats['total_tanpa_keterangan'] }}</span>
            </button>

            <!-- Tab Laporan Monev -->
            <button type="button" onclick="switchTab('tab-monev')" id="btn-tab-monev"
                class="tab-btn px-4 py-2.5 rounded-xl font-bold text-xs bg-indigo-50 text-indigo-800 hover:bg-indigo-100 border-2 border-indigo-300 transition flex items-center gap-2">
                <span class="size-2 rounded-full bg-indigo-500 animate-pulse"></span>
                <span>Laporan Monev</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] bg-indigo-200 text-indigo-900 font-extrabold">{{ $stats['total_monev_kegiatan'] }}</span>
            </button>
        </div>

        <!-- ========================================================================= -->
        <!-- Tab 1: Semua Pegawai (Dengan In-Table Filtering: Telat, Masuk, Sakit, Alpha, Izin) -->
        <!-- ========================================================================= -->
        <div id="tab-semua" class="tab-pane block">
            <div class="bg-white border-2 border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <!-- Bar Filter & Pencarian Pegawai -->
                <div class="p-4 border-b bg-gray-50">
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3">
                        <div class="flex-shrink-0">
                            <h3 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                                <span>Status Seluruh Pegawai Non-ASN</span>
                                <span id="activeFilterBadge" class="text-xs px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 font-bold border border-blue-300">Semua Pegawai ({{ $stats['total_karyawan'] }})</span>
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">Filter instan siapa yang masuk, siapa yang telat, sakit, atau belum ada keterangan.</p>
                        </div>

                    <!-- Tombol Filter Cepat Berwarna (Bukan Pagination Kotak Abu) -->
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="relative">
                            <input type="text" id="employeeSearchInput" onkeyup="filterPegawaiSearch()" placeholder="Cari nama pegawai..."
                                class="pl-8 pr-3 py-1.5 text-xs rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white shadow-sm">
                            <svg class="size-4 text-gray-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>

                        <!-- Chip Filter Berwarna Permanen -->
                        <button type="button" onclick="filterPegawaiTable('all')" id="pill-filter-all"
                            style="background-color: #2563eb; color: #ffffff !important; border: 2px solid #facc15;"
                            class="pill-btn px-3.5 py-1.5 rounded-xl font-black text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer whitespace-nowrap">
                            <span class="flex-shrink-0">Semua Pegawai</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-white/20 text-white font-extrabold flex-shrink-0">{{ $stats['total_karyawan'] }}</span>
                        </button>
                        <button type="button" onclick="filterPegawaiTable('masuk')" id="pill-filter-masuk"
                            style="background-color: #ecfdf5; color: #065f46; border: 2px solid #a7f3d0;"
                            class="pill-btn px-3 py-1.5 rounded-xl font-bold text-xs hover:opacity-90 transition flex items-center gap-1.5 cursor-pointer whitespace-nowrap">
                            <span class="size-2 rounded-full bg-emerald-500 flex-shrink-0"></span>
                            <span class="flex-shrink-0">Masuk</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-emerald-200 text-emerald-950 font-bold flex-shrink-0">{{ $stats['total_masuk'] }}</span>
                        </button>
                        <button type="button" onclick="filterPegawaiTable('telat')" id="pill-filter-telat"
                            style="background-color: #fffbeb; color: #78350f; border: 2px solid #fde68a;"
                            class="pill-btn px-3 py-1.5 rounded-xl font-bold text-xs hover:opacity-90 transition flex items-center gap-1.5 cursor-pointer whitespace-nowrap">
                            <span class="size-2 rounded-full bg-amber-500 flex-shrink-0"></span>
                            <span class="flex-shrink-0">Telat</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-amber-200 text-amber-950 font-bold flex-shrink-0">{{ $stats['total_telat'] }}</span>
                        </button>
                        <button type="button" onclick="filterPegawaiTable('sakit')" id="pill-filter-sakit"
                            style="background-color: #fdf2f8; color: #831843; border: 2px solid #fbcfe8;"
                            class="pill-btn px-3 py-1.5 rounded-xl font-bold text-xs hover:opacity-90 transition flex items-center gap-1.5 cursor-pointer whitespace-nowrap">
                            <span class="size-2 rounded-full bg-pink-500 flex-shrink-0"></span>
                            <span class="flex-shrink-0">Sakit</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-pink-200 text-pink-950 font-bold flex-shrink-0">{{ $stats['total_sakit'] }}</span>
                        </button>
                        <button type="button" onclick="filterPegawaiTable('izin')" id="pill-filter-izin"
                            style="background-color: #faf5ff; color: #581c87; border: 2px solid #e9d5ff;"
                            class="pill-btn px-3 py-1.5 rounded-xl font-bold text-xs hover:opacity-90 transition flex items-center gap-1.5 cursor-pointer whitespace-nowrap">
                            <span class="size-2 rounded-full bg-purple-500 flex-shrink-0"></span>
                            <span class="flex-shrink-0">Izin / Cuti</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-purple-200 text-purple-950 font-bold flex-shrink-0">{{ $stats['total_izin'] }}</span>
                        </button>
                        <button type="button" onclick="filterPegawaiTable('alpha')" id="pill-filter-alpha"
                            style="background-color: #fff1f2; color: #881337; border: 2px solid #fecdd3;"
                            class="pill-btn px-3 py-1.5 rounded-xl font-bold text-xs hover:opacity-90 transition flex items-center gap-1.5 cursor-pointer whitespace-nowrap">
                            <span class="size-2 rounded-full bg-rose-500 flex-shrink-0"></span>
                            <span class="flex-shrink-0">Alpha</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-rose-200 text-rose-950 font-bold flex-shrink-0">{{ $stats['total_tanpa_keterangan'] }}</span>
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-sm" id="tableSemuaPegawai">
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
                                    $statusCategory = $isTelat ? 'telat' : 'masuk';
                                @endphp
                                <tr class="pegawai-row hover:bg-gray-50 transition {{ $isTelat ? 'bg-amber-50/30' : '' }}" data-status="{{ $statusCategory }}" data-is-masuk="true" data-name="{{ strtolower($item['employee']->user->name) }}" data-role="{{ strtolower($item['employee']->role?->name ?? '') }}">
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
                                <tr class="pegawai-row hover:bg-gray-50 transition bg-pink-50/25" data-status="sakit" data-is-masuk="false" data-name="{{ strtolower($item['employee']->user->name) }}" data-role="{{ strtolower($item['employee']->role?->name ?? '') }}">
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
                                <tr class="pegawai-row hover:bg-gray-50 transition bg-purple-50/25" data-status="izin" data-is-masuk="false" data-name="{{ strtolower($item['employee']->user->name) }}" data-role="{{ strtolower($item['employee']->role?->name ?? '') }}">
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
                                <tr class="pegawai-row hover:bg-gray-50 transition bg-rose-50/20" data-status="alpha" data-is-masuk="false" data-name="{{ strtolower($item['employee']->user->name) }}" data-role="{{ strtolower($item['employee']->role?->name ?? '') }}">
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

                            <tr id="noRowsFoundSemua" class="hidden">
                                <td colspan="7" class="py-12 text-center">
                                    <div class="max-w-md mx-auto bg-gray-50 border border-gray-200 rounded-2xl p-6 shadow-sm">
                                        <svg class="size-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <p id="noRowsText" class="text-xs font-bold text-gray-700">Tidak ada data pegawai yang sesuai dengan filter ini.</p>
                                        <p class="text-[11px] text-gray-500 mt-1">Seluruh pegawai lainnya tercatat pada kategori presensi lain hari ini.</p>
                                        <button type="button" onclick="filterPegawaiTable('all')" class="mt-3 px-4 py-1.5 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white border-2 border-yellow-300 shadow transition inline-flex items-center gap-1.5 cursor-pointer">
                                            Tampilkan Semua Pegawai
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
                    <table class="w-full border-collapse text-left text-sm">
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
                    <table class="w-full border-collapse text-left text-sm">
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
                    <table class="w-full border-collapse text-left text-sm">
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
                    <table class="w-full border-collapse text-left text-sm">
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
                    <table class="w-full border-collapse text-left text-sm">
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
        // SweetAlert ketika ada pengajuan cuti baru bagi Admin
        document.addEventListener('DOMContentLoaded', function() {
            @if(isset($stats['total_pending_cuti']) && $stats['total_pending_cuti'] > 0)
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: '<span style="color:#1e3a8a; font-weight:800;">Pengajuan Cuti Menunggu Persetujuan!</span>',
                        html: `
                            <div style="text-align: left; font-size: 13px; color: #374151; line-height: 1.6;">
                                <p style="margin-bottom: 8px;">Terdapat <b style="color: #2563eb; font-size: 16px;">{{ $stats['total_pending_cuti'] }}</b> pengajuan cuti/izin pegawai Non-ASN baru yang belum diproses oleh atasan.</p>
                                <div style="background: #eff6ff; padding: 10px 14px; border-radius: 12px; border: 1px solid #bfdbfe; margin-top: 10px;">
                                    <div style="font-weight: 700; color: #1e40af; font-size: 12px; text-transform: uppercase;">Perhatian:</div>
                                    <div style="color: #1f2937; margin-top: 2px;">Silakan tinjau dan berikan persetujuan atau penolakan pada dashboard administrasi.</div>
                                </div>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Tinjau Pengajuan Sekarang',
                        cancelButtonText: 'Nanti Saja',
                        confirmButtonColor: '#2563eb',
                        cancelButtonColor: '#64748b',
                        customClass: {
                            popup: 'rounded-2xl shadow-2xl border border-blue-100',
                            confirmButton: 'px-5 py-2.5 rounded-xl font-bold border-2 border-yellow-400 shadow-md text-white transition active:scale-95',
                            cancelButton: 'px-4 py-2.5 rounded-xl font-semibold transition active:scale-95'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('administration.index', ['status' => 'pending']) }}";
                        }
                    });
                }
            @endif
        });

        // Tab Switching Logic dengan Styling Aktif: Biru dengan border agak kuning
        function switchTab(tabId) {
            document.querySelectorAll('.tab-pane').forEach(el => el.classList.add('hidden'));
            const targetPane = document.getElementById(tabId);
            if (targetPane) targetPane.classList.remove('hidden');

            // Reset semua tab buttons ke style berwarna awal
            const defaultStyles = {
                'btn-tab-semua': 'tab-btn px-4 py-2.5 rounded-xl font-bold text-xs bg-blue-50 text-blue-800 hover:bg-blue-100 border-2 border-blue-200 transition flex items-center gap-2',
                'btn-tab-masuk': 'tab-btn px-4 py-2.5 rounded-xl font-bold text-xs bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border-2 border-emerald-300 transition flex items-center gap-2',
                'btn-tab-telat': 'tab-btn px-4 py-2.5 rounded-xl font-bold text-xs bg-amber-50 text-amber-800 hover:bg-amber-100 border-2 border-amber-300 transition flex items-center gap-2',
                'btn-tab-sakit': 'tab-btn px-4 py-2.5 rounded-xl font-bold text-xs bg-pink-50 text-pink-800 hover:bg-pink-100 border-2 border-pink-300 transition flex items-center gap-2',
                'btn-tab-izin': 'tab-btn px-4 py-2.5 rounded-xl font-bold text-xs bg-purple-50 text-purple-800 hover:bg-purple-100 border-2 border-purple-300 transition flex items-center gap-2',
                'btn-tab-alpha': 'tab-btn px-4 py-2.5 rounded-xl font-bold text-xs bg-rose-50 text-rose-800 hover:bg-rose-100 border-2 border-rose-300 transition flex items-center gap-2',
                'btn-tab-monev': 'tab-btn px-4 py-2.5 rounded-xl font-bold text-xs bg-indigo-50 text-indigo-800 hover:bg-indigo-100 border-2 border-indigo-300 transition flex items-center gap-2',
            };

            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active-tab');
                if (defaultStyles[btn.id]) {
                    btn.className = defaultStyles[btn.id];
                }
            });

            // Set tab aktif menjadi BIRU dengan BORDER AGAK KUNING
            const activeBtn = document.getElementById('btn-' + tabId);
            if (activeBtn) {
                activeBtn.className = "tab-btn active-tab px-4 py-2.5 rounded-xl font-bold text-xs bg-blue-600 text-white border-2 border-yellow-400 shadow-lg ring-2 ring-yellow-400/30 transition flex items-center gap-2";
            }
        }

        // Set default tab aktif saat awal dimuat
        document.addEventListener('DOMContentLoaded', function() {
            switchTab('tab-semua');
        });

        // Filter cepat dari kartu Total Pegawai
        function filterPegawaiInline(status) {
            switchTab('tab-semua');
            filterPegawaiTable(status);
        }

        // Filter tabel pegawai secara langsung (Client-side)
        function filterPegawaiTable(status) {
            currentActiveStatus = status;
            const rows = document.querySelectorAll('#tableSemuaPegawai .pegawai-row');
            const searchVal = (document.getElementById('employeeSearchInput')?.value || '').toLowerCase().trim();
            let visibleCount = 0;

            rows.forEach(row => {
                const rowStatus = row.getAttribute('data-status');
                const isMasuk = row.getAttribute('data-is-masuk') === 'true';
                const rowName = row.getAttribute('data-name') || '';
                const rowRole = row.getAttribute('data-role') || '';

                let matchesStatus = false;
                if (status === 'all') {
                    matchesStatus = true;
                } else if (status === 'masuk') {
                    matchesStatus = isMasuk; // SEMUA yang hadir (tepat waktu maupun telat)
                } else if (status === 'telat') {
                    matchesStatus = (rowStatus === 'telat');
                } else if (status === 'sakit') {
                    matchesStatus = (rowStatus === 'sakit');
                } else if (status === 'izin') {
                    matchesStatus = (rowStatus === 'izin');
                } else if (status === 'alpha') {
                    matchesStatus = (rowStatus === 'alpha');
                } else {
                    matchesStatus = (rowStatus === status);
                }

                const matchesSearch = !searchVal || rowName.includes(searchVal) || rowRole.includes(searchVal);

                if (matchesStatus && matchesSearch) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            const noRowEl = document.getElementById('noRowsFoundSemua');
            if (noRowEl) {
                noRowEl.classList.toggle('hidden', visibleCount > 0);
            }

            // Update badge dan styling tombol filter
            const badge = document.getElementById('activeFilterBadge');
            const statusLabels = {
                'all': 'Semua Pegawai (' + visibleCount + ')',
                'masuk': 'Pegawai Masuk / Hadir (' + visibleCount + ')',
                'telat': 'Pegawai Terlambat / Telat (' + visibleCount + ')',
                'sakit': 'Pegawai Sakit / Surat Dokter (' + visibleCount + ')',
                'izin': 'Pegawai Izin & Cuti (' + visibleCount + ')',
                'alpha': 'Pegawai Tanpa Keterangan (' + visibleCount + ')'
            };
            if (badge && statusLabels[status]) {
                badge.innerText = statusLabels[status];
            }

            // Update styling tombol filter chip: Setiap tombol mempertahankan warnanya masing-masing dengan inline style
            const pillConfigs = {
                'all': {
                    activeBg: '#2563eb',
                    activeColor: '#ffffff',
                    activeBorder: '#facc15',
                    normalBg: '#eff6ff',
                    normalColor: '#1e40af',
                    normalBorder: '#bfdbfe',
                },
                'masuk': {
                    activeBg: '#059669',
                    activeColor: '#ffffff',
                    activeBorder: '#facc15',
                    normalBg: '#ecfdf5',
                    normalColor: '#065f46',
                    normalBorder: '#a7f3d0',
                },
                'telat': {
                    activeBg: '#d97706',
                    activeColor: '#ffffff',
                    activeBorder: '#facc15',
                    normalBg: '#fffbeb',
                    normalColor: '#78350f',
                    normalBorder: '#fde68a',
                },
                'sakit': {
                    activeBg: '#db2777',
                    activeColor: '#ffffff',
                    activeBorder: '#facc15',
                    normalBg: '#fdf2f8',
                    normalColor: '#831843',
                    normalBorder: '#fbcfe8',
                },
                'izin': {
                    activeBg: '#7c3aed',
                    activeColor: '#ffffff',
                    activeBorder: '#facc15',
                    normalBg: '#faf5ff',
                    normalColor: '#581c87',
                    normalBorder: '#e9d5ff',
                },
                'alpha': {
                    activeBg: '#e11d48',
                    activeColor: '#ffffff',
                    activeBorder: '#facc15',
                    normalBg: '#fff1f2',
                    normalColor: '#881337',
                    normalBorder: '#fecdd3',
                }
            };

            for (const key in pillConfigs) {
                const el = document.getElementById('pill-filter-' + key);
                if (el) {
                    const cfg = pillConfigs[key];
                    const isActive = (key === status);
                    el.style.backgroundColor = isActive ? cfg.activeBg : cfg.normalBg;
                    el.style.color = isActive ? cfg.activeColor : cfg.normalColor;
                    el.style.borderColor = isActive ? cfg.activeBorder : cfg.normalBorder;
                    el.style.boxShadow = isActive ? '0 4px 12px rgba(0,0,0,0.2)' : 'none';
                    el.style.fontWeight = isActive ? '900' : '700';
                    el.style.transform = isActive ? 'scale(1.05)' : 'scale(1)';
                }
            }
        }

        let currentActiveStatus = 'all';
        function filterPegawaiSearch() {
            filterPegawaiTable(currentActiveStatus);
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