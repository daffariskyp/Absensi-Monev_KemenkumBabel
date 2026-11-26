<x-layouts.layout :title="$title" :active="$active">
    @php
        $employee = $user->employee;
        $fields = [
            'name' => ['Nama lengkap', 'text', 255, $user->name],
            'work_email' => ['Email kantor', 'email', 255, $employee?->work_email],
            'phone_number' => ['Nomor HP', 'tel', 15, $employee?->phone_number],
            'birth_date' => ['Tanggal lahir', 'date', null, $employee?->birth_date],
            'education' => ['Pendidikan terakhir', 'text', 255, $employee?->education],
            'telegram_link' => ['Telegram', 'text', 255, $employee?->telegram_link],
        ];
    @endphp

    <section class="max-w-5xl mx-auto py-4">
        <form action="{{ route('users.profile.update') }}" method="POST" enctype="multipart/form-data"
            class="bg-white rounded-2xl border border-blue-200 p-4 sm:p-6 shadow-sm">
            @csrf
            @method('PUT')

            <h1 class="text-2xl font-bold text-gray-900">Profil Saya</h1>
            @if(session('profile_saved'))
                <p role="status" class="mt-3 rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-800">
                    {{ session('profile_saved') }}
                </p>
            @endif
            @if($errors->any())
                <p role="alert" class="mt-3 text-sm text-red-700">Profil belum tersimpan. {{ $errors->first() }}</p>
            @endif

            <div class="mt-5 flex flex-wrap items-center gap-4">
                <img id="profile-photo-preview"
                    src="{{ $employee?->photo ? asset('storage/' . $employee->photo) : asset('blank_profile.png') }}"
                    alt="Pratinjau foto profil" width="96" height="96"
                    class="w-24 h-24 rounded-full border border-blue-200 object-cover">
                <div class="min-w-0">
                    <label for="profile-photo" class="block font-semibold text-sm">Foto profil</label>
                    <input id="profile-photo" type="file" name="photo" accept="image/jpeg,image/png,image/webp"
                        aria-describedby="profile-photo-help" class="block w-full mt-1 text-sm">
                    <p id="profile-photo-help" class="text-xs text-gray-600 mt-1">JPG, PNG, atau WebP hingga 5 MB.</p>
                    @error('photo')
                        <p class="text-xs text-red-700 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <dl class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-4 rounded-xl border border-blue-200 p-4">
                <div>
                    <dt class="text-xs text-gray-600">Akses akun</dt>
                    <dd class="text-sm font-semibold">{{ ['admin' => 'Administrator', 'supervisor' => 'Supervisor', 'user' => 'Pengguna'][$user->role] ?? $user->role }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-600">Divisi / jabatan</dt>
                    <dd class="text-sm font-semibold">{{ $employee?->role?->name ?? 'Belum ditetapkan' }}</dd>
                </div>
            </dl>

            <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($fields as $name => [$label, $type, $maxLength, $value])
                    <div>
                        <label class="block text-sm font-semibold mb-1" for="profile-{{ $name }}">{{ $label }}</label>
                        <input class="w-full min-w-0 rounded-xl border border-blue-200 px-3 py-2 text-sm"
                            id="profile-{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $value) }}"
                            @if($maxLength) maxlength="{{ $maxLength }}" @endif
                            @required($name === 'name')>
                        @error($name)
                            <p class="text-xs text-red-700 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold mb-1" for="profile-address">Alamat</label>
                    <textarea class="w-full rounded-xl border border-blue-200 px-3 py-2 text-sm"
                        id="profile-address" name="address" maxlength="255" rows="2">{{ old('address', $employee?->address) }}</textarea>
                    @error('address')
                        <p class="text-xs text-red-700 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold mb-1" for="profile-bio">Tentang saya</label>
                    <textarea class="w-full rounded-xl border border-blue-200 px-3 py-2 text-sm"
                        id="profile-bio" name="bio" maxlength="1000" rows="4">{{ old('bio', $employee?->bio) }}</textarea>
                    @error('bio')
                        <p class="text-xs text-red-700 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <button type="submit" class="app-action mt-5">Simpan profil</button>
        </form>

    </section>

    <script>
        (() => {
            const input = document.getElementById('profile-photo');
            const preview = document.getElementById('profile-photo-preview');
            const savedPhoto = preview.src;
            let previewUrl;

            input.addEventListener('change', () => {
                if (previewUrl) URL.revokeObjectURL(previewUrl);
                const photo = input.files[0];
                previewUrl = photo ? URL.createObjectURL(photo) : null;
                preview.src = previewUrl ?? savedPhoto;
            });
        })();
    </script>
</x-layouts.layout>
