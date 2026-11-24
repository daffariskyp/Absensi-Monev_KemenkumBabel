<x-layouts.layout :title="$title" :active="$active">
    <main class="flex-1 p-6 bg-white shadow-md">
        <h1 class="text-2xl font-semibold mb-4">Detail Cuti</h1>
        <h1 class="mt-5 text-gray-900 text-xl mb-2">{{ $administration->user->name }}</h1>

        <div class="card">
            <div class="card-body">
                <p><span class="mt-1 text-gray-900 font-semibold">Kategori Cuti :</span>
                    {{ ucfirst(str_replace('_', ' ', $administration->leave_category)) }}
                </p>
                <p><span class="mt-1 text-gray-900 font-semibold">Tanggal Mulai :</span>
                    {{ \Carbon\Carbon::parse($administration->start_date)->locale('id')->translatedFormat('d F Y') }}
                </p>
                <p><span class="mt-1 text-gray-900 font-semibold">Tanggal Akhir :</span>
                    {{ \Carbon\Carbon::parse($administration->end_date)->locale('id')->translatedFormat('d F Y') }}
                </p>
                <p class="mt-1"><span class="mt-1 text-gray-900 font-semibold">Deskripsi :</span>
                    {{ $administration->description ?? 'Tidak ada deskripsi.' }}
                </p>

                <div class="grid grid-cols-2 gap-2 mb-4">
                    <div>
                        <p class="mt-1 text-gray-900 font-semibold">Membawa Laptop :</p>
                        <p>
                            {{ $administration->bring_laptop ? 'Ya' : 'Tidak' }}
                        </p>
                    </div>

                    <div>
                        <p class="mt-1 text-gray-900 font-semibold">Dapat Dihubungi :</p>
                        <p>
                            {{ $administration->contacted ? 'Ya' : 'Tidak' }}
                        </p>
                    </div>
                </div>

                <div class="mt-10">
                    <a href="{{ route('administrations.index') }}"
                        class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 flex w-20 items-center justify-center gap-1">
                        <svg class="size-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="6"
                                d="m15 19-7-7 7-7" />
                        </svg>
                        <span>
                            Back
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </main>
</x-layouts.layout>
