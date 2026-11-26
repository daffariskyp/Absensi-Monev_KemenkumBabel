<x-layout-app :title="$title">

    <div class="flex flex-col min-h-screen">
        <!-- Navbar -->
        <x-layouts.navbar />

        <div class="app-shell flex flex-1">
            <!-- Sidebar -->
            <x-layouts.sidebar :active="$active ?? ''" />

            <!-- Main Content -->
            <main class="app-content relative flex-1 px-6 py-4">
                {{ $slot }}
            </main>
        </div>

        <footer class="app-footer">
            <span>&copy; {{ now()->year }} Fadhil Raihan</span>
            <span><strong>{{ config('app.name') }}</strong> &middot; {{ config('app.brand_description') }}</span>
        </footer>
    </div>

</x-layout-app>
