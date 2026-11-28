<div class="app-sidebar" id="app-sidebar">
    <nav aria-label="Navigasi utama" class="pt-8 items-center bg-white flex flex-col space-y-7 h-full relative border-r relative z-50">
        @if(auth()->user()->role === 'admin')
            {{-- Admin Menu: Presensi, Monev, Izin/Cuti, & Manajemen Non-ASN --}}
            <x-sidebar.dashboard :active="$active" />
            <x-sidebar.attendance :active="$active" />
            <x-sidebar.recap :active="$active" />
            <x-sidebar.administration :active="$active" />
            <x-sidebar.admin :active="$active" />
        @elseif(auth()->user()->role === 'supervisor')
            {{-- Koordinator / Supervisor / Kabag Menu: Akses Dashboard, Presensi/Absen Harian, Rekapan Absensi, & Izin/Cuti --}}
            <x-sidebar.dashboard :active="$active" />
            <x-sidebar.attendance :active="$active" />
            <x-sidebar.recap :active="$active" />
            <x-sidebar.administration :active="$active" />
        @else
            {{-- Pegawai Non-ASN Menu: Absensi, Cek Kehadiran, & Pengajuan Izin/Cuti --}}
            <x-sidebar.attendance :active="$active" />
            <x-sidebar.recap :active="$active" />
            <x-sidebar.administration :active="$active" />
        @endif
    </nav>
</div>
