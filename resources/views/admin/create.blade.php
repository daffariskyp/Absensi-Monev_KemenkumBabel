<x-layouts.layout :title="$title" :active="$active">
    <main class="h-[500px] flex flex-col items-center justify-center">
        <div class="bg-white w-[60%] py-9 px-12 rounded-xl border border-gray-200">
            <form action="{{ route('admin.store') }}" method="POST">
                @csrf
                <h1 class="font-semibold text-xl">Create New Account</h1>

                <div class="flex mt-10 w-full">

                    <!-- Username Input -->
                    <div class="w-1/2 flex flex-col space-y-1">
                        <label for="username" class="primary-gray font-medium text-sm">Username</label>
                        <input
                            class="w-[94%] primary-gray font-medium rounded-lg py-1 px-2 text-sm border border-gray-200 outline-none"
                            placeholder="Enter username" type="text" id="username" name="name"
                            value="{{ old('name') }}" required>
                        @error('name')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div class="w-1/2 flex flex-col space-y-1">
                        <label for="password" class="primary-gray font-medium text-sm">Password</label>
                        <input
                            class="w-[94%] primary-gray font-medium rounded-lg py-1 px-2 text-sm border border-gray-200 outline-none"
                            placeholder="Enter password" type="text" id="password" name="password" required>
                        @error('password')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="flex mt-3 w-full">

                    <!-- Email Input -->
                    <div class="w-1/2 flex flex-col space-y-1">
                        <label for="email" class="primary-gray font-medium text-sm">Email Akun</label>
                        <input
                            class="w-[94%] primary-gray font-medium rounded-lg py-1 px-2 text-sm border border-gray-200 outline-none"
                            placeholder="Enter email" type="email" id="email" name="email"
                            value="{{ old('email') }}" required>
                        @error('email')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div class="w-1/2 flex flex-col space-y-1">
                        <label for="password_confirmation" class="primary-gray font-medium text-sm">Confirm
                            password</label>
                        <input
                            class="w-[94%] primary-gray font-medium rounded-lg py-1 px-2 text-sm border border-gray-200 outline-none"
                            placeholder="Confirm password" type="text" id="password_confirmation"
                            name="password_confirmation" required>
                        @error('password_confirmation')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="flex mt-3 w-full">
                    <!-- Division / Posisi Jabatan (Termasuk Kabag (Kepala Bagian)) -->
                    <div class="w-1/2 flex flex-col space-y-1">
                        <label for="role_id" class="primary-gray font-medium text-sm">Divisi / Jabatan</label>
                        <select name="role_id" id="role_id"
                            class="w-[94%] primary-gray font-medium rounded-lg py-1.5 px-3 text-sm border border-gray-300 outline-none focus:ring-2 focus:ring-blue-400"
                            required>
                            <option value="">Pilih Jabatan Pegawai</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Hak Akses Akun Sistem (Admin, Supervisor/Koordinator, Pegawai) -->
                    <div class="w-1/2 flex flex-col space-y-1">
                        <label for="system_role" class="primary-gray font-medium text-sm">Hak Akses Sistem</label>
                        <select name="system_role" id="system_role"
                            class="w-[94%] primary-gray font-medium rounded-lg py-1.5 px-3 text-sm border border-gray-300 outline-none focus:ring-2 focus:ring-blue-400"
                            required>
                            <option value="user" {{ old('system_role') == 'user' ? 'selected' : '' }}>Pegawai Non-ASN (Absensi & Pengajuan Cuti)</option>
                            <option value="supervisor" {{ old('system_role') == 'supervisor' ? 'selected' : '' }}>Kabag / Supervisor (Dashboard, Presensi/Absen, Izin/Cuti, & Rekap)</option>
                            <option value="admin" {{ old('system_role') == 'admin' ? 'selected' : '' }}>Administrator Utama (Akses Penuh & Kelola SDM)</option>
                        </select>
                        <span class="text-[11px] text-gray-500">Kabag / Supervisor berhak melakukan presensi/absen harian, memantau dashboard, menyetujui izin cuti, dan melihat rekapan absensi.</span>
                        @error('system_role')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Tombol Simpan Biru dengan Border Kuning & Efek Click Shadow -->
                <div class="mt-9 mb-4 flex justify-center items-center gap-3">
                    <a href="{{ route('admin.index') }}" 
                        class="btn-colorful px-6 py-2.5 bg-gray-200 hover:bg-gray-300 active:shadow-inner text-gray-700 font-bold text-xs rounded-xl transition border border-gray-300">
                        Batal
                    </a>
                    <button type="submit" id="btnSaveSdm"
                        style="background-color: #2563eb; color: #ffffff !important; border: 2px solid #facc15;"
                        class="btn-colorful px-8 py-2.5 hover:bg-blue-700 active:shadow-inner text-white font-black text-sm rounded-xl shadow-lg hover:shadow-xl transition flex items-center gap-2 cursor-pointer">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Simpan Akun SDM
                    </button>
                </div>
            </form>
        </div>
    </main>
</x-layouts.layout>
