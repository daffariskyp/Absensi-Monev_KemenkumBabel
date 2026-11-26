<x-layouts.layout :title="$title" :active="$active">
    <div class="max-w-6xl mx-auto py-6 px-4">
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-blue-700 to-indigo-800 text-white rounded-2xl p-6 shadow-lg mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="inline-flex items-center gap-2 bg-blue-500/30 text-blue-100 text-xs px-3 py-1 rounded-full font-medium mb-2 backdrop-blur-sm">
                    <span class="size-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Sistem Presensi & Monev Pegawai Non-ASN
                </div>
                <h1 class="text-2xl font-bold">Selamat Datang, {{ $user->name }}</h1>
                <p class="text-blue-100 text-sm mt-1">
                    Jabatan / Role: <span class="font-bold underline">{{ $user->employee?->role?->name ?? ($user->role === 'supervisor' ? 'Kabag / Koordinator' : ($user->role === 'admin' ? 'Administrator' : 'Tenaga Non-ASN')) }}</span> | Hari: {{ $today }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('administration.create') }}" 
                    style="background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); color: #ffffff !important; border: 2px solid #facc15; box-shadow: 0 4px 14px rgba(234, 88, 12, 0.4);"
                    class="btn-colorful px-5 py-2.5 hover:opacity-95 text-white rounded-xl text-xs sm:text-sm font-extrabold transition flex items-center gap-2 transform active:scale-95 active:shadow-inner cursor-pointer">
                    <svg class="size-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Ajukan Izin / Cuti</span>
                </a>
                <a href="{{ route('attendance.recap') }}" 
                    style="background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%); color: #ffffff !important; border: 1px solid rgba(255,255,255,0.3);"
                    class="btn-colorful px-4 py-2.5 hover:opacity-95 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md hover:shadow-lg transition flex items-center gap-2 transform active:scale-95 cursor-pointer">
                    <svg class="size-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Lihat Rekap Absensi</span>
                </a>
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('attendance.settings') }}" class="px-3 py-2.5 bg-blue-950/70 hover:bg-blue-900 text-white rounded-xl text-sm font-semibold border border-white/20 shadow-md transition flex items-center gap-1.5" title="Pengaturan Lokasi Kantor">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </a>
                @endif
            </div>
        </div>

        {{-- Status Izin / Cuti Khusus untuk Pegawai Hari Ini --}}
        @if(isset($todayApprovedLeave) && $todayApprovedLeave)
            <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 text-white rounded-2xl p-4 sm:p-5 shadow-lg mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-2 border-emerald-300">
                <div class="flex items-center gap-3">
                    <span class="p-3 bg-white/20 rounded-2xl text-white backdrop-blur-md">
                        <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </span>
                    <div>
                        <div class="font-extrabold text-base flex items-center gap-2">
                            <span>Status Hari Ini: Anda Sedang Dalam Masa Izin Resmi</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs bg-white text-emerald-800 font-black">DISETUJUI</span>
                        </div>
                        <p class="text-emerald-100 text-xs sm:text-sm mt-0.5">
                            Permohonan: <strong class="text-white">{{ ucfirst(str_replace('_', ' ', $todayApprovedLeave->leave_category)) }}</strong> 
                            ({{ \Carbon\Carbon::parse($todayApprovedLeave->start_date)->translatedFormat('d M') }} s/d {{ \Carbon\Carbon::parse($todayApprovedLeave->end_date)->translatedFormat('d M Y') }}). Anda tidak dikenakan sanksi absensi.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('administration.print', $todayApprovedLeave->id) }}" target="_blank"
                        class="btn-colorful px-4 py-2 bg-white text-emerald-900 hover:bg-emerald-50 rounded-xl text-xs font-black shadow-md transition flex items-center gap-1.5 border-2 border-emerald-300">
                        <svg class="size-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                        Download Bukti Cuti (TTD QR)
                    </a>
                </div>
            </div>
        @elseif(isset($userPendingLeave) && $userPendingLeave)
            <div class="bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600 text-white rounded-2xl p-4 sm:p-5 shadow-lg mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-2 border-yellow-300">
                <div class="flex items-center gap-3">
                    <span class="p-3 bg-white/20 rounded-2xl text-white backdrop-blur-md">
                        <svg class="size-6 animate-spin" style="animation-duration: 3s;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </span>
                    <div>
                        <div class="font-extrabold text-base flex items-center gap-2">
                            <span>Pengajuan Izin / Cuti Anda Sedang Menunggu Persetujuan Atasan</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs bg-white text-orange-800 font-black">MENUNGGU</span>
                        </div>
                        <p class="text-amber-100 text-xs sm:text-sm mt-0.5">
                            Permohonan: <strong class="text-white">{{ ucfirst(str_replace('_', ' ', $userPendingLeave->leave_category)) }}</strong> telah terkirim. Anda dapat memantau status atau membatalkannya di Halaman Tunggu.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('administration.index') }}"
                        class="btn-colorful px-4 py-2 bg-white text-orange-900 hover:bg-yellow-50 rounded-xl text-xs font-black shadow-md transition flex items-center gap-1.5 border-2 border-yellow-400">
                        Lihat Halaman Tunggu
                    </a>
                </div>
            </div>
        @endif

        <!-- Lokasi Kantor Info Card -->
        <div class="bg-white border rounded-xl p-4 shadow-sm mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="size-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-800">{{ $office->office_name }}</h4>
                    <p class="text-xs text-gray-500">
                        Radius Toleransi: <span class="font-medium text-gray-700">{{ $office->radius_meters }} meter</span> | 
                        Jam Masuk: <span class="font-medium text-gray-700">{{ substr($office->check_in_start, 0, 5) }} - {{ substr($office->check_in_end, 0, 5) }}</span> | 
                        Jam Pulang: <span class="font-medium text-gray-700">&ge; {{ substr($office->check_out_start, 0, 5) }}</span>
                    </p>
                </div>
            </div>
            <div id="locationStatusBadge" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-gray-600">
                <span class="size-2 rounded-full bg-gray-400"></span>
                <span>Mendeteksi Lokasi GPS Anda...</span>
            </div>
        </div>

        <!-- Status Presensi Hari Ini Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Box Absen Masuk -->
            <div class="bg-white border rounded-xl p-5 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between mb-4 border-b pb-3">
                    <div class="flex items-center gap-2">
                        <span class="p-2 rounded-lg bg-green-50 text-green-600">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                        </span>
                        <h3 class="font-bold text-gray-800">1. Absen Masuk (Datang)</h3>
                    </div>
                    @if($attendance && $attendance->check_in_time)
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $attendance->check_in_status === 'tepat_waktu' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $attendance->check_in_status === 'tepat_waktu' ? 'Tepat Waktu' : 'Terlambat' }}
                        </span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Belum Masuk</span>
                    @endif
                </div>

                @if($attendance && $attendance->check_in_time)
                    <div class="flex items-center gap-4">
                        @if($attendance->check_in_photo)
                            <img src="{{ asset('storage/' . $attendance->check_in_photo) }}" alt="Foto Masuk" 
                                class="size-24 rounded-lg object-cover border-2 border-green-500 shadow-sm cursor-pointer hover:opacity-90"
                                onclick="openPhotoModal(this.src, 'Foto Wajah Absen Masuk')">
                        @endif
                        <div class="space-y-1 text-sm">
                            <div class="text-xs text-gray-500">Waktu Masuk:</div>
                            <div class="text-xl font-bold text-gray-800">{{ Carbon\Carbon::parse($attendance->check_in_time)->format('H:i:s') }} WIB</div>
                            <div class="text-xs text-gray-500">
                                Jarak ke Kantor: <span class="font-medium text-gray-700">{{ $attendance->check_in_distance ?? 0 }} meter</span>
                            </div>
                            <div class="text-xs text-green-600 font-medium flex items-center gap-1">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                Absen masuk berhasil diverifikasi
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Form Absen Masuk Kamera -->
                    <div class="space-y-4">
                        <p class="text-xs text-gray-600">Posisikan wajah Anda tegak di depan kamera. Ambil foto menggunakan kamera langsung atau live streaming.</p>
                        
                        <!-- Camera Area -->
                        <div class="relative bg-gray-900 rounded-xl overflow-hidden aspect-video flex items-center justify-center border-2 border-gray-300">
                            <video id="videoCheckIn" autoplay playsinline muted class="w-full h-full object-cover hidden"></video>
                            <canvas id="canvasCheckIn" class="hidden"></canvas>
                            <img id="previewCheckIn" class="hidden w-full h-full object-cover" />
                            
                            <!-- Initial Placeholder -->
                            <div id="placeholderCheckIn" class="text-center p-4">
                                <svg class="size-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="text-xs text-gray-300 block">Kamera belum aktif. Klik tombol di bawah untuk mengambil foto.</span>
                            </div>

                            <!-- Camera Frame Guide -->
                            <div id="guideCheckIn" class="hidden absolute inset-0 border-2 border-dashed border-white/60 m-6 rounded-2xl pointer-events-none flex items-center justify-center">
                                <span class="text-[11px] text-white/90 bg-black/50 px-2 py-0.5 rounded">Posisikan Wajah di Sini</span>
                            </div>
                        </div>

                        <!-- Camera Action Buttons -->
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <!-- Direct Hardware/Mobile Camera Trigger (WORKS EVERYWHERE!) -->
                            <input type="file" id="directCaptureIn" accept="image/*" capture="user" class="hidden" 
                                onchange="handleDirectCapture(this, 'previewCheckIn', 'placeholderCheckIn', 'photoInputIn', 'btnSubmitIn', 'videoCheckIn', 'guideCheckIn')">
                            
                            <button type="button" onclick="document.getElementById('directCaptureIn').click()" 
                                class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-xs font-bold shadow-md hover:shadow-lg flex items-center gap-1.5 transition transform active:scale-95">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                Buka Kamera HP / Perangkat
                            </button>

                            <div class="flex items-center gap-1.5">
                                <button type="button" id="btnStartCameraIn" onclick="initCamera('videoCheckIn', 'placeholderCheckIn', 'guideCheckIn', 'previewCheckIn', 'directCaptureIn')" class="btn-colorful px-3.5 py-2 bg-gradient-to-r from-indigo-600 to-blue-700 hover:from-indigo-700 hover:to-blue-800 text-white rounded-xl text-xs font-bold shadow-md transition flex items-center gap-1.5 active:scale-95 border-2 border-transparent">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                    Live Stream
                                </button>
                                <button type="button" id="btnCaptureIn" onclick="takeSnapshot('videoCheckIn', 'canvasCheckIn', 'previewCheckIn', 'photoInputIn', 'btnSubmitIn', 'guideCheckIn')" class="btn-colorful px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-bold rounded-xl shadow-md flex items-center gap-1.5 transition transform active:scale-95 border-2 border-transparent">
                                    Ambil Foto
                                </button>
                            </div>
                        </div>

                        <form action="{{ route('attendance.checkin') }}" method="POST" id="formCheckIn">
                            @csrf
                            <input type="hidden" name="latitude" id="latIn">
                            <input type="hidden" name="longitude" id="lngIn">
                            <input type="hidden" name="photo" id="photoInputIn">
                            <button type="submit" id="btnSubmitIn" disabled class="btn-colorful w-full py-3 bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 hover:from-emerald-700 hover:to-teal-800 text-white text-sm font-bold rounded-xl shadow-lg transition flex items-center justify-center gap-2 transform active:scale-[0.99] disabled:cursor-not-allowed disabled:shadow-md border-2 border-transparent">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                Kirim Absen Masuk
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Box Status Absen Pulang (Hanya Foto Wajah & GPS) -->
            <div class="bg-white border rounded-xl p-5 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between mb-4 border-b pb-3">
                    <div class="flex items-center gap-2">
                        <span class="p-2 rounded-lg bg-indigo-50 text-indigo-600">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                        </span>
                        <h3 class="font-bold text-gray-800">2. Absen Pulang</h3>
                    </div>
                    @if($attendance && $attendance->check_out_time)
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">Sudah Pulang</span>
                    @elseif($attendance && $attendance->check_in_time)
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 animate-pulse">Sedang Bekerja</span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Menunggu Masuk</span>
                    @endif
                </div>

                @if($attendance && $attendance->check_out_time)
                    <div class="flex items-center gap-4">
                        @if($attendance->check_out_photo)
                            <img src="{{ asset('storage/' . $attendance->check_out_photo) }}" alt="Foto Pulang" 
                                class="size-24 rounded-lg object-cover border-2 border-indigo-500 shadow-sm cursor-pointer hover:opacity-90"
                                onclick="openPhotoModal(this.src, 'Foto Wajah Absen Pulang')">
                        @endif
                        <div class="space-y-1 text-sm">
                            <div class="text-xs text-gray-500">Waktu Pulang:</div>
                            <div class="text-xl font-bold text-gray-800">{{ Carbon\Carbon::parse($attendance->check_out_time)->format('H:i:s') }} WIB</div>
                            <div class="text-xs text-gray-500">
                                Jarak ke Kantor: <span class="font-medium text-gray-700">{{ $attendance->check_out_distance ?? 0 }} meter</span>
                            </div>
                            <div class="text-xs text-emerald-600 font-medium flex items-center gap-1">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                Absen pulang berhasil diverifikasi
                            </div>
                        </div>
                    </div>
                @elseif($attendance && $attendance->check_in_time)
                    <!-- Form Absen Pulang Kamera (Hanya Foto Wajah) -->
                    <div class="space-y-4">
                        <p class="text-xs text-gray-600">Ambil foto wajah pulang Anda di area kantor sebelum meninggalkan tempat tugas.</p>
                        
                        <div class="relative bg-gray-900 rounded-xl overflow-hidden aspect-video flex items-center justify-center border-2 border-gray-300">
                            <video id="videoCheckOut" autoplay playsinline muted class="w-full h-full object-cover hidden"></video>
                            <canvas id="canvasCheckOut" class="hidden"></canvas>
                            <img id="previewCheckOut" class="hidden w-full h-full object-cover" />
                            
                            <div id="placeholderCheckOut" class="text-center p-4">
                                <svg class="size-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="text-xs text-gray-300 block">Kamera belum aktif. Klik tombol di bawah untuk mengambil foto pulang.</span>
                            </div>

                            <div id="guideCheckOut" class="hidden absolute inset-0 border-2 border-dashed border-white/60 m-6 rounded-2xl pointer-events-none flex items-center justify-center">
                                <span class="text-[11px] text-white/90 bg-black/50 px-2 py-0.5 rounded">Posisikan Wajah di Sini</span>
                            </div>
                        </div>

                        <!-- Camera Actions Out -->
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <input type="file" id="directCaptureOut" accept="image/*" capture="user" class="hidden" 
                                onchange="handleDirectCapture(this, 'previewCheckOut', 'placeholderCheckOut', 'photoInputOut', 'btnSubmitOut', 'videoCheckOut', 'guideCheckOut')">
                            
                            <button type="button" onclick="document.getElementById('directCaptureOut').click()" 
                                class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl text-xs font-bold shadow-md hover:shadow-lg flex items-center gap-1.5 transition transform active:scale-95">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                Buka Kamera HP / Perangkat
                            </button>

                            <div class="flex items-center gap-1.5">
                                <button type="button" id="btnStartCameraOut" onclick="initCamera('videoCheckOut', 'placeholderCheckOut', 'guideCheckOut', 'previewCheckOut', 'directCaptureOut')" class="btn-colorful px-3.5 py-2 bg-gradient-to-r from-purple-600 to-indigo-700 hover:from-purple-700 hover:to-indigo-800 text-white rounded-xl text-xs font-bold shadow-md transition flex items-center gap-1.5 active:scale-95 border-2 border-transparent">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                    Live Stream
                                </button>
                                <button type="button" id="btnCaptureOut" onclick="takeSnapshot('videoCheckOut', 'canvasCheckOut', 'previewCheckOut', 'photoInputOut', 'btnSubmitOut', 'guideCheckOut')" class="btn-colorful px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white text-xs font-bold rounded-xl shadow-md flex items-center gap-1.5 transition transform active:scale-95 border-2 border-transparent">
                                    Ambil Foto
                                </button>
                            </div>
                        </div>

                        <form action="{{ route('attendance.checkout') }}" method="POST" id="formCheckOut">
                            @csrf
                            <input type="hidden" name="latitude" id="latOut">
                            <input type="hidden" name="longitude" id="lngOut">
                            <input type="hidden" name="photo" id="photoInputOut">
                            <button type="submit" id="btnSubmitOut" disabled class="btn-colorful w-full py-3 bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-700 hover:from-indigo-700 hover:to-purple-800 text-white text-sm font-bold rounded-xl shadow-lg transition flex items-center justify-center gap-2 transform active:scale-[0.99] disabled:cursor-not-allowed disabled:shadow-md border-2 border-transparent">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                Kirim Absen Pulang
                            </button>
                        </form>
                    </div>
                @else
                    <div class="text-center py-12 text-gray-400 text-sm">
                        <svg class="size-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="font-medium text-gray-600">Menunggu Absen Masuk</p>
                        <p class="text-xs text-gray-400 mt-1">Anda harus melakukan absen masuk terlebih dahulu sebelum dapat mengambil absen pulang.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- SECTION MONEV KEGIATAN HARIAN (FORM TERSENDIRI & DAFTAR KEGIATAN) -->
        <div id="monev-section" class="bg-white border rounded-2xl p-6 shadow-md mb-8">
            <!-- Monev Header Banner -->
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between pb-4 border-b gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="size-12 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0">
                        <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            @if(auth()->user()->role === 'admin')
                                <h2 class="text-xl font-bold text-gray-800">Monitoring Monev Aktivitas Seluruh Pegawai Non-ASN</h2>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">
                                    {{ $activityCount }} Kegiatan Terlaporkan Hari Ini
                                </span>
                            @else
                                <h2 class="text-xl font-bold text-gray-800">Monev Aktivitas & Kegiatan Harian Pegawai</h2>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">
                                    {{ $activityCount }} / 10 Kegiatan
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">
                            @if(auth()->user()->role === 'admin')
                                Memantau seluruh uraian tugas dan kendala lapangan yang dilaporkan oleh Satpam, Driver, dan Petugas Kebersihan hari ini.
                            @else
                                Catat laporan aktivitas tugas harian Anda (Maksimal 10 kegiatan per hari, batas pengisian pukul <strong class="text-indigo-700">14:30 WIB</strong>).
                            @endif
                        </p>
                    </div>
                </div>

                <div>
                    @if(!$isBeforeClosingTime)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                            <span class="size-2 rounded-full bg-rose-500"></span>
                            Pengisian Ditutup (Melebihi 14:30 WIB)
                        </span>
                    @elseif($activityCount >= 10)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                            <span class="size-2 rounded-full bg-amber-500"></span>
                            Batas Maksimal 10 Kegiatan Tercapai
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="size-2 rounded-full bg-emerald-500 animate-ping"></span>
                            Pengisian Dibuka (Sisa: {{ 10 - $activityCount }} kegiatan s/d 14:30)
                        </span>
                    @endif
                </div>
            </div>

            <!-- Form Pengisian Kegiatan Baru (Tampil jika masih bisa mengisi) -->
            @if($canSubmitMonev)
                <div class="bg-gradient-to-br from-indigo-50/50 to-blue-50/30 rounded-2xl border border-indigo-100 p-5 mb-8">
                    <h3 class="font-bold text-gray-800 text-sm mb-3 flex items-center gap-2">
                        <span class="size-6 rounded-lg bg-indigo-600 text-white text-xs flex items-center justify-center font-bold">
                            +
                        </span>
                        Tambah Catatan Kegiatan #{{ $activityCount + 1 }} dari 10:
                    </h3>

                    <form action="{{ route('attendance.monev.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Uraian Aktivitas -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">
                                    1. Uraian Aktivitas / Tugas Pekerjaan <span class="text-red-500">*</span>
                                </label>
                                <textarea name="activity_description" rows="3" required
                                    class="w-full border border-gray-300 rounded-lg p-2.5 text-xs bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                    placeholder="Contoh: Melakukan pengecekan dan patroli keamanan di area gerbang utama dan pos piket...">{{ old('activity_description') }}</textarea>
                                @error('activity_description')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Kendala yang Dihadapi -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">
                                    2. Kendala yang Dihadapi (Opsional)
                                </label>
                                <textarea name="obstacle" rows="3"
                                    class="w-full border border-gray-300 rounded-lg p-2.5 text-xs bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                    placeholder="Tuliskan kendala / hambatan jika ada (contoh: Lampu pos 2 redup / cuaca hujan lebat)...">{{ old('obstacle') }}</textarea>
                                @error('obstacle')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Bukti Foto Kegiatan (Maks 3 Foto) -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                3. Bukti Foto Kegiatan (Maksimal 3 Foto)
                            </label>
                            <input type="file" name="activity_photos[]" multiple accept="image/*"
                                onchange="previewMonevPhotos(this)"
                                class="w-full border border-gray-300 rounded-xl p-2 text-xs bg-white focus:outline-none file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 file:shadow-sm file:cursor-pointer">
                            <p class="text-[11px] text-gray-500 mt-1">Dapat memilih 1 hingga 3 foto dokumentasi pekerjaan (JPG, PNG, max 3MB).</p>
                            
                            <!-- Container Preview Foto -->
                            <div id="monevPhotosPreview" class="flex gap-2 mt-2"></div>
                            @error('activity_photos')
                                <span class="text-xs text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit" 
                                class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white rounded-xl text-xs font-bold shadow-md hover:shadow-lg transition flex items-center gap-1.5 transform active:scale-95">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                Simpan Kegiatan #{{ $activityCount + 1 }}
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- DAFTAR KEGIATAN YANG TELAH DIISI HARI INI -->
            <div>
                <h3 class="font-bold text-gray-800 text-sm mb-3 flex items-center gap-2">
                    <svg class="size-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    @if(auth()->user()->role === 'admin')
                        Aktivitas & Kegiatan Seluruh Pegawai yang Masuk Hari Ini:
                    @else
                        Aktivitas & Kegiatan yang Telah Anda Laporkan Hari Ini:
                    @endif
                </h3>

                @if($todayActivities->isEmpty())
                    <div class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-300 text-gray-500">
                        <svg class="size-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-xs font-semibold text-gray-700">
                            @if(auth()->user()->role === 'admin')
                                Belum ada pegawai yang mencatat kegiatan pekerjaan hari ini.
                            @else
                                Belum ada kegiatan pekerjaan yang dicatat hari ini.
                            @endif
                        </p>
                        <p class="text-[11px] text-gray-400 mt-0.5">
                            @if(auth()->user()->role === 'admin')
                                Aktivitas dari Satpam, Driver, dan Petugas Kebersihan akan langsung terpantau di sini.
                            @elseif($isBeforeClosingTime)
                                Silakan gunakan form di atas untuk mencatat kegiatan harian Anda sebelum pukul 14:30 WIB.
                            @else
                                Batas waktu pengisian pukul 14:30 WIB telah berakhir.
                            @endif
                        </p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($todayActivities as $idx => $act)
                            <div class="bg-gray-50 hover:bg-gray-100/80 transition rounded-xl p-4 border border-gray-200">
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <div class="flex items-center gap-2">
                                        @if(auth()->user()->role === 'admin')
                                            <span class="font-bold text-gray-900 text-xs">{{ $act->user->name }}</span>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                                {{ $act->user->employee?->role?->name ?? 'Pegawai Non-ASN' }}
                                            </span>
                                        @endif
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-600 text-white shadow-sm">
                                            Kegiatan #{{ $idx + 1 }}
                                        </span>
                                        <span class="text-xs font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 px-2.5 py-0.5 rounded-lg shadow-sm">
                                            Jam {{ Carbon\Carbon::parse($act->time)->format('H:i') }} WIB
                                        </span>
                                    </div>
                                    @if(auth()->user()->role === 'admin' || $isBeforeClosingTime)
                                        <form action="{{ route('attendance.monev.destroy', $act->id) }}" method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan kegiatan #{{ $idx + 1 }} ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-500 hover:text-rose-700 hover:bg-rose-50 p-1.5 rounded-lg text-xs font-semibold transition" title="Hapus kegiatan">
                                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <div class="space-y-1.5 text-xs text-gray-800">
                                    <div>
                                        <span class="font-bold text-gray-700">Uraian Tugas:</span>
                                        <p class="text-gray-700 mt-0.5 leading-relaxed">{{ $act->activity_description }}</p>
                                    </div>

                                    @if($act->obstacle && $act->obstacle !== 'Tidak ada kendala')
                                        <div class="pt-1">
                                            <span class="font-bold text-amber-800">Kendala:</span>
                                            <p class="text-amber-900 italic mt-0.5">{{ $act->obstacle }}</p>
                                        </div>
                                    @endif

                                    @if(!empty($act->photos))
                                        <div class="pt-2">
                                            <span class="font-semibold text-gray-600 block mb-1">Bukti Foto ({{ count($act->photos) }} Foto):</span>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($act->photos as $p)
                                                    <img src="{{ asset('storage/' . $p) }}" alt="Foto Kegiatan"
                                                        class="size-16 rounded-lg object-cover border-2 border-indigo-200 cursor-pointer hover:opacity-90 shadow-sm transition"
                                                        onclick="openPhotoModal('{{ asset('storage/' . $p) }}', 'Bukti Foto Kegiatan #{{ $idx + 1 }}')">
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

    <!-- Image Preview Modal (Fixed Closing) -->
    <div id="photoModal" style="display: none;" class="fixed inset-0 bg-black/80 z-[100] items-center justify-center p-4">
        <div class="bg-white rounded-2xl overflow-hidden max-w-lg w-full shadow-2xl relative" onclick="event.stopPropagation()">
            <div class="p-3.5 border-b flex justify-between items-center bg-gray-50">
                <h4 id="photoModalTitle" class="text-sm font-bold text-gray-800">Preview Foto</h4>
                <button type="button" onclick="closePhotoModal()" class="text-gray-400 hover:text-gray-700 text-2xl font-bold w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-200 transition">&times;</button>
            </div>
            <div class="p-4 flex justify-center bg-black">
                <img id="photoModalImg" src="" class="max-h-[75vh] object-contain rounded">
            </div>
            <div class="p-3 bg-gray-50 border-t flex justify-end">
                <button type="button" onclick="closePhotoModal()" class="btn-colorful px-5 py-2 bg-gradient-to-r from-gray-700 to-gray-800 hover:from-gray-800 hover:to-gray-900 text-white text-xs font-bold rounded-xl shadow-md transition border border-transparent">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentStream = null;
        let userLat = null;
        let userLng = null;
        const officeLat = {{ $office->latitude }};
        const officeLng = {{ $office->longitude }};
        const officeRadius = {{ $office->radius_meters }};

        // Geolocation tracking
        document.addEventListener('DOMContentLoaded', function() {
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        userLat = position.coords.latitude;
                        userLng = position.coords.longitude;

                        // Fill hidden inputs
                        const latIn = document.getElementById('latIn');
                        const lngIn = document.getElementById('lngIn');
                        const latOut = document.getElementById('latOut');
                        const lngOut = document.getElementById('lngOut');
                        if (latIn) latIn.value = userLat;
                        if (lngIn) lngIn.value = userLng;
                        if (latOut) latOut.value = userLat;
                        if (lngOut) lngOut.value = userLng;

                        // Calculate distance to office
                        const distance = calculateDistance(userLat, userLng, officeLat, officeLng);
                        const badge = document.getElementById('locationStatusBadge');

                        if (distance <= officeRadius) {
                            badge.className = "flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200";
                            badge.innerHTML = `<span class="size-2 rounded-full bg-emerald-500"></span> Dalam Area Kantor (${distance}m dari pusat kantor)`;
                        } else {
                            badge.className = "flex flex-wrap items-center justify-between gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200";
                            badge.innerHTML = `
                                <div class="flex items-center gap-1.5">
                                    <span class="size-2 rounded-full bg-rose-500"></span> 
                                    <span>Di Luar Area Kantor (${distance}m, toleransi ${officeRadius}m)</span>
                                </div>
                                <button type="button" onclick="calibrateToMyLocation()" class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold text-[11px] shadow transition flex items-center gap-1">
                                    <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    Set Kantor ke GPS Saya
                                </button>
                            `;
                        }
                    },
                    function(error) {
                        const badge = document.getElementById('locationStatusBadge');
                        badge.className = "flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200";
                        badge.innerHTML = `<span class="size-2 rounded-full bg-amber-500"></span> GPS: Klik 'Izinkan Lokasi' pada browser Anda`;
                    },
                    { enableHighAccuracy: true, timeout: 10000 }
                );
            }
        });

        // Camera WebRTC helper with automatic fallback
        async function initCamera(videoId, placeholderId, guideId, previewId, directInputId) {
            const video = document.getElementById(videoId);
            const placeholder = document.getElementById(placeholderId);
            const guide = document.getElementById(guideId);
            const preview = document.getElementById(previewId);

            // Periksa ketersediaan MediaDevices (HTTPS / Localhost constraint)
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert("Browser membatasi streaming WebRTC pada koneksi non-HTTPS. Sistem otomatis membuka kamera perangkat Anda langsung.");
                document.getElementById(directInputId).click();
                return;
            }

            try {
                if (currentStream) {
                    currentStream.getTracks().forEach(track => track.stop());
                }

                // Coba resolusi ideal, jika gagal coba constraint dasar
                let stream;
                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: "user", width: { ideal: 640 }, height: { ideal: 480 } },
                        audio: false
                    });
                } catch(e) {
                    stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                }

                currentStream = stream;
                video.srcObject = stream;
                video.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
                if (guide) guide.classList.remove('hidden');
                if (preview) preview.classList.add('hidden');
            } catch (err) {
                console.error("Camera error:", err);
                alert("Tidak dapat mengakses streaming webcam (" + err.message + "). Membuka kamera langsung dari perangkat Anda...");
                document.getElementById(directInputId).click();
            }
        }

        // Direct Native Camera / File Input Capture
        function handleDirectCapture(fileInput, previewId, placeholderId, hiddenInputId, submitBtnId, videoId, guideId) {
            if (!fileInput.files || !fileInput.files[0]) return;

            const file = fileInput.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    // Kompresi ke canvas agar ukuran tidak melebihi memory
                    const canvas = document.createElement('canvas');
                    let width = img.width;
                    let height = img.height;
                    const maxDim = 800;

                    if (width > height && width > maxDim) {
                        height = Math.round(height * (maxDim / width));
                        width = maxDim;
                    } else if (height > maxDim) {
                        width = Math.round(width * (maxDim / height));
                        height = maxDim;
                    }

                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    const dataUrl = canvas.toDataURL('image/jpeg', 0.85);

                    // Set ke input hidden dan preview
                    document.getElementById(hiddenInputId).value = dataUrl;
                    const preview = document.getElementById(previewId);
                    preview.src = dataUrl;
                    preview.classList.remove('hidden');

                    const video = document.getElementById(videoId);
                    if (video) video.classList.add('hidden');
                    const placeholder = document.getElementById(placeholderId);
                    if (placeholder) placeholder.classList.add('hidden');
                    const guide = document.getElementById(guideId);
                    if (guide) guide.classList.add('hidden');

                    if (submitBtnId) {
                        const submitBtn = document.getElementById(submitBtnId);
                        if (submitBtn) submitBtn.disabled = false;
                    }

                    // Hentikan stream kamera jika sedang berjalan
                    if (currentStream) {
                        currentStream.getTracks().forEach(track => track.stop());
                    }
                };
                img.src = e.target.result;
            };

            reader.readAsDataURL(file);
        }

        // Take Snapshot Face Photo from WebRTC Stream
        function takeSnapshot(videoId, canvasId, previewId, inputId, submitBtnId, guideId) {
            const video = document.getElementById(videoId);
            const canvas = document.getElementById(canvasId);
            const preview = document.getElementById(previewId);
            const input = document.getElementById(inputId);
            const guide = document.getElementById(guideId);

            if (!video.srcObject) {
                alert("Silakan nyalakan live stream kamera terlebih dahulu atau gunakan tombol 'Buka Kamera HP / Perangkat'!");
                return;
            }

            canvas.width = video.videoWidth || 640;
            canvas.height = video.videoHeight || 480;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
            input.value = dataUrl;
            preview.src = dataUrl;
            preview.classList.remove('hidden');
            video.classList.add('hidden');
            if (guide) guide.classList.add('hidden');

            if (submitBtnId) {
                const btnSubmit = document.getElementById(submitBtnId);
                if (btnSubmit) btnSubmit.disabled = false;
            }
        }

        // Haversine distance in meters
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371000;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                      Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return Math.round(R * c);
        }

        // Kendala Character Counter Validation (MIN 100 CHARS)
        function checkObstacleLength(textarea) {
            const length = textarea.value.trim().length;
            const counter = document.getElementById('charCount');
            if (length >= 100) {
                counter.className = "text-xs font-semibold text-emerald-600";
                counter.innerText = `${length} karakter (Memenuhi syarat minimal)`;
            } else {
                counter.className = "text-xs font-semibold text-red-500";
                counter.innerText = `${length} / 100 karakter minimum`;
            }
        }

        // Preview Activity Photos (max 3)
        function previewActivityPhotos(input) {
            const container = document.getElementById('activityPhotosPreview');
            container.innerHTML = '';
            if (input.files.length > 3) {
                alert("Maksimal hanya dapat mengunggah 3 foto kegiatan.");
                input.value = '';
                return;
            }
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = "size-16 rounded object-cover border-2 border-indigo-300";
                    container.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        }

        // Quick GPS Calibration to Current Location
        function calibrateToMyLocation() {
            if (!userLat || !userLng) {
                alert("Koordinat GPS perangkat Anda belum terdeteksi. Harap izinkan akses lokasi pada browser Anda.");
                return;
            }

            if (!confirm("Apakah Anda sedang berada di kantor dan ingin menetapkan titik kantor ke posisi GPS Anda saat ini?\nLatitude: " + userLat + "\nLongitude: " + userLng)) {
                return;
            }

            fetch('{{ route("attendance.calibrate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    latitude: userLat,
                    longitude: userLng,
                    radius_meters: 500
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("Berhasil! Titik kantor telah diperbarui ke koordinat Anda (" + userLat + ", " + userLng + ") dengan radius toleransi 500 meter.");
                    window.location.reload();
                } else {
                    alert("Gagal mengalibrasi: " + (data.message || 'Terjadi kesalahan'));
                }
            })
            .catch(err => {
                alert("Gagal menghubungi server: " + err.message);
            });
        }

        // Modal Photo Preview (Fixed Closing, Escape & Backdrop Support)
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

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closePhotoModal();
        });

        // Close modal on backdrop click
        document.addEventListener('DOMContentLoaded', function() {
            const photoModalEl = document.getElementById('photoModal');
            if (photoModalEl) {
                photoModalEl.addEventListener('click', function(e) {
                    if (e.target === this) closePhotoModal();
                });
            }
        });

        // Preview Monev Activity Photos (max 3)
        function previewMonevPhotos(input) {
            const container = document.getElementById('monevPhotosPreview');
            if (!container) return;
            container.innerHTML = '';
            if (input.files.length > 3) {
                alert("Maksimal hanya dapat melampirkan 3 foto kegiatan.");
                input.value = '';
                return;
            }
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = "size-16 rounded object-cover border-2 border-indigo-300 shadow-sm";
                    container.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        }

        // CheckOut Form Validation before submit (Hanya Foto Wajah)
        const formCheckOut = document.getElementById('formCheckOut');
        if (formCheckOut) {
            formCheckOut.addEventListener('submit', function(e) {
                const photoInput = document.getElementById('photoInputOut').value;

                if (!photoInput) {
                    e.preventDefault();
                    alert("Foto wajah pulang wajib diambil terlebih dahulu menggunakan kamera!");
                    return false;
                }
            });
        }
    </script>
</x-layouts.layout>
