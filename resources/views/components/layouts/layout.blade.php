<x-layout-app :title="$title">
    {{-- @vite('resources/css/app.css') --}}
    @notifyCss <!-- Tambahkan CSS Laravel Notify -->

    <div class="flex flex-col min-h-screen">
        <!-- Navbar -->
        <x-layouts.navbar :title="$title" :active="$active ?? ''"/>

        <div class="flex flex-1">
            <!-- Sidebar -->
            <x-layouts.sidebar :active="$active ?? ''" />

            <!-- Main Content -->
            <main class="relative flex-1 px-6 py-4 ml-[8%]">
                <!-- Laravel Notify -->
                <x-notify::notify class="fixed top-5 right-5 z-[9999] shadow-lg" />

                {{ $slot }}
            </main>
        </div>
    </div>

    @notifyJs <!-- Tambahkan JavaScript Laravel Notify -->

    @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'supervisor'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // SweetAlert Notifikasi Pengajuan Cuti Menunggu Persetujuan untuk Admin & Koordinator / Supervisor
                fetch("{{ route('administration.pendingCount') }}")
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.count > 0) {
                            if (typeof window.notifyPendingLeaves === 'function') {
                                window.notifyPendingLeaves(data.count, data.latest_name, data.latest_category);
                            } else if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'warning',
                                    title: '<span style="color:#1e3a8a; font-weight:800;">Pengajuan Cuti Menunggu Persetujuan!</span>',
                                    html: `
                                        <div style="text-align: left; font-size: 13px; color: #374151; line-height: 1.6;">
                                            <p style="margin-bottom: 8px;">Terdapat <b style="color: #2563eb; font-size: 16px;">${data.count}</b> pengajuan cuti/izin pegawai baru yang memerlukan tindakan atasan.</p>
                                            ${data.latest_name ? `
                                                <div style="background: #eff6ff; padding: 10px 14px; border-radius: 12px; border: 1px solid #bfdbfe; margin-top: 10px;">
                                                    <div style="font-weight: 700; color: #1e40af; font-size: 12px; text-transform: uppercase;">Pengajuan Terbaru:</div>
                                                    <div style="color: #1f2937; margin-top: 2px;"><b>${data.latest_name}</b> - <span style="color:#4338ca; font-weight:600;">${data.latest_category || 'Izin Pegawai'}</span></div>
                                                </div>
                                            ` : ''}
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
                                        window.location.href = "{{ route('administration.index') }}?status=pending";
                                    }
                                });
                            }
                        }
                    })
                    .catch(err => console.log('Pending leaves check:', err));
            });
        </script>
    @endif

    <style>
        /* Pastikan Notify tidak tertutup elemen lain */
        x-notify::notify {
            z-index: 9999 !important;
            position: fixed !important;
            top: 10px !important;
            right: 20px !important;
        }

        /* Hindari masalah overflow yang bisa menutupi notify */
        body, html {
            overflow: visible !important;
        }
    </style>
</x-layout-app>
