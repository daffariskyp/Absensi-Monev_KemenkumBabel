<x-layouts.layout :title="$title" :active="$active">
    <div class="mx-auto max-w-3xl my-5">
        <div class="bg-white rounded-lg border-2 border-gray-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4 border-b pb-3">
                <h1 class="text-2xl font-bold text-gray-800">Pengajuan Izin & Cuti Non-ASN</h1>
                <span class="text-xs bg-blue-100 text-blue-800 px-3 py-1 rounded-full font-medium">Pegawai Non-ASN</span>
            </div>

            <form action="{{ route('administration.store') }}" method="POST" enctype="multipart/form-data" id="administrationForm">
                @csrf
                <div class="space-y-4">
                    <!-- Leave Category -->
                    <div class="flex flex-col">
                        <label class="text-sm font-semibold text-gray-700 mb-1">Jenis Izin / Kategori Cuti <span class="text-red-500">*</span></label>
                        <select name="leave_category" class="border px-4 py-2 text-sm bg-white text-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                            @foreach ($categories as $key => $label)
                                <option value="{{ $key }}" {{ old('leave_category') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('leave_category')
                            <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Date Inputs -->
                    <div class="flex flex-col">
                        <label class="text-sm font-semibold text-gray-700 mb-1">Rentang Tanggal <span class="text-red-500">*</span></label>
                        <div class="flex gap-4">
                            <div class="w-1/2">
                                <label for="start_date" class="text-xs text-gray-500">Mulai Tanggal:</label>
                                <input type="date" name="start_date" id="start_date" 
                                    class="w-full border px-3 py-2 text-sm rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" 
                                    required value="{{ old('start_date', date('Y-m-d')) }}">
                                @error('start_date')
                                    <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="w-1/2">
                                <label for="end_date" class="text-xs text-gray-500">Sampai Tanggal:</label>
                                <input type="date" name="end_date" id="end_date" 
                                    class="w-full border px-3 py-2 text-sm rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" 
                                    required value="{{ old('end_date', date('Y-m-d')) }}">
                                @error('end_date')
                                    <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="flex flex-col">
                        <label class="text-sm font-semibold text-gray-700 mb-1" for="description">Uraian / Alasan Izin <span class="text-red-500">*</span></label>
                        <textarea name="description" id="description" rows="3" 
                            class="border px-4 py-2 text-sm rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                            placeholder="Jelaskan keperluan izin / cuti Anda secara rinci (minimal 10 karakter)..." required>{{ old('description') }}</textarea>
                        @error('description')
                            <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Attachment Upload (Surat Dokter / Nota Tugas / Bukti) -->
                    <div class="flex flex-col">
                        <label class="text-sm font-semibold text-gray-700 mb-1" for="attachment">
                            Lampiran Bukti Pendukung (Surat Dokter / Nota Dinas / dsb.)
                        </label>
                        <input type="file" name="attachment" id="attachment" accept=".pdf,.jpg,.jpeg,.png"
                            class="border px-3 py-2 text-sm rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50">
                        <span class="text-xs text-gray-500 mt-1">Format diperbolehkan: PDF, JPG, PNG (Maksimal 3MB). Wajib untuk izin sakit jika lebih dari 1 hari.</span>
                        @error('attachment')
                            <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Radio Option -->
                    <div class="flex gap-4 pt-2">
                        <div class="w-1/2">
                            <label class="text-sm font-semibold text-gray-700 block mb-1">Membawa Laptop / Perangkat Kerja?</label>
                            <div class="flex border p-2 rounded-md bg-white space-x-4">
                                <label class="flex items-center text-sm text-gray-700 cursor-pointer">
                                    <input type="radio" name="bring_laptop" class="mr-2 accent-blue-600" value="0" {{ old('bring_laptop', '0') == '0' ? 'checked' : '' }}> Tidak
                                </label>
                                <label class="flex items-center text-sm text-gray-700 cursor-pointer">
                                    <input type="radio" name="bring_laptop" class="mr-2 accent-blue-600" value="1" {{ old('bring_laptop') == '1' ? 'checked' : '' }}> Ya
                                </label>
                            </div>
                        </div>
                        <div class="w-1/2">
                            <label class="text-sm font-semibold text-gray-700 block mb-1">Dapat Dihubungi saat Izin?</label>
                            <div class="flex border p-2 rounded-md bg-white space-x-4">
                                <label class="flex items-center text-sm text-gray-700 cursor-pointer">
                                    <input type="radio" name="contacted" class="mr-2 accent-blue-600" value="0" {{ old('contacted') == '0' ? 'checked' : '' }}> Tidak
                                </label>
                                <label class="flex items-center text-sm text-gray-700 cursor-pointer">
                                    <input type="radio" name="contacted" class="mr-2 accent-blue-600" value="1" {{ old('contacted', '1') == '1' ? 'checked' : '' }}> Ya
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center justify-between pt-6 border-t">
                        <a href="{{ route('attendance.index') }}" 
                            class="btn-colorful px-5 py-2.5 border border-gray-300 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-bold text-xs transition flex items-center gap-1.5 shadow-sm active:shadow-inner">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                            Kembali ke Absensi
                        </a>
                        <button type="submit" 
                            style="background-color: #2563eb; color: #ffffff !important; border: 2px solid #facc15;"
                            class="btn-colorful px-7 py-2.5 hover:bg-blue-700 text-white rounded-xl font-black text-sm shadow-lg hover:shadow-xl transition flex items-center gap-2 active:shadow-inner cursor-pointer">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Kirim Permohonan Izin / Cuti
                        </button>
                    </div>
                </div>
            </form>
        </div>

        @if(isset($pastLeaves) && $pastLeaves->isNotEmpty())
            <div class="mt-8 bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h3 class="font-bold text-gray-800 text-sm mb-3 flex items-center gap-2">
                    <svg class="size-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Riwayat Pengajuan Izin / Cuti Anda Sebelumnya
                </h3>

                <div class="divide-y text-xs">
                    @foreach($pastLeaves as $past)
                        <div class="py-3 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                            <div>
                                <div class="font-bold text-gray-800">
                                    {{ $categories[$past->leave_category] ?? $past->leave_category }}
                                </div>
                                <div class="text-gray-500 text-[11px] mt-0.5">
                                    {{ \Carbon\Carbon::parse($past->start_date)->locale('id')->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($past->end_date)->locale('id')->translatedFormat('d M Y') }} 
                                    ({{ $past->lama_hari_cuti }} hari kerja)
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
</x-layouts.layout>
