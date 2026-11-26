<x-layouts.layout :title="$title" :active="$active">
    <main class="max-w-screen w-full mx-auto rounded-xl bg-white border">
        <!-- Search and Add Button -->
        <div class="admin-toolbar flex items-center justify-between mb-2 pt-3 px-6">
            <form action="{{ route('admin.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                <div class="relative flex items-center">
                    <input type="search" placeholder="Cari nama atau email" aria-label="Cari nama atau email"
                        class="table-filter pl-9 pr-4 py-1 text-sm"
                        value="{{ request('search') }}" name="search" />
                    <svg class="size-5 ml-3 z-0 absolute" viewBox="0 0 41 41" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M19.5196 4C17.2041 4.0002 14.9222 4.55412 12.8644 5.61556C10.8065 6.677 9.0323 8.21517 7.68979 10.1017C6.34729 11.9883 5.47541 14.1686 5.14689 16.4607C4.81838 18.7527 5.04275 21.0901 5.8013 23.2778C6.55985 25.4655 7.83058 27.4401 9.50746 29.0369C11.1843 30.6336 13.2188 31.8062 15.441 32.4567C17.6632 33.1073 20.0088 33.217 22.282 32.7767C24.5552 32.3364 26.6902 31.3589 28.5088 29.9257L34.7477 36.1645C35.0699 36.4757 35.5014 36.6479 35.9493 36.644C36.3972 36.6401 36.8257 36.4604 37.1425 36.1437C37.4592 35.827 37.6389 35.3985 37.6427 34.9506C37.6466 34.5026 37.4744 34.0711 37.1633 33.7489L30.9244 27.5101C32.6123 25.3689 33.6632 22.7958 33.9569 20.0852C34.2506 17.3746 33.7753 14.6361 32.5853 12.1831C31.3953 9.73003 29.5388 7.66157 27.2281 6.2144C24.9175 4.76722 22.246 3.99982 19.5196 4ZM8.41543 18.5208C8.41543 15.5758 9.58533 12.7514 11.6678 10.669C13.7502 8.58657 16.5746 7.41667 19.5196 7.41667C22.4646 7.41667 25.289 8.58657 27.3714 10.669C29.4539 12.7514 30.6238 15.5758 30.6238 18.5208C30.6238 21.4658 29.4539 24.2902 27.3714 26.3727C25.289 28.4551 22.4646 29.625 19.5196 29.625C16.5746 29.625 13.7502 28.4551 11.6678 26.3727C9.58533 24.2902 8.41543 21.4658 8.41543 18.5208Z"
                            fill="#616161" fill-opacity="0.6" />
                    </svg>
                </div>
                <div>
                    <select name="role"
                        class="primary-gray font-medium rounded-lg py-1 px-2 text-sm border outline-none"
                        aria-label="Divisi">
                        <option value="">Semua divisi</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="app-action">Cari</button>
            </form>
            <a href="{{ route('admin.create') }}"
                class="btn-colorful bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-700 hover:to-indigo-800 cursor-pointer text-white text-xs px-4 py-2 rounded-xl shadow-md flex justify-center items-center gap-1.5 transition border border-transparent">
                <svg class="size-4 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                    height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 12h14m-7 7V5" />
                </svg>
                Tambah pegawai
            </a>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse ">
                <thead class="bg-[#F9F9F9]">
                    <tr>
                        <th class="w-[3%] pl-4 py-2 primary-gray text-left font-medium text-sm">No</th>
                        <th class="w-[10%] px-4 py-2 primary-gray text-left font-medium text-sm">Username</th>
                        <th class="w-[8%] px-4 py-2 primary-gray text-left font-medium text-sm">Divisi</th>
                        <th class="w-[3%] px-2 py-2 primary-gray text-left font-medium text-sm">Role</th>
                        <th class="w-[8%] px-4 py-2 primary-gray text-left font-medium text-sm">Email</th>
                        <th class="w-44 px-4 py-2 primary-gray text-center font-medium text-sm">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <!-- Row -->
                    @foreach ($employees as $index => $employee)
                        <tr class="group hover:bg-[#F5F5F5] font-medium transition">
                            <td class="pl-6 py-2.5 text-sm text-gray-500">
                                {{ $employees->firstItem() + $index }}
                            </td>
                            <td class="px-4 py-2.5 text-sm">
                                <a href="{{ route('admin.edit', $employee->id) }}" class="font-bold text-gray-900 hover:text-blue-600 transition flex items-center gap-1.5" title="Klik untuk edit data">
                                    <img src="{{ $employee->photo ? asset('storage/' . $employee->photo) : asset('blank_profile.png') }}"
                                        alt="" width="32" height="32" loading="lazy" class="w-8 h-8 rounded-full object-cover">
                                    {{ $employee->user->name ?? 'No username' }}
                                </a>
                            </td>
                            <td class="px-2 py-2.5 text-sm">
                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700">
                                    {{ $employee->role->name ?? 'No devisi' }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 text-sm">
                                @if($employee->user?->role === 'admin')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-blue-100 text-blue-800">
                                        Admin Utama
                                    </span>
                                @elseif($employee->user?->role === 'supervisor')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-purple-100 text-purple-800">
                                        Koordinator
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        Pegawai
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-sm text-gray-600">
                                {{ $employee->user->email ?? 'No email' }}
                            </td>
                            <td class="py-2.5 px-4 text-sm text-center" onclick="event.stopPropagation()">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.edit', $employee->id) }}" onclick="event.stopPropagation()"
                                        style="background-color: #2563eb; color: #ffffff !important; border: 1px solid #3b82f6;"
                                        class="btn-colorful px-3.5 py-1.5 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-1 hover:opacity-95 active:shadow-inner"
                                        title="Ubah data & password">
                                        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        Rubah
                                    </a>
                                    <button type="button" onclick="event.stopPropagation(); confirmDeleteDirect({{ $employee->id }}, '{{ addslashes($employee->user?->name ?? 'Pegawai') }}')"
                                        style="background-color: #e11d48; color: #ffffff !important; border: 1px solid #f43f5e;"
                                        class="btn-colorful px-3.5 py-1.5 text-white rounded-xl text-xs font-bold shadow-sm transition flex items-center gap-1 hover:opacity-95 active:shadow-inner cursor-pointer"
                                        title="Hapus akun pegawai ini">
                                        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    <!-- Row -->
                </tbody>
            </table>
            @if($employees->hasPages())
                <div class="flex justify-between items-center my-5 px-6">
                    {{ $employees->appends(['search' => $search, 'role' => $roleFilter])->links() }}
                </div>
            @endif
        </div>
    </main>
</x-layouts.layout>
<script>
    // SweetAlert Konfirmasi Hapus Langsung dari Tabel Admin
    function confirmDeleteDirect(employeeId, employeeName) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '<span style="color: #991b1b; font-weight: 800;">Hapus Akun SDM?</span>',
                html: `
                    <div style="text-align: left; font-size: 13px; color: #374151; line-height: 1.6;">
                        <p>Apakah Anda yakin ingin menghapus akun: <b style="color: #1e3a8a;">${employeeName}</b>?</p>
                        <div style="background: #fff1f2; border: 1px solid #fecdd3; padding: 10px 14px; border-radius: 12px; margin-top: 10px; color: #9f1239; font-size: 12px;">
                            <b>Peringatan:</b> Akun pengguna dan seluruh data absensinya akan dihapus permanen dari sistem.
                        </div>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus Sekarang!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                customClass: {
                    popup: 'rounded-2xl shadow-2xl border border-rose-200',
                    confirmButton: 'px-5 py-2.5 rounded-xl font-bold shadow-md text-white transition active:scale-95',
                    cancelButton: 'px-4 py-2.5 rounded-xl font-semibold transition active:scale-95'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    fetch(`/admin/${employeeId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Dihapus!',
                                text: `Akun ${employeeName} telah berhasil dihapus.`,
                                confirmButtonColor: '#2563eb'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', data.message || 'Gagal menghapus user.', 'error');
                        }
                    })
                    .catch(err => {
                        Swal.fire('Error!', 'Terjadi kendala saat menghapus data.', 'error');
                    });
                }
            });
        } else {
            if (confirm(`Apakah Anda yakin ingin menghapus akun pegawai ${employeeName}?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/${employeeId}`;
                form.innerHTML = `
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')}">
                    <input type="hidden" name="_method" value="DELETE">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    }
</script>
