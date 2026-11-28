<x-layouts.layout :title="$title" :active="$active">
    <main class="flex flex-col items-center justify-center">
        <div class="bg-white w-[60%] py-9 px-12 rounded-xl border border-gray-200">
            <form action="{{ route('admin.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @if($errors->any())
                    <p role="alert" class="text-red-600 text-sm mb-4">
                        Perubahan belum tersimpan. {{ $errors->first() }}
                    </p>
                @endif
                <div class="flex flex-wrap items-center gap-3">
                    <img id="image-preview"
                        src="{{ $employee->photo ? asset('storage/' . $employee->photo) : asset('blank_profile.png') }}"
                        alt="Pratinjau foto profil" class="w-24 h-24 rounded-full object-cover">
                    <div class="min-w-0">
                        <div class="ml-5">
                            <h1 class="font-semibold text-xl">{{ $employee->user->name ?? 'No username' }}</h1>
                            <p class="tex-xs">{{ $employee->role->name ?? 'No devisi' }}</p>
                        </div>
                        <div class="ml-5 mt-2">
                            <label for="photo" class="block text-sm font-semibold">Foto profil</label>
                            <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp"
                                aria-describedby="photo-help" class="block w-full text-sm">
                            <p id="photo-help" class="text-xs mt-1">JPG, PNG, atau WebP hingga 5 MB.</p>
                            @error('photo')
                                <p class="text-red-600 text-xs">{{ $message }}</p>
                            @enderror
                            <button type="button" id="reset-photo" class="text-xs mt-2 underline" hidden>Batal pilih foto</button>
                        </div>
                    </div>
                </div>

                <div class="flex mt-10 w-full">
                    <!-- Nama Pegawai -->
                    <div class="w-1/2 flex flex-col space-y-1">
                        <label for="name" class="primary-gray font-medium text-sm">Nama Lengkap User</label>
                        <input
                            class="w-[94%] primary-gray font-medium rounded-lg py-1 px-2 text-sm border border-gray-200 outline-none focus:ring-2 focus:ring-blue-400"
                            placeholder="Enter full name" type="text" name="name" id="name"
                            value="{{ old('name', $employee->user?->name) }}" required>
                        @error('name')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Work Email Input -->
                    <div class="w-1/2 flex flex-col space-y-1">
                        <label for="work_email" class="primary-gray font-medium text-sm">Email Company</label>
                        <input
                            class="w-[94%] primary-gray font-medium rounded-lg py-1 px-2 text-sm border border-gray-200 outline-none focus:ring-2 focus:ring-blue-400"
                            placeholder="Enter email" type="email" name="work_email" id="work_email"
                            value="{{ old('work_email', $employee->work_email) }}">
                        @error('work_email')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="flex mt-3 w-full">
                    <!-- Division / Posisi Jabatan (Termasuk Kabag (Kepala Bagian)) -->
                    <div class="w-1/2 flex flex-col space-y-1">
                        <label for="role_id" class="primary-gray font-medium text-sm">Divisi / Jabatan</label>
                        <select name="role_id" id="role_id"
                            class="w-[94%] primary-gray font-medium rounded-lg py-1.5 px-3 text-sm border border-gray-200 outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="">Pilih Jabatan Pegawai</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id', $employee->role_id) == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Hak Akses Sistem -->
                    <div class="w-1/2 flex flex-col space-y-1">
                        <label for="system_role" class="primary-gray font-medium text-sm">Hak Akses Sistem</label>
                        <select name="system_role" id="system_role"
                            class="w-[94%] primary-gray font-medium rounded-lg py-1.5 px-3 text-sm border border-gray-200 outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="user" {{ old('system_role', $employee->user?->role) == 'user' ? 'selected' : '' }}>Pegawai Non-ASN (Presensi & Izin)</option>
                            <option value="supervisor" {{ old('system_role', $employee->user?->role) == 'supervisor' ? 'selected' : '' }}>Kabag / Supervisor (Dashboard, Presensi/Absen, Izin/Cuti, & Rekap)</option>
                            <option value="admin" {{ old('system_role', $employee->user?->role) == 'admin' ? 'selected' : '' }}>Administrator Utama (Akses Penuh)</option>
                        </select>
                    </div>
                </div>
                <div class="flex mt-3 w-full">

                    <!-- NIK Input -->
                    <div class="w-1/2 flex flex-col space-y-1">
                        <label for="nik" class="primary-gray font-medium text-sm">NIK</label>
                        <input
                            class="w-[94%] primary-gray font-medium rounded-lg py-1 px-2 text-sm border border-gray-200 outline-none"
                            placeholder="Enter NIK" type="text" name="nik" id="nik"
                            value="{{ old('nik', $employee->nik) }}">
                        @error('nik')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Telegram input -->
                    <div class="w-1/2 flex flex-col space-y-1">
                        <label for="telegram_link" class="primary-gray font-medium text-sm">Link
                            Telegram</label>
                        <input
                            class="w-[94%] primary-gray font-medium rounded-lg py-1 px-2 text-sm border border-gray-200 outline-none"
                            placeholder="Enter link telegram" type="text" name="telegram_link" id="telegram_link"
                            value="{{ old('telegram_link', $employee->telegram_link) }}">
                        @error('telegram_link')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="flex mt-3 w-full">

                    <!-- Status SDM Input -->
                    <div class="w-1/2 flex flex-col space-y-1">
                        <label for="status" class="primary-gray font-medium text-sm">Status SDM</label>
                        <select name="status" id="status"
                            class="w-[94%] primary-gray font-medium rounded-lg py-1 px-2 text-sm border border-gray-200 outline-none">
                            <option value="">Select status</option>
                            @foreach (['Kontrak', 'Freelance', 'Tetap', 'Tenaga Ahli'] as $status)
                                <option value="{{ $status }}"
                                    {{ old('status', $employee->status) === $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="w-1/2 flex flex-col space-y-1">
                        <label for="address" class="primary-gray font-medium text-sm">Alamat</label>
                        <input
                            class="w-[94%] primary-gray font-medium rounded-lg py-1 px-2 text-sm border border-gray-200 outline-none"
                            placeholder="Enter address" type="text" name="address" id="address"
                            value="{{ old('address', $employee->address) }}">
                        @error('address')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="flex mt-3 w-full">

                    <!-- Phone Number Input -->
                    <div class="w-1/2 flex flex-col space-y-1">
                        <label for="phone_number" class="primary-gray font-medium text-sm">No. HP</label>
                        <input
                            class="w-[94%] primary-gray font-medium rounded-lg py-1 px-2 text-sm border border-gray-200 outline-none"
                            placeholder="Enter no. hp" type="text" name="phone_number" id="phone_number"
                            value="{{ old('phone_number', $employee->phone_number) }}">
                        @error('phone_number')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Birth Date -->
                    <div class="w-1/2 flex flex-col space-y-1">
                        <label for="birth_date" class="primary-gray font-medium text-sm">Tanggal
                            Lahir</label>
                        <input
                            class="w-[94%] primary-gray font-medium rounded-lg py-1 px-2 text-sm border border-gray-200 outline-none"
                            type="date" name="birth_date" id="birth_date"
                            value="{{ old('birth_date', $employee->birth_date) }}">
                        @error('birth_date')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="flex mt-3 w-full">

                    <!-- Join Date Input -->
                    <div class="w-1/2 flex flex-col space-y-1">
                        <label for="join_date" class="primary-gray font-medium text-sm">Tanggal Masuk</label>
                        <input
                            class="w-[94%] primary-gray font-medium rounded-lg py-1 px-2 text-sm border border-gray-200 outline-none"
                            type="date" name="join_date" id="join_date"
                            value="{{ old('join_date', $employee->join_date) }}">
                        @error('join_date')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Education terakhir -->
                    <div class="w-1/2 flex flex-col space-y-1">
                        <label for="education" class="primary-gray font-medium text-sm">Pendidikan
                            Terakhir</label>
                        <input
                            class="w-[94%] primary-gray font-medium rounded-lg py-1 px-2 text-sm border border-gray-200 outline-none"
                            placeholder="Enter education" type="text" name="education" id="education"
                            value="{{ old('education', $employee->education) }}">
                        @error('education')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Bagian Khusus Administrator: Ambil Data & Ubah Password User -->
                <div class="mt-6 pt-5 border-t border-gray-200">
                    <div class="bg-blue-50 border-2 border-blue-200 rounded-2xl p-4 mb-4">
                        <div class="flex items-center justify-between flex-wrap gap-2 mb-1">
                            <div class="flex items-center gap-2">
                                <span class="p-1 rounded-lg bg-blue-600 text-white">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                </span>
                                <h3 class="text-xs font-black text-blue-950 uppercase tracking-wider">Ubah & Kelola Password Akun (Khusus Hak Akses Admin)</h3>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-200 text-blue-900">Admin Only</span>
                        </div>
                        <p class="text-xs text-blue-800 leading-relaxed">
                            Password user tersimpan dalam enkripsi hash (Bcrypt). Anda dapat langsung mengganti password baru untuk user ini, atau menggunakan tombol instan di bawah:
                        </p>
                        <div class="flex flex-wrap items-center gap-2 mt-3">
                            <button type="button" onclick="setDefaultPassword('password123')"
                                class="btn-colorful px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition flex items-center gap-1 cursor-pointer shadow-sm">
                                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                Set Password Standar (password123)
                            </button>
                            <button type="button" onclick="generateRandomPassword()"
                                class="btn-colorful px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold transition flex items-center gap-1 cursor-pointer shadow-sm">
                                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                Generate Password Acak
                            </button>
                            <button type="button" onclick="copyCurrentPassword()"
                                class="btn-colorful px-3 py-1.5 bg-gray-700 hover:bg-gray-800 text-white rounded-lg text-xs font-bold transition flex items-center gap-1 cursor-pointer shadow-sm">
                                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                                Salin Password
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="w-full sm:w-1/2 flex flex-col space-y-1">
                            <label for="admin_password" class="primary-gray font-bold text-xs">Password Baru User</label>
                            <div class="relative">
                                <input
                                    class="w-full primary-gray font-medium rounded-lg py-2 pl-3 pr-10 text-sm border border-gray-300 outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                                    placeholder="Masukkan password baru (minimal 8 karakter)" type="password" name="password" id="admin_password" minlength="8">
                                <button type="button" onclick="togglePasswordVisibility('admin_password', 'toggleEyeIcon1')"
                                    class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-700 transition" title="Lihat/Sembunyikan Password">
                                    <svg id="toggleEyeIcon1" class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </button>
                            </div>
                            @error('password')
                                <span class="text-red-600 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="w-full sm:w-1/2 flex flex-col space-y-1">
                            <label for="admin_password_confirmation" class="primary-gray font-bold text-xs">Ulangi Konfirmasi Password</label>
                            <div class="relative">
                                <input
                                    class="w-full primary-gray font-medium rounded-lg py-2 pl-3 pr-10 text-sm border border-gray-300 outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                                    placeholder="Ulangi password baru yang sama" type="password" name="password_confirmation" id="admin_password_confirmation" minlength="8">
                                <button type="button" onclick="togglePasswordVisibility('admin_password_confirmation', 'toggleEyeIcon2')"
                                    class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-700 transition" title="Lihat/Sembunyikan Password">
                                    <svg id="toggleEyeIcon2" class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons: Simpan, Hapus Langsung & Batal -->
                <div class="mt-8 pt-5 border-t border-gray-200 flex flex-wrap justify-between items-center gap-3">
                    <button type="button" onclick="confirmDeleteFromEdit({{ $employee->id }}, '{{ addslashes($employee->user?->name) }}')"
                        style="background-color: #e11d48; color: #ffffff !important;"
                        class="btn-colorful px-5 py-2.5 hover:opacity-90 active:shadow-inner text-white font-extrabold text-xs rounded-xl shadow-md transition flex items-center gap-1.5 cursor-pointer">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        Hapus Akun Pengguna Ini
                    </button>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.index') }}"
                            class="btn-colorful px-5 py-2.5 bg-gray-100 hover:bg-gray-200 active:shadow-inner text-gray-700 font-bold text-xs rounded-xl transition shadow-sm border border-gray-300">
                            Batal
                        </a>
                        <button type="submit"
                            style="background-color: #2563eb; color: #ffffff !important; border: 2px solid #facc15;"
                            class="btn-colorful px-8 py-2.5 hover:bg-blue-700 active:shadow-inner text-white font-black text-xs rounded-xl shadow-lg transition flex items-center gap-2 cursor-pointer">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>
</x-layouts.layout>

<script>
    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.add('text-blue-600');
        } else {
            input.type = 'password';
            icon.classList.remove('text-blue-600');
        }
    }

    function setDefaultPassword(pass) {
        document.getElementById('admin_password').value = pass;
        document.getElementById('admin_password_confirmation').value = pass;
        document.getElementById('admin_password').type = 'text';
        document.getElementById('admin_password_confirmation').type = 'text';
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: 'Password Standar Ditetapkan',
                text: `Password baru diset: "${pass}". Klik Simpan Perubahan untuk mengaktifkan.`,
                timer: 2500,
                showConfirmButton: false
            });
        }
    }

    function generateRandomPassword() {
        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#$%';
        let randomPass = '';
        for (let i = 0; i < 10; i++) {
            randomPass += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('admin_password').value = randomPass;
        document.getElementById('admin_password_confirmation').value = randomPass;
        document.getElementById('admin_password').type = 'text';
        document.getElementById('admin_password_confirmation').type = 'text';

        navigator.clipboard.writeText(randomPass).then(() => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Password Acak Dibuat & Disalin!',
                    text: `Password baru: "${randomPass}" (Telah otomatis disalin ke clipboard).`,
                    timer: 3500
                });
            }
        });
    }

    function copyCurrentPassword() {
        const val = document.getElementById('admin_password').value;
        if (!val) {
            alert('Silakan ketik atau generate password terlebih dahulu.');
            return;
        }
        navigator.clipboard.writeText(val).then(() => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Disalin!',
                    text: `Password "${val}" berhasil disalin ke clipboard.`,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                alert('Password disalin ke clipboard.');
            }
        });
    }

    function confirmDeleteFromEdit(employeeId, name) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Hapus Pegawai Ini?',
                html: `Apakah Anda yakin ingin menghapus akun pegawai <b>${name}</b>?<br><span style="color:#dc2626; font-size:12px;">Data presensi dan akun login akan dihapus permanen.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus Sekarang!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/${employeeId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Terhapus!', 'Data pegawai berhasil dihapus.', 'success').then(() => {
                                window.location.href = "{{ route('admin.index') }}";
                            });
                        } else {
                            Swal.fire('Error', data.message || 'Gagal menghapus.', 'error');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Error', 'Terjadi kesalahan sistem saat menghapus.', 'error');
                    });
                }
            });
        } else {
            if (confirm(`Hapus akun pegawai ${name}?`)) {
                // fallback
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/${employeeId}`;
                form.innerHTML = `
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                    <input type="hidden" name="_method" value="DELETE">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    }

    const fileInput = document.getElementById('photo');
    const imagePreview = document.getElementById('image-preview');
    const resetPhoto = document.getElementById('reset-photo');
    const savedPhoto = imagePreview.src;
    let previewUrl;

    fileInput.addEventListener('change', () => {
        if (previewUrl) URL.revokeObjectURL(previewUrl);
        const file = fileInput.files[0];
        previewUrl = file ? URL.createObjectURL(file) : null;
        imagePreview.src = previewUrl ?? savedPhoto;
        resetPhoto.hidden = !file;
    });

    resetPhoto.addEventListener('click', () => {
        fileInput.value = '';
        fileInput.dispatchEvent(new Event('change'));
    });
</script>
