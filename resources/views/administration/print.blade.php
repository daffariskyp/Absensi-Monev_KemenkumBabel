<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} | {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('kemenkum.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-card { border: none !important; box-shadow: none !important; padding: 0 !important; }
        }
        @page {
            size: A4;
            margin: 1.5cm;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 font-sans p-6">
    <!-- Action Bar (Hidden on Print) -->
    <div class="max-w-3xl mx-auto mb-6 flex items-center justify-between no-print bg-white p-4 rounded-2xl shadow-sm border">
        <div class="flex items-center gap-2">
            <a href="{{ url()->previous() }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition flex items-center gap-1.5">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali
            </a>
            <span class="text-xs text-gray-500 font-medium">Dokumen Bukti Cuti / Izin Resmi Non-ASN</span>
        </div>
        <button type="button" onclick="window.print()" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-2">
            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
            Cetak / Simpan PDF
        </button>
    </div>

    <!-- Printable Formal Document Card -->
    <div class="max-w-3xl mx-auto bg-white p-8 md:p-12 rounded-2xl shadow-md border print-card">
        <!-- KOP SURAT RESMI -->
        <div class="flex items-center justify-between pb-4 border-b-2 border-gray-900 mb-6">
            <div class="flex items-center gap-4">
                <img src="{{ asset('kemenkum.png') }}" alt="Kementerian Hukum" class="w-16 h-16 object-contain" onerror="this.style.display='none'">
                <div>
                    <h2 class="text-xs uppercase font-extrabold tracking-widest text-gray-600">KEMENTERIAN HUKUM REPUBLIK INDONESIA</h2>
                    <h1 class="text-base font-extrabold uppercase text-gray-900 tracking-wide">{{ $office->office_name ?? 'KANTOR WILAYAH KEPULAUAN BANGKA BELITUNG' }}</h1>
                    <p class="text-[11px] font-semibold text-gray-600 mt-0.5">{{ config('app.name') }}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-black rounded-full border border-emerald-300">
                    DISETUJUI RESMI
                </span>
            </div>
        </div>

        <!-- JUDUL SURAT -->
        <div class="text-center my-6">
            <h3 class="text-lg font-black uppercase tracking-wider text-gray-900 underline decoration-2 underline-offset-4">
                SURAT KETERANGAN PERSETUJUAN CUTI / IZIN NON-ASN
            </h3>
            <p class="text-xs text-gray-600 font-semibold mt-1">Nomor Registrasi: {{ $docNumber }}</p>
        </div>

        <p class="text-xs text-gray-700 leading-relaxed mb-4">
            Berdasarkan permohonan yang diajukan melalui {{ config('app.name') }}, dengan ini pimpinan memberikan persetujuan izin/cuti kepada pegawai di bawah ini:
        </p>

        <!-- DATA PEGAWAI -->
        <div class="bg-gray-50 border rounded-xl p-4 mb-5 text-xs space-y-2">
            <div class="grid grid-cols-3 gap-2">
                <span class="font-bold text-gray-600">Nama Pegawai:</span>
                <span class="col-span-2 font-extrabold text-gray-900 text-sm">{{ $administration->user?->name ?? '-' }}</span>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <span class="font-bold text-gray-600">Nomor Induk Kependudukan (NIK):</span>
                <span class="col-span-2 font-semibold text-gray-800">{{ $administration->user?->employee?->nik ?? '-' }}</span>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <span class="font-bold text-gray-600">Jabatan / Role Tenaga Non-ASN:</span>
                <span class="col-span-2 font-bold text-indigo-700">{{ $administration->user?->employee?->role?->name ?? 'Pegawai Non-ASN' }}</span>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <span class="font-bold text-gray-600">Unit Kerja / Lokasi:</span>
                <span class="col-span-2 font-medium text-gray-800">{{ $office->office_name ?? 'Kantor Wilayah' }}</span>
            </div>
        </div>

        <!-- RINCIAN PERSETUJUAN CUTI -->
        <h4 class="text-xs font-extrabold uppercase tracking-wider text-gray-800 mb-2">Rincian Izin / Cuti yang Disetujui:</h4>
        <div class="border rounded-xl overflow-hidden mb-6 text-xs">
            <table class="w-full border-collapse">
                <tbody class="divide-y divide-gray-200">
                    <tr class="bg-gray-50/50">
                        <td class="py-2.5 px-4 font-bold text-gray-700 w-1/3">Jenis Permohonan</td>
                        <td class="py-2.5 px-4 font-extrabold text-gray-900">
                            {{ $categories[$administration->leave_category] ?? ucfirst(str_replace('_', ' ', $administration->leave_category)) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2.5 px-4 font-bold text-gray-700">Tanggal Mulai Izin s/d Selesai</td>
                        <td class="py-2.5 px-4 font-extrabold text-blue-900">
                            {{ \Carbon\Carbon::parse($administration->start_date)->locale('id')->translatedFormat('l, d F Y') }} s/d {{ \Carbon\Carbon::parse($administration->end_date)->locale('id')->translatedFormat('l, d F Y') }}
                        </td>
                    </tr>
                    <tr class="bg-gray-50/50">
                        <td class="py-2.5 px-4 font-bold text-gray-700">Jumlah Durasi Cuti</td>
                        <td class="py-2.5 px-4 font-extrabold text-gray-900">
                            {{ $administration->lama_hari_cuti }} ({{ $administration->lama_hari_cuti }} Hari Kerja)
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2.5 px-4 font-bold text-gray-700">Alasan / Uraian Keperluan</td>
                        <td class="py-2.5 px-4 text-gray-800 leading-relaxed">
                            {{ $administration->description }}
                        </td>
                    </tr>
                    <tr class="bg-gray-50/50">
                        <td class="py-2.5 px-4 font-bold text-gray-700">Catatan Persetujuan Atasan</td>
                        <td class="py-2.5 px-4 text-emerald-800 font-semibold italic">
                            "{{ $administration->approver_note ?? 'Disetujui secara resmi oleh atasan langsung.' }}"
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p class="text-xs text-gray-600 leading-relaxed mb-8">
            Demikian surat keterangan persetujuan ini diterbitkan melalui sistem elektronik dan memiliki kekuatan pembuktian yang sah untuk digunakan sebagaimana mestinya.
        </p>

        <!-- TANDA TANGAN ELEKTRONIK & QR CODE VERIFIKASI -->
        <div class="flex justify-between items-end pt-4 border-t border-gray-200">
            <div class="flex items-center gap-3">
                <div class="p-2 border-2 border-gray-800 rounded-xl bg-white shadow-sm inline-block">
                    {!! $qrSvg !!}
                </div>
                <div class="text-[11px] text-gray-500 max-w-xs space-y-0.5">
                    <div class="font-extrabold text-gray-800 uppercase flex items-center gap-1">
                        <svg class="size-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                        Tanda Tangan Digital Terverifikasi
                    </div>
                    <p>Pindai (scan) QR Code di atas menggunakan smartphone untuk memverifikasi keaslian surat persetujuan cuti ini.</p>
                </div>
            </div>

            <div class="text-right text-xs">
                <p class="text-gray-500">{{ $office->office_name ?? 'Pangkalpinang' }}, {{ \Carbon\Carbon::parse($administration->approved_at ?? $administration->updated_at)->locale('id')->translatedFormat('d F Y') }}</p>
                <p class="font-bold text-gray-800 mt-1">Menyetujui,</p>
                <p class="text-gray-600">Pejabat Pembina Kepegawaian / Atasan</p>
                <div class="h-12"></div>
                <p class="font-black text-gray-900 underline text-sm">{{ $administration->approver?->name ?? 'Kasubag Kepegawaian & Tata Usaha' }}</p>
                <p class="text-[11px] text-gray-500">Kementerian Hukum Republik Indonesia</p>
            </div>
        </div>
    </div>
</body>
</html>
