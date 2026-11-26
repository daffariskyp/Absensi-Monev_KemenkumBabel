<x-layouts.layout :title="$title" :active="$active">
    <div class="max-w-4xl mx-auto py-8 px-4">
        <!-- Top Status Alert Banner -->
        <div class="bg-gradient-to-r from-amber-500 via-amber-600 to-orange-600 text-white rounded-2xl p-6 shadow-xl mb-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="size-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white shrink-0 shadow-inner">
                        <svg class="size-7 animate-spin" style="animation-duration: 3s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/25 backdrop-blur-sm text-amber-50 mb-1">
                            <span class="size-2 rounded-full bg-yellow-300 animate-ping"></span>
                            HALAMAN TUNGGU PERSETUJUAN
                        </div>
                        <h1 class="text-2xl font-black tracking-tight">Pengajuan Izin Sedang Diproses</h1>
                        <p class="text-amber-100 text-xs sm:text-sm mt-0.5">
                            Permohonan Anda telah tersimpan di sistem dan sedang menunggu verifikasi serta persetujuan dari pimpinan / admin.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 self-stretch sm:self-auto justify-end">
                    <a href="{{ route('attendance.index') }}" 
                        class="px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur-md text-white text-xs font-bold rounded-xl border border-white/30 transition flex items-center gap-1.5 shadow-sm">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Ke Absensi
                    </a>
                </div>
            </div>
        </div>

        <!-- Detail Permohonan Card -->
        <div class="bg-white border-2 border-amber-200/80 rounded-2xl shadow-md overflow-hidden mb-8">
            <div class="p-5 bg-amber-50/50 border-b border-amber-200/60 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="size-3 rounded-full bg-amber-500 animate-pulse"></span>
                    <h2 class="font-bold text-gray-800 text-base">Rincian Pengajuan yang Sedang Menunggu</h2>
                </div>
                <span class="px-3 py-1 bg-amber-100 border border-amber-300 text-amber-800 text-xs font-bold rounded-full">
                    Status: Menunggu Persetujuan
                </span>
            </div>

            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Kategori Izin -->
                    <div class="bg-gray-50 p-4 rounded-xl border">
                        <span class="text-xs text-gray-500 font-medium">Jenis Izin / Cuti:</span>
                        <div class="text-base font-bold text-gray-800 mt-1">
                            {{ $categories[$pendingLeave->leave_category] ?? ucfirst(str_replace('_', ' ', $pendingLeave->leave_category)) }}
                        </div>
                    </div>

                    <!-- Durasi & Tanggal -->
                    <div class="bg-gray-50 p-4 rounded-xl border">
                        <span class="text-xs text-gray-500 font-medium">Periode & Durasi:</span>
                        <div class="text-base font-bold text-gray-800 mt-1">
                            {{ \Carbon\Carbon::parse($pendingLeave->start_date)->locale('id')->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($pendingLeave->end_date)->locale('id')->translatedFormat('d M Y') }}
                        </div>
                        <div class="text-xs text-indigo-700 font-semibold mt-0.5">
                            Total: {{ $pendingLeave->lama_hari_cuti }} Hari Kerja
                        </div>
                    </div>
                </div>

                <!-- Alasan / Uraian -->
                <div class="bg-gray-50 p-4 rounded-xl border">
                    <span class="text-xs text-gray-500 font-medium">Alasan / Uraian Keperluan Izin:</span>
                    <p class="text-sm text-gray-800 mt-1 leading-relaxed whitespace-pre-line font-normal">
                        {{ $pendingLeave->description }}
                    </p>
                </div>

                <!-- Info Perangkat & Bukti Foto Surat Sakit -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Bawa Laptop & Kontak -->
                    <div class="bg-gray-50 p-4 rounded-xl border space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Membawa Laptop / Perangkat Kerja:</span>
                            <span class="font-bold {{ $pendingLeave->bring_laptop ? 'text-emerald-700' : 'text-gray-700' }}">
                                {{ $pendingLeave->bring_laptop ? 'Ya' : 'Tidak' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Dapat Dihubungi saat Izin:</span>
                            <span class="font-bold {{ $pendingLeave->contacted ? 'text-emerald-700' : 'text-gray-700' }}">
                                {{ $pendingLeave->contacted ? 'Ya' : 'Tidak' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between pt-1 border-t text-[11px] text-gray-400">
                            <span>Waktu Mengajukan:</span>
                            <span>{{ $pendingLeave->created_at->locale('id')->translatedFormat('d M Y H:i') }} WIB</span>
                        </div>
                    </div>

                    <!-- Bukti Surat Sakit / Lampiran -->
                    <div class="bg-gray-50 p-4 rounded-xl border">
                        <span class="text-xs text-gray-500 font-medium block mb-2">Lampiran Bukti / Surat Keterangan Dokter:</span>
                        @if($pendingLeave->attachment)
                            @php
                                $ext = strtolower(pathinfo($pendingLeave->attachment, PATHINFO_EXTENSION));
                                $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
                                $url = asset('storage/' . $pendingLeave->attachment);
                            @endphp

                            @if($isImg)
                                <div class="flex items-center gap-3">
                                    <img src="{{ $url }}" alt="Surat Sakit" 
                                        class="size-16 rounded-lg object-cover border-2 border-indigo-400 cursor-pointer hover:opacity-90 shadow-sm"
                                        onclick="openPhotoModal('{{ $url }}', 'Bukti Lampiran Surat Sakit')">
                                    <div>
                                        <button type="button" onclick="openPhotoModal('{{ $url }}', 'Bukti Lampiran Surat Sakit')"
                                            class="text-xs font-bold text-indigo-600 hover:text-indigo-800 underline block">
                                            Lihat Foto Surat Sakit
                                        </button>
                                        <span class="text-[10px] text-gray-400">Klik untuk melihat ukuran penuh</span>
                                    </div>
                                </div>
                            @else
                                <a href="{{ $url }}" target="_blank"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-semibold hover:bg-indigo-100 transition">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    Unduh Dokumen PDF
                                </a>
                            @endif
                        @else
                            <span class="text-xs text-gray-400 italic">Tidak ada berkas lampiran yang diunggah.</span>
                        @endif
                    </div>
                </div>

                <!-- Tombol Aksi: Batalkan Pengajuan -->
                <div class="pt-4 border-t flex flex-wrap items-center justify-between gap-3">
                    <p class="text-xs text-gray-500">
                        * Jika terdapat kesalahan data atau ingin mengubah jadwal, Anda dapat membatalkan pengajuan ini.
                    </p>
                    <form action="{{ route('administration.destroy', $pendingLeave->id) }}" method="POST" 
                        onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan izin ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                            style="background-color: #e11d48; color: #ffffff !important; border: 2px solid #fda4af;"
                            class="btn-colorful px-5 py-2.5 hover:bg-rose-700 text-white rounded-xl text-xs font-black shadow-md transition flex items-center gap-1.5 active:shadow-inner cursor-pointer">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Batalkan Pengajuan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Riwayat Izin Sebelumnya (Jika Ada) -->
        @if(isset($pastLeaves) && $pastLeaves->isNotEmpty())
            <div class="bg-white border rounded-2xl p-6 shadow-sm">
                <h3 class="font-bold text-gray-800 text-sm mb-4 flex items-center gap-2">
                    <svg class="size-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Riwayat Pengajuan Izin / Cuti Sebelumnya
                </h3>

                <div class="divide-y text-xs">
                    @foreach($pastLeaves as $past)
                        <div class="py-3 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                            <div>
                                <div class="font-bold text-gray-800">
                                    {{ $categories[$past->leave_category] ?? $past->leave_category }}
                                </div>
                                <div class="text-gray-500 text-[11px] mt-0.5">
                                    {{ \Carbon\Carbon::parse($past->start_date)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($past->end_date)->translatedFormat('d M Y') }} 
                                    ({{ $past->lama_hari_cuti }} hari)
                                </div>
                                @if($past->approver_note)
                                    <div class="text-[11px] text-gray-600 italic mt-0.5">
                                        Catatan atasan: "{{ $past->approver_note }}"
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center gap-2">
                                @if($past->status === 'approved')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                        &check; Disetujui
                                    </span>
                                    <a href="{{ route('administration.print', $past->id) }}" target="_blank"
                                        class="btn-colorful px-3 py-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg text-[11px] font-bold shadow-sm transition flex items-center gap-1 border border-transparent"
                                        title="Download Surat Bukti Cuti Resmi dengan TTD QR">
                                        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                                        <span>Download TTD QR</span>
                                    </a>
                                @elseif($past->status === 'rejected')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">
                                        &times; Ditolak
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                        Menunggu
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Image Preview Modal (Fixed Closing) -->
    <div id="photoModal" style="display: none;" class="fixed inset-0 bg-black/80 z-[100] items-center justify-center p-4">
        <div class="bg-white rounded-2xl overflow-hidden max-w-lg w-full shadow-2xl relative" onclick="event.stopPropagation()">
            <div class="p-3.5 border-b flex justify-between items-center bg-gray-50">
                <h4 id="photoModalTitle" class="text-sm font-bold text-gray-800">Bukti Lampiran</h4>
                <button type="button" onclick="closePhotoModal()" class="text-gray-400 hover:text-gray-700 text-2xl font-bold w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-200 transition">&times;</button>
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

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closePhotoModal();
        });

        document.addEventListener('DOMContentLoaded', function() {
            const el = document.getElementById('photoModal');
            if (el) {
                el.addEventListener('click', function(e) {
                    if (e.target === this) closePhotoModal();
                });
            }
        });
    </script>
</x-layouts.layout>
