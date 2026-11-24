<x-layouts.layout :title="$title" :active="$active">
    <div class="mx-auto max-w-3xl my-5">
        <div class="bg-white rounded-lg border-2 border-gray-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4 border-b pb-3">
                <h1 class="text-2xl font-bold text-gray-800">Edit Pengajuan Izin</h1>
                <span class="text-xs bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full font-medium">Status: {{ ucfirst($administration->status) }}</span>
            </div>

            <form action="{{ route('administration.update', $administration->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <!-- Leave Category -->
                    <div class="flex flex-col">
                        <label class="text-sm font-semibold text-gray-700 mb-1">Jenis Izin / Kategori Cuti <span class="text-red-500">*</span></label>
                        <select name="leave_category" class="border px-4 py-2 text-sm bg-white text-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                            @foreach ($categories as $key => $label)
                                <option value="{{ $key }}" {{ old('leave_category', $administration->leave_category) == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('leave_category')
                            <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Date Inputs -->
                    <div class="flex flex-col">
                        <label class="text-sm font-semibold text-gray-700 mb-1">Rentang Tanggal <span class="text-red-500">*</span></label>
                        <div class="flex gap-4">
                            <div class="w-1/2">
                                <label for="start_date" class="text-xs text-gray-500">Mulai Tanggal:</label>
                                <input type="date" name="start_date" id="start_date" 
                                    class="w-full border px-3 py-2 text-sm rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" 
                                    required value="{{ old('start_date', $administration->start_date) }}">
                                @error('start_date')
                                    <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="w-1/2">
                                <label for="end_date" class="text-xs text-gray-500">Sampai Tanggal:</label>
                                <input type="date" name="end_date" id="end_date" 
                                    class="w-full border px-3 py-2 text-sm rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" 
                                    required value="{{ old('end_date', $administration->end_date) }}">
                                @error('end_date')
                                    <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="flex flex-col">
                        <label class="text-sm font-semibold text-gray-700 mb-1" for="description">Uraian / Alasan Izin <span class="text-red-500">*</span></label>
                        <textarea name="description" id="description" rows="3" 
                            class="border px-4 py-2 text-sm rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                            placeholder="Jelaskan keperluan izin / cuti Anda secara rinci..." required>{{ old('description', $administration->description) }}</textarea>
                        @error('description')
                            <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Attachment Upload -->
                    <div class="flex flex-col">
                        <label class="text-sm font-semibold text-gray-700 mb-1" for="attachment">
                            Lampiran Bukti Pendukung (Opsional jika ingin mengganti berkas sebelumnya)
                        </label>
                        @if($administration->attachment)
                            <div class="mb-2 text-xs text-blue-600">
                                Berkas saat ini: <a href="{{ asset('storage/' . $administration->attachment) }}" target="_blank" class="underline">Lihat Lampiran</a>
                            </div>
                        @endif
                        <input type="file" name="attachment" id="attachment" accept=".pdf,.jpg,.jpeg,.png"
                            class="border px-3 py-2 text-sm rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50">
                        @error('attachment')
                            <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Radio Option -->
                    <div class="flex gap-4 pt-2">
                        <div class="w-1/2">
                            <label class="text-sm font-semibold text-gray-700 block mb-1">Membawa Laptop?</label>
                            <div class="flex border p-2 rounded-md bg-white space-x-4">
                                <label class="flex items-center text-sm text-gray-700 cursor-pointer">
                                    <input type="radio" name="bring_laptop" class="mr-2 accent-blue-600" value="0" {{ old('bring_laptop', $administration->bring_laptop ? '1' : '0') == '0' ? 'checked' : '' }}> Tidak
                                </label>
                                <label class="flex items-center text-sm text-gray-700 cursor-pointer">
                                    <input type="radio" name="bring_laptop" class="mr-2 accent-blue-600" value="1" {{ old('bring_laptop', $administration->bring_laptop ? '1' : '0') == '1' ? 'checked' : '' }}> Ya
                                </label>
                            </div>
                        </div>
                        <div class="w-1/2">
                            <label class="text-sm font-semibold text-gray-700 block mb-1">Dapat Dihubungi?</label>
                            <div class="flex border p-2 rounded-md bg-white space-x-4">
                                <label class="flex items-center text-sm text-gray-700 cursor-pointer">
                                    <input type="radio" name="contacted" class="mr-2 accent-blue-600" value="0" {{ old('contacted', $administration->contacted ? '1' : '0') == '0' ? 'checked' : '' }}> Tidak
                                </label>
                                <label class="flex items-center text-sm text-gray-700 cursor-pointer">
                                    <input type="radio" name="contacted" class="mr-2 accent-blue-600" value="1" {{ old('contacted', $administration->contacted ? '1' : '0') == '1' ? 'checked' : '' }}> Ya
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-4 pt-6 border-t">
                        <a href="{{ route('administration.index') }}" 
                            class="px-6 py-2 border border-gray-400 bg-white text-gray-700 rounded-md hover:bg-gray-100 font-medium text-sm">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium text-sm shadow">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.layout>
