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
