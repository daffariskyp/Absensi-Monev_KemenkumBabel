<x-layouts.layout :title="$title" :active="$active">
    <div class="max-w-7xl mx-auto py-6 px-4">
        <!-- Dashboard Header -->
        <div class="bg-gradient-to-r from-slate-800 to-indigo-900 text-white rounded-2xl p-6 shadow-md mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="inline-flex items-center gap-2 bg-indigo-500/30 text-indigo-100 text-xs px-3 py-1 rounded-full font-medium mb-2 backdrop-blur-sm">
                    <span class="size-2 rounded-full bg-emerald-400"></span>
                    Dashboard Perizinan & Surat Sakit Non-ASN
                </div>
                <h1 class="text-2xl font-bold">Dashboard Pengajuan Izin & Cuti Pegawai</h1>
                <p class="text-indigo-200 text-sm mt-1">
                    Kelola dan pantau perizinan kerja, surat keterangan sakit dokter, cuti tahunan, dan status persetujuan atasan.
                </p>
            </div>
            <div>
                <a href="{{ route('administration.create') }}"
                    style="background-color: #2563eb; color: #ffffff !important; border: 2px solid #facc15;"
                    class="btn-colorful hover:bg-blue-700 text-white text-sm px-5 py-2.5 rounded-xl font-black inline-flex items-center gap-2 shadow-lg transition active:shadow-inner cursor-pointer">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Pengajuan Izin Baru
                </a>
            </div>
        </div>

        <!-- Dashboard Statistik Cards -->
        <div class="grid grid-cols-2 md:grid-cols-6 gap-3 mb-6">
            <div class="bg-white border rounded-xl p-4 shadow-sm">
                <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Izin</div>
                <div class="text-2xl font-extrabold text-gray-800 mt-1" data-count-up>{{ $stats['total'] }}</div>
                <div class="text-[11px] text-gray-500">Semua Permohonan</div>
            </div>
            <div class="bg-white border border-amber-200 rounded-xl p-4 shadow-sm">
                <div class="text-[11px] font-bold text-amber-600 uppercase tracking-wider">Pending</div>
                <div class="text-2xl font-extrabold text-amber-700 mt-1" data-count-up>{{ $stats['pending'] }}</div>
                <div class="text-[11px] text-amber-500">Menunggu Atasan</div>
            </div>
            <div class="bg-white border border-emerald-200 rounded-xl p-4 shadow-sm">
                <div class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Disetujui</div>
                <div class="text-2xl font-extrabold text-emerald-700 mt-1" data-count-up>{{ $stats['approved'] }}</div>
                <div class="text-[11px] text-emerald-500">Izin Diterima</div>
            </div>
            <div class="bg-white border border-rose-200 rounded-xl p-4 shadow-sm">
                <div class="text-[11px] font-bold text-rose-600 uppercase tracking-wider">Ditolak</div>
                <div class="text-2xl font-extrabold text-rose-700 mt-1" data-count-up>{{ $stats['rejected'] }}</div>
                <div class="text-[11px] text-rose-500">Tidak Diizinkan</div>
            </div>
            <div class="bg-white border border-purple-200 rounded-xl p-4 shadow-sm">
                <div class="text-[11px] font-bold text-purple-600 uppercase tracking-wider">Izin Sakit</div>
                <div class="text-2xl font-extrabold text-purple-700 mt-1" data-count-up>{{ $stats['sakit'] }}</div>
                <div class="text-[11px] text-purple-500">Surat Dokter</div>
            </div>
            <div class="bg-white border border-blue-200 rounded-xl p-4 shadow-sm">
                <div class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">Cuti Tahunan</div>
                <div class="text-2xl font-extrabold text-blue-700 mt-1" data-count-up>{{ $stats['cuti'] }}</div>
                <div class="text-[11px] text-blue-500">Hak Cuti Non-ASN</div>
            </div>
        </div>

        <!-- Banner: Pegawai Sedang Izin/Sakit Hari Ini -->
        @if($activeLeavesToday->isNotEmpty())
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="size-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-sm font-bold text-amber-900">Pegawai Non-ASN Sedang Izin / Sakit Hari Ini ({{ $todayFormatted }}):</h3>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($activeLeavesToday as $al)
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-amber-300 rounded-lg text-xs shadow-sm">
                            <span class="font-bold text-gray-800">{{ $al->user?->name }}</span>
                            <span class="px-1.5 py-0.5 rounded bg-blue-100 text-blue-800 text-[10px] font-semibold">
                                {{ $al->user?->employee?->role?->name ?? 'Non-ASN' }}
                            </span>
                            <span class="text-amber-700 font-medium">
                                - {{ $categories[$al->leave_category] ?? $al->leave_category }} 
                                (s/d {{ \Carbon\Carbon::parse($al->end_date)->translatedFormat('d M') }})
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Filter Bar -->
        <div class="bg-white border rounded-xl p-4 shadow-sm mb-6 flex flex-wrap items-center justify-between gap-4">
            <form action="" method="GET" class="flex flex-wrap items-center gap-3">
                <input type="search" name="search" aria-label="Cari nama pemohon"
                    value="{{ request()->string('search') }}" placeholder="Cari nama pemohon"
                    class="table-filter px-3 text-sm">
                <button type="submit" class="app-action">Cari</button>

                <!-- Filter Kategori -->
                <div>
                    <select name="leave_category"
                        class="text-gray-700 font-medium rounded-lg py-1.5 px-3 text-xs border border-gray-300 outline-none bg-gray-50 focus:ring-1 focus:ring-blue-500"
                        onchange="this.form.submit()">
                        <option value="">Semua Kategori Izin</option>
                        @foreach ($categories as $key => $label)
                            <option value="{{ $key }}" {{ request('leave_category') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status -->
                <div>
                    <select name="status"
                        class="text-gray-700 font-medium rounded-lg py-1.5 px-3 text-xs border border-gray-300 outline-none bg-gray-50 focus:ring-1 focus:ring-blue-500"
                        onchange="this.form.submit()">
                        <option value="">Semua Status Approval</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending (Menunggu)</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <!-- Filter Pegawai (Admin Only) -->
                @if(auth()->user()->role === 'admin')
                    <div>
                        <select name="user_id" class="text-gray-700 font-medium rounded-lg py-1.5 px-3 text-xs border border-gray-300 outline-none bg-gray-50 focus:ring-1 focus:ring-blue-500" onchange="this.form.submit()">
                            <option value="">Semua Pegawai Non-ASN</option>
                            @foreach(\App\Models\User::with('employee.role')->get() as $u)
                                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->employee?->role?->name ?? 'User' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </form>
            
            <span class="text-xs text-gray-500 font-medium">
                Menampilkan {{ $administrations->total() }} total pengajuan
            </span>
        </div>

        <!-- Tabel Rekapitulasi Izin -->
        <div class="bg-white border rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead class="bg-gray-100 text-gray-700 text-xs uppercase font-bold tracking-wider">
                        <tr>
                            <th class="py-3.5 px-3">#</th>
                            <th class="py-3.5 px-3">Pegawai Non-ASN</th>
                            <th class="py-3.5 px-3">Jenis Izin</th>
                            <th class="py-3.5 px-3">Tanggal Mulai & Berakhir</th>
                            <th class="py-3.5 px-3">Alasan / Uraian</th>
                            <th class="py-3.5 px-3 text-center">Bukti Surat Sakit / Foto</th>
                            <th class="py-3.5 px-3 text-center">Status</th>
                            <th class="py-3.5 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-sm">
                        @forelse ($administrations as $index => $item)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-3 px-3 text-gray-500">{{ $administrations->firstItem() + $index }}</td>
                                
                                <!-- Pegawai Non-ASN -->
                                <td class="py-3 px-3">
                                    <div class="font-bold text-gray-900">{{ $item->user?->name ?? 'User N/A' }}</div>
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-100 text-blue-800">
                                        {{ $item->user?->employee?->role?->name ?? 'Non-ASN' }}
                                    </span>
                                </td>

                                <!-- Jenis Izin -->
                                <td class="py-3 px-3">
                                    <span class="font-semibold text-gray-800">
                                        {{ $categories[$item->leave_category] ?? ucfirst(str_replace('_', ' ', $item->leave_category)) }}
                                    </span>
                                </td>

                                <!-- Tanggal Mulai & Berakhir -->
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5 text-xs text-gray-800 font-semibold">
                                        <span class="text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                                            Mulai: {{ \Carbon\Carbon::parse($item->start_date)->locale('id')->translatedFormat('d M Y') }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-xs text-gray-800 font-semibold mt-1">
                                        <span class="text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200">
                                            Selesai: {{ \Carbon\Carbon::parse($item->end_date)->locale('id')->translatedFormat('d M Y') }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-gray-500 mt-1 font-medium">
                                        Durasi: <span class="font-bold text-gray-700">{{ $item->lama_hari_cuti }} Hari Kerja</span>
                                    </div>
                                </td>

                                <!-- Alasan Izin -->
                                <td class="py-3 px-3 max-w-xs">
                                    <p class="text-xs text-gray-700 leading-relaxed">{{ $item->description }}</p>
                                    <div class="flex items-center gap-2 mt-1 text-[11px] text-gray-400">
                                        <span>Bawa Laptop: {{ $item->bring_laptop ? 'Ya' : 'Tidak' }}</span>
                                        <span>&bull;</span>
                                        <span>Bisa Dihubungi: {{ $item->contacted ? 'Ya' : 'Tidak' }}</span>
                                    </div>
                                </td>

                                <!-- Bukti Foto Surat Sakit / Lampiran -->
                                <td class="py-3 px-3 text-center">
                                    @if($item->attachment)
                                        @php
                                            $ext = strtolower(pathinfo($item->attachment, PATHINFO_EXTENSION));
                                            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
                                            $fileUrl = asset('storage/' . $item->attachment);
                                        @endphp

                                        @if($isImage)
                                            <div class="flex flex-col items-center gap-1">
                                                <img src="{{ $fileUrl }}" alt="Surat Sakit" 
                                                    class="size-14 rounded-lg object-cover border-2 border-purple-400 shadow cursor-pointer hover:scale-105 transition"
                                                    onclick="openSuratModal('{{ $fileUrl }}', 'Bukti Surat Sakit / Keterangan Dokter - {{ $item->user?->name }}')">
                                                <span class="text-[10px] text-purple-700 font-semibold">Surat Sakit</span>
                                            </div>
                                        @else
                                            <a href="{{ $fileUrl }}" target="_blank"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-purple-50 text-purple-700 border border-purple-200 text-xs font-semibold hover:bg-purple-100 transition">
                                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                                Dokumen PDF
                                            </a>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-400 italic">Tidak ada bukti</span>
                                    @endif
                                </td>

                                <!-- Status Approval -->
                                <td class="py-3 px-3 text-center whitespace-nowrap">
                                    @if($item->status === 'approved')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                            &check; Disetujui
                                        </span>
                                    @elseif($item->status === 'rejected')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800" title="{{ $item->approver_note }}">
                                            &times; Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 animate-pulse">
                                            &bull; Menunggu
                                        </span>
                                    @endif

                                    @if($item->approver_note)
                                        <div class="text-[11px] text-gray-500 mt-1 italic max-w-xs truncate" title="{{ $item->approver_note }}">
                                            "{{ $item->approver_note }}"
                                        </div>
                                    @endif
                                </td>

                                <!-- Aksi -->
                                <td class="py-3 px-3 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        {{-- Tombol Cetak Bukti Izin (TTD QR) jika Disetujui --}}
                                        @if($item->status === 'approved')
                                            <a href="{{ route('administration.print', $item->id) }}" target="_blank"
                                                class="btn-colorful px-3 py-1.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-xs font-bold shadow-md transition flex items-center gap-1.5 border border-transparent"
                                                title="Cetak Bukti Cuti Resmi dengan TTD QR">
                                                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                                                <span>Bukti TTD QR</span>
                                            </a>
                                        @endif

                                        {{-- Tombol Setujui / Tolak bagi Admin & Supervisor --}}
                                        @if(in_array(auth()->user()->role, ['admin', 'supervisor']) && $item->status === 'pending')
                                            <form action="{{ route('administration.approve', $item->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menyetujui pengajuan izin ini?')"
                                                    class="btn-colorful px-3 py-1.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl text-xs font-bold shadow-md transition border border-transparent">
                                                    Setujui
                                                </button>
                                            </form>

                                            <button type="button" onclick="openRejectModal({{ $item->id }})"
                                                class="btn-colorful px-3 py-1.5 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white rounded-xl text-xs font-bold shadow-md transition border border-transparent">
                                                Tolak
                                            </button>
                                        @endif

                                        {{-- Edit & Hapus bagi Pemilik saat masih Pending --}}
                                        @if($item->status === 'pending' && ($item->user_id === auth()->id() || in_array(auth()->user()->role, ['admin', 'supervisor'])))
                                            <a href="{{ route('administration.edit', $item->id) }}" class="p-1.5 text-gray-500 hover:text-blue-600 transition" title="Edit">
                                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('administration.destroy', $item->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Hapus pengajuan izin ini?')" class="p-1.5 text-gray-500 hover:text-rose-600 transition" title="Hapus">
                                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-12 text-gray-400">
                                    <svg class="size-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Belum ada data pengajuan izin atau surat sakit yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($administrations->hasPages())
                <div class="p-4 border-t">
                    {{ $administrations->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Preview Surat Sakit / Foto Lampiran (Fixed Closing) -->
    <div id="suratModal" style="display: none;" class="fixed inset-0 bg-black/80 z-[100] items-center justify-center p-4">
        <div class="bg-white rounded-2xl overflow-hidden max-w-2xl w-full shadow-2xl relative" onclick="event.stopPropagation()">
            <div class="p-4 border-b flex justify-between items-center bg-gray-50">
                <h4 id="suratModalTitle" class="text-sm font-bold text-gray-800">Bukti Foto Surat Keterangan Sakit</h4>
                <button type="button" onclick="closeSuratModal()" class="text-gray-400 hover:text-gray-700 text-2xl font-bold w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition">&times;</button>
            </div>
            <div class="p-4 flex justify-center bg-black">
                <img id="suratModalImg" src="" class="max-h-[75vh] object-contain rounded">
            </div>
            <div class="p-3 bg-gray-50 border-t flex justify-between items-center">
                <span class="text-xs text-gray-500">Bukti otentik surat keterangan dokter yang diunggah pegawai.</span>
                <div class="flex items-center gap-2">
                    <a id="suratDownloadBtn" href="" target="_blank" class="btn-colorful px-4 py-1.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-xs font-bold shadow-md transition">
                        Buka Ukuran Asli
                    </a>
                    <button type="button" onclick="closeSuratModal()" class="btn-colorful px-4 py-1.5 bg-gradient-to-r from-gray-700 to-gray-800 text-white rounded-xl text-xs font-bold transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal (Fixed Closing) -->
    <div id="rejectModal" style="display: none;" class="fixed inset-0 bg-black/60 z-[110] items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl relative" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-bold text-gray-800">Tolak Pengajuan Izin</h3>
                <button type="button" onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-700 text-2xl font-bold w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition">&times;</button>
            </div>
            <p class="text-xs text-gray-500 mb-4">Berikan alasan penolakan izin yang akan disampaikan kepada pegawai non-ASN.</p>
            
            <form id="rejectForm" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <textarea name="approver_note" id="approver_note" rows="3" required
                        class="w-full border border-gray-300 rounded-lg p-2.5 text-xs focus:ring-2 focus:ring-red-400 focus:outline-none"
                        placeholder="Contoh: Jadwal jaga pengamanan sedang kekurangan personel, mohon ajukan di tanggal lain..."></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeRejectModal()" 
                        class="px-4 py-2 border rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100 transition">
                        Batal
                    </button>
                    <button type="submit" 
                        class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-semibold shadow transition">
                        Kirim Penolakan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openSuratModal(src, title) {
            const modal = document.getElementById('suratModal');
            document.getElementById('suratModalImg').src = src;
            document.getElementById('suratModalTitle').innerText = title;
            document.getElementById('suratDownloadBtn').href = src;
            modal.style.display = 'flex';
        }

        function closeSuratModal() {
            const modal = document.getElementById('suratModal');
            if (modal) modal.style.display = 'none';
        }

        function openRejectModal(id) {
            const modal = document.getElementById('rejectModal');
            const form = document.getElementById('rejectForm');
            form.action = '/administration/' + id + '/reject';
            document.getElementById('approver_note').value = '';
            modal.style.display = 'flex';
        }

        function closeRejectModal() {
            const modal = document.getElementById('rejectModal');
            if (modal) modal.style.display = 'none';
        }

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSuratModal();
                closeRejectModal();
            }
        });

        // Close on backdrop click
        document.addEventListener('DOMContentLoaded', function() {
            const suratEl = document.getElementById('suratModal');
            if (suratEl) {
                suratEl.addEventListener('click', function(e) {
                    if (e.target === this) closeSuratModal();
                });
            }

            const rejectEl = document.getElementById('rejectModal');
            if (rejectEl) {
                rejectEl.addEventListener('click', function(e) {
                    if (e.target === this) closeRejectModal();
                });
            }
        });
    </script>
</x-layouts.layout>
