<div class="w-[8.5%] h-screen fixed z-50"> 
    <nav class="pt-8 items-center bg-white flex flex-col space-y-7 h-full relative border-r relative z-50">
        @if(auth()->user()->role === 'admin')
            {{-- Admin Menu: Full Command Center --}}
            <x-sidebar.dashboard :active="$active" />
            <x-sidebar.attendance :active="$active" />
            <x-sidebar.recap :active="$active" />
            <x-sidebar.administration :active="$active" />
            <x-sidebar.project :active="$active" />
            <x-sidebar.activity :active="$active" />
            <x-sidebar.admin :active="$active" />
        @else
            {{-- Pegawai Non-ASN Menu: Absensi, Cek Kehadiran, & Pengajuan Izin/Cuti --}}
            <x-sidebar.attendance :active="$active" />
            <x-sidebar.recap :active="$active" />
            <x-sidebar.administration :active="$active" />
        @endif
    </nav>
</div>
