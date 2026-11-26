<x-layouts.layout :title="$title" :active="$active">
    <div class="max-w-3xl mx-auto py-8 px-4">
        <div class="bg-white border rounded-2xl p-6 shadow-sm">
            <div class="flex items-center gap-3 pb-4 mb-6 border-b">
                <div class="size-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Pengaturan Titik Lokasi Kantor & Ketentuan Presensi</h1>
                    <p class="text-xs text-gray-500">Tentukan koordinat titik kantor dan radius maksimal agar absensi pegawai Non-ASN hanya diterima di lokasi kantor.</p>
                </div>
            </div>

            <form action="{{ route('attendance.settings.update') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Kantor / Instansi <span class="text-red-500">*</span></label>
                    <input type="text" name="office_name" value="{{ old('office_name', $office->office_name) }}" required
                        class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @error('office_name') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Latitude Kantor <span class="text-red-500">*</span></label>
                        <input type="text" name="latitude" id="officeLat" value="{{ old('latitude', $office->latitude) }}" required
                            class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @error('latitude') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Longitude Kantor <span class="text-red-500">*</span></label>
                        <input type="text" name="longitude" id="officeLng" value="{{ old('longitude', $office->longitude) }}" required
                            class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @error('longitude') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Quick Presets -->
                <div class="flex flex-wrap items-center justify-between gap-2 p-3.5 bg-blue-50/80 border-2 border-blue-200 rounded-2xl text-xs shadow-sm">
                    <span class="font-black text-blue-900 uppercase tracking-wide">Preset Lokasi Cepat:</span>
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" onclick="setPresetKanwilBabel()" 
                            style="background-color: #2563eb; color: #ffffff !important;"
                            class="btn-colorful px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition flex items-center gap-1.5 shadow-md active:shadow-inner active:scale-95 border border-transparent cursor-pointer">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                            Kanwil Kemenkumham Babel
                        </button>
                        <button type="button" onclick="getCurrentLocation()" 
                            style="background-color: #059669; color: #ffffff !important;"
                            class="btn-colorful px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition flex items-center gap-1.5 shadow-md active:shadow-inner active:scale-95 border border-transparent cursor-pointer">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            Gunakan GPS Saya Sekarang
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Radius Toleransi Lokasi (Meter) <span class="text-red-500">*</span></label>
                    <input type="number" name="radius_meters" id="radiusMeters" value="{{ old('radius_meters', $office->radius_meters) }}" required min="20" max="5000"
                        class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <p class="text-xs text-gray-400 mt-1">Disarankan <strong>500 - 1000 meter</strong> untuk kawasan komplek perkantoran yang luas agar sinyal GPS di dalam ruangan tetap terakomodasi.</p>
                    @error('radius_meters') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2 border-t">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Mulai Absen Masuk:</label>
                        <input type="time" name="check_in_start" value="{{ old('check_in_start', $office->check_in_start) }}" required
                            class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Batas Tepat Waktu (Jam Masuk):</label>
                        <input type="time" name="check_in_end" value="{{ old('check_in_end', $office->check_in_end) }}" required
                            class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Jam Mulai Absen Pulang:</label>
                        <input type="time" name="check_out_start" value="{{ old('check_out_start', $office->check_out_start) }}" required
                            class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:outline-none">
                    </div>
                </div>

                <div class="pt-6 border-t flex justify-end items-center gap-3">
                    <a href="{{ route('attendance.index') }}" 
                        style="background-color: #fff1f2; color: #9f1239; border: 2px solid #fecdd3;"
                        class="btn-colorful px-5 py-2.5 rounded-xl text-xs font-bold transition shadow-sm active:shadow-inner active:scale-95">
                        Batal
                    </a>
                    <button type="submit" 
                        style="background-color: #2563eb; color: #ffffff !important; border: 2px solid #facc15;"
                        class="btn-colorful px-7 py-2.5 hover:bg-blue-700 text-white rounded-xl text-sm font-black shadow-lg hover:shadow-xl transition active:shadow-inner active:scale-95 flex items-center gap-2 cursor-pointer">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function setPresetKanwilBabel() {
            document.querySelector('input[name="office_name"]').value = "Kanwil Kemenkumham Kepulauan Bangka Belitung";
            document.getElementById('officeLat').value = "-2.1516954";
            document.getElementById('officeLng').value = "106.1568149";
            document.getElementById('radiusMeters').value = "500";
            alert("Preset Kanwil Kemenkumham Bangka Belitung berhasil diterapkan:\nLatitude: -2.1516954\nLongitude: 106.1568149\nRadius: 500 meter.\n\nSilakan klik 'Simpan Pengaturan' untuk menyimpan.");
        }

        function getCurrentLocation() {
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function(pos) {
                    document.getElementById('officeLat').value = pos.coords.latitude;
                    document.getElementById('officeLng').value = pos.coords.longitude;
                    alert("Koordinat GPS berhasil diperbarui sesuai lokasi perangkat Anda saat ini: " + pos.coords.latitude + ", " + pos.coords.longitude);
                }, function(err) {
                    alert("Gagal membaca GPS: " + err.message);
                }, { enableHighAccuracy: true });
            } else {
                alert("Browser tidak mendukung geolokasi.");
            }
        }
    </script>
</x-layouts.layout>
