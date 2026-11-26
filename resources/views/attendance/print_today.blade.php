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
            body { font-size: 11pt; }
            @page { margin: 1.5cm; }
        }
    </style>
</head>
<body class="bg-white text-gray-900 p-8">
    <div class="no-print mb-6 flex justify-between items-center bg-gray-100 p-4 rounded-lg">
        <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-blue-600 hover:underline">&larr; Kembali ke Dashboard Utama</a>
        <button onclick="window.print()" class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold shadow hover:bg-blue-700">
            Download / Cetak PDF
        </button>
    </div>

    <!-- Kop Laporan Resmi Non-ASN -->
    <div class="border-b-2 border-gray-900 pb-4 mb-6 text-center">
        <h2 class="text-xl font-bold tracking-wide uppercase">PEMERINTAH REPUBLIK INDONESIA</h2>
        <h3 class="text-lg font-bold tracking-wider uppercase">LAPORAN AKTIVITAS & PRESENSI HARIAN TENAGA NON-ASN / PPNPN</h3>
        <p class="text-xs font-semibold tracking-wide">{{ config('app.name') }}</p>
        <p class="text-sm font-semibold text-gray-700 mt-1">Hari / Tanggal: {{ $todayFormatted }} | Lokasi: {{ $office->office_name }}</p>
    </div>

    <table class="w-full border-collapse border border-gray-400 text-xs mb-8">
        <thead>
            <tr class="bg-gray-100 text-center font-bold">
                <th class="border border-gray-400 p-2 w-8">No</th>
                <th class="border border-gray-400 p-2">Nama Pegawai</th>
                <th class="border border-gray-400 p-2 w-28">Jabatan / Role</th>
                <th class="border border-gray-400 p-2 w-28">NIK</th>
                <th class="border border-gray-400 p-2 w-16">Masuk</th>
                <th class="border border-gray-400 p-2 w-16">Pulang</th>
                <th class="border border-gray-400 p-2 w-36">Status Kehadiran</th>
                <th class="border border-gray-400 p-2">Keterangan / Uraian Tugas / Alasan Izin</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recap as $index => $row)
                <tr>
                    <td class="border border-gray-400 p-2 text-center">{{ $index + 1 }}</td>
                    <td class="border border-gray-400 p-2 font-bold">{{ $row['name'] }}</td>
                    <td class="border border-gray-400 p-2 text-center font-semibold">{{ $row['role'] }}</td>
                    <td class="border border-gray-400 p-2 text-center">{{ $row['nik'] }}</td>
                    <td class="border border-gray-400 p-2 text-center font-medium">{{ $row['check_in'] }}</td>
                    <td class="border border-gray-400 p-2 text-center font-medium">{{ $row['check_out'] }}</td>
                    <td class="border border-gray-400 p-2 text-center font-semibold">
                        @if(str_contains($row['status'], 'Hadir'))
                            <span class="text-emerald-700">{{ $row['status'] }}</span>
                        @elseif(str_contains($row['status'], 'Izin'))
                            <span class="text-blue-700">{{ $row['status'] }}</span>
                        @else
                            <span class="text-red-700 font-bold">{{ $row['status'] }}</span>
                        @endif
                    </td>
                    <td class="border border-gray-400 p-2">
                        <div>{{ $row['keterangan'] }}</div>
                        @if(!empty($row['activities']) && $row['activities']->isNotEmpty())
                            <div class="mt-1.5 pt-1.5 border-t border-gray-200">
                                <span class="font-bold underline block mb-1">Rincian Monev Kegiatan ({{ $row['activities']->count() }}):</span>
                                <ol class="list-decimal list-inside space-y-1">
                                    @foreach($row['activities'] as $act)
                                        <li>
                                            <strong>[{{ substr($act->time, 0, 5) }}]</strong> {{ $act->activity_description }}
                                            @if($act->obstacle && $act->obstacle !== 'Tidak ada kendala')
                                                <span class="italic text-gray-600">(Kendala: {{ $act->obstacle }})</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ol>
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="border border-gray-400 p-4 text-center text-gray-500">
                        Tidak ada data pegawai.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tanda Tangan dengan QR Code Digital -->
    <div class="grid grid-cols-2 text-xs pt-8 gap-8">
        <div class="flex flex-col items-center text-center">
            <p>Mengetahui,</p>
            <p class="font-bold">Pejabat Pembuat Komitmen (PPK) / Kasubag TU</p>
            <div class="my-2 p-1.5 border border-gray-800 rounded-xl bg-white shadow-sm inline-block">
                @if(isset($qrSvg))
                    {!! $qrSvg !!}
                @endif
            </div>
            <p class="text-[10px] text-gray-500 font-semibold mb-1">Tanda Tangan Digital Terverifikasi QR</p>
            <p class="font-bold underline text-sm">Kasubag Kepegawaian & Tata Usaha</p>
            <p class="text-gray-600">Kementerian Hukum Republik Indonesia</p>
        </div>
        <div class="flex flex-col items-center text-center">
            <p>{{ $todayFormatted }}</p>
            <p class="font-bold">Koordinator Pengelola Pegawai Non-ASN</p>
            <div class="h-28 flex items-end justify-center">
                <p class="font-bold underline text-sm">( {{ auth()->user()->name }} )</p>
            </div>
            <p class="text-gray-600">Pengelola Kepegawaian</p>
        </div>
    </div>
</body>
</html>
