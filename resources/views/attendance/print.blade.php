<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
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
        <a href="{{ route('attendance.recap') }}" class="text-sm font-semibold text-blue-600 hover:underline">&larr; Kembali ke Rekap</a>
        <button onclick="window.print()" class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold shadow hover:bg-blue-700">
            Cetak / Simpan PDF
        </button>
    </div>

    <!-- Kop Surat Instansi / Non-ASN -->
    <div class="border-b-2 border-gray-800 pb-4 mb-6 text-center">
        <h2 class="text-xl font-bold tracking-wide uppercase">PEMERINTAH REPUBLIK INDONESIA</h2>
        <h3 class="text-lg font-bold tracking-wider uppercase">LAPORAN REKAPITULASI PRESENSI & MONITORING EVALUASI (MONEV)</h3>
        <h4 class="text-sm font-semibold text-gray-700 uppercase">TENAGA NON-ASN / PPNPN TAHUN {{ $year }}</h4>
        <p class="text-xs text-gray-500 mt-1">Periode Bulan: {{ Carbon\Carbon::create(null, $month, 1)->locale('id')->translatedFormat('F Y') }} | Lokasi: {{ $office->office_name }}</p>
    </div>

    @if($targetUser)
        <div class="mb-4 text-xs space-y-1">
            <div class="grid grid-cols-4 max-w-md">
                <span class="font-semibold text-gray-700">Nama Pegawai</span>
                <span class="col-span-3">: {{ $targetUser->name }}</span>
                <span class="font-semibold text-gray-700">Jabatan / Role</span>
                <span class="col-span-3">: {{ $targetUser->employee?->role?->name ?? 'Tenaga Non-ASN' }}</span>
                <span class="font-semibold text-gray-700">NIK / ID</span>
                <span class="col-span-3">: {{ $targetUser->employee?->nik ?? '-' }}</span>
            </div>
        </div>
    @endif

    <table class="w-full border-collapse border border-gray-400 text-xs mb-8">
        <thead>
            <tr class="bg-gray-100 text-center font-bold">
                <th class="border border-gray-400 p-2 w-8">No</th>
                <th class="border border-gray-400 p-2 w-24">Tanggal</th>
                @if(!$targetUser)
                    <th class="border border-gray-400 p-2">Nama Pegawai</th>
                @endif
                <th class="border border-gray-400 p-2 w-20">Masuk</th>
                <th class="border border-gray-400 p-2 w-20">Pulang</th>
                <th class="border border-gray-400 p-2 w-20">Status</th>
                <th class="border border-gray-400 p-2">Uraian Monev Aktivitas Harian</th>
                <th class="border border-gray-400 p-2">Kendala (Min 100 Karakter)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $index => $item)
                <tr>
                    <td class="border border-gray-400 p-2 text-center">{{ $index + 1 }}</td>
                    <td class="border border-gray-400 p-2 text-center">
                        {{ Carbon\Carbon::parse($item->date)->locale('id')->translatedFormat('d/m/Y') }}
                    </td>
                    @if(!$targetUser)
                        <td class="border border-gray-400 p-2 font-medium">{{ $item->user?->name }}</td>
                    @endif
                    <td class="border border-gray-400 p-2 text-center">
                        {{ $item->check_in_time ? Carbon\Carbon::parse($item->check_in_time)->format('H:i') : '-' }}
                    </td>
                    <td class="border border-gray-400 p-2 text-center">
                        {{ $item->check_out_time ? Carbon\Carbon::parse($item->check_out_time)->format('H:i') : '-' }}
                    </td>
                    <td class="border border-gray-400 p-2 text-center capitalize font-semibold">
                        {{ $item->status }}
                    </td>
                    <td class="border border-gray-400 p-2">{{ $item->monev_activity ?? '-' }}</td>
                    <td class="border border-gray-400 p-2 italic">{{ $item->monev_obstacle ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $targetUser ? 7 : 8 }}" class="border border-gray-400 p-4 text-center text-gray-500">
                        Tidak ada catatan kehadiran pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tanda Tangan Pengesahan -->
    <div class="grid grid-cols-2 text-xs pt-6 gap-8">
        <div class="text-center">
            <p>Mengetahui,</p>
            <p class="font-bold">Pejabat Pembuat Komitmen / Atasan Langsung</p>
            <div class="h-20"></div>
            <p class="font-bold underline">( .................................................... )</p>
            <p class="text-gray-600">NIP. ............................................</p>
        </div>
        <div class="text-center">
            <p>{{ Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</p>
            <p class="font-bold">Pegawai Non-ASN Bersangkutan</p>
            <div class="h-20"></div>
            <p class="font-bold underline">( {{ $targetUser ? $targetUser->name : '....................................................' }} )</p>
            <p class="text-gray-600">Tenaga Pendukung Non-ASN</p>
        </div>
    </div>
</body>
</html>
