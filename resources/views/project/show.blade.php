<x-layouts.layout :title="$title" :active="$active">
    <main class="flex-1 p-6 bg-white shadow-md">
        <h1 class="text-2xl font-semibold mb-4">Detail Project</h1>
        <h1 class="mt-5 text-gray-900 text-xl mb-2">{{ $project->name }}</h1>

        <div class="card">
            <div class="card-body">
                <p><span class="mt-1 text-gray-900 font-semibold">Start Date :</span>
                    {{ \Carbon\Carbon::parse($project->start_date)->locale('id')->translatedFormat('d F Y') }}</p>
                <p><span class="mt-1 text-gray-900 font-semibold">End Date :</span>
                    {{ \Carbon\Carbon::parse($project->end_date)->locale('id')->translatedFormat('d F Y') }}</p>
                <p class="mt-1"><span class="mt-1 text-gray-900 font-semibold">Level :</span>
                    {{ $project->level->name }}</p>
                <p class="mt-1"><span class="mt-1 text-gray-900 font-semibold">Status :</span>
                    {{ $project->status->name }}</p>
                <!-- Display Non-ASN Core Roles -->
                <div class="grid grid-cols-3 gap-3 p-3 bg-blue-50/70 border border-blue-200 rounded-xl mb-4">
                    <div>
                        <p class="text-xs font-semibold text-blue-900 uppercase">Petugas Satpam</p>
                        <p class="text-sm font-bold text-gray-800">
                            {{ $project->employees->first(fn($e) => $e->role?->name === 'Satpam')?->user->name ?? 'Belum Ditugaskan' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-blue-900 uppercase">Driver / Pengemudi</p>
                        <p class="text-sm font-bold text-gray-800">
                            {{ $project->employees->first(fn($e) => $e->role?->name === 'Driver')?->user->name ?? 'Belum Ditugaskan' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-blue-900 uppercase">Petugas Kebersihan</p>
                        <p class="text-sm font-bold text-gray-800">
                            {{ $project->employees->first(fn($e) => $e->role?->name === 'Petugas Kebersihan')?->user->name ?? 'Belum Ditugaskan' }}
                        </p>
                    </div>
                </div>

                <!-- Display employee roles -->
                <h5 class="mt-4 text-gray-900 font-semibold">Assigned Employees</h5>
                <div class="grid grid-cols-2 gap-2 mb-4">
                    <div>
                        <p class="mt-1 text-gray-900 font-semibold">Project Director :</p>
                        <p>
                            {{ $project->employees->where('role.name', 'Project Director')->first()?->user->name ?? 'Not assigned' }}
                        </p>
                    </div>

                    <div>
                        <p class="mt-1 text-gray-900 font-semibold">Engineer Web :</p>
                        <p>
                            {{ $project->employees->where('role.name', 'Engineer Web')->first()?->user->name ?? 'Not assigned' }}
                        </p>
                    </div>

                    <div>
                        <p class="mt-1 text-gray-900 font-semibold">Project Analyst :</p>
                        <p>
                            {{ $project->employees->where('role.name', 'Analyst')->first()?->user->name ?? 'Not assigned' }}
                        </p>
                    </div>

                    <div>
                        <p class="mt-1 text-gray-900 font-semibold">Engineer Android :</p>
                        <p>
                            @php
                                $androidEmp = $project->employees->first(fn($e) => in_array($e->role?->name, ['Engineer Android', 'Engineer Mobile']));
                            @endphp
                            {{ $androidEmp?->user->name ?? 'Not assigned' }}
                        </p>
                    </div>

                    <div>
                        <p class="mt-1 text-gray-900 font-semibold">Engineer IOS :</p>
                        <p>
                            @php
                                $iosEmp = $project->employees->first(fn($e) => in_array($e->role?->name, ['Engineer IOS', 'Engineer Mobile']));
                            @endphp
                            {{ $iosEmp?->user->name ?? 'Not assigned' }}
                        </p>
                    </div>

                    <div>
                        <p class="mt-1 text-gray-900 font-semibold">Content Creator:</p>
                        <p>
                            {{ $project->employees->where('role.name', 'Content Creator')->first()?->user->name ?? 'Not assigned' }}
                        </p>
                    </div>

                    <div>
                        <p class="mt-1 text-gray-900 font-semibold">Designer:</p>
                        <p>
                            {{ $project->employees->where('role.name', 'Designer')->first()?->user->name ?? 'Not assigned' }}
                        </p>
                    </div>
                    
                    <div>
                        <p class="mt-1 text-gray-900 font-semibold">Engineer Tester :</p>
                        <p>
                            {{ $project->employees->where('role.name', 'Engineer Tester')->first()?->user->name ?? 'Not assigned' }}
                        </p>
                    </div>

                    <div>
                        <p class="mt-1 text-gray-900 font-semibold">Copy Writer:</p>
                        <p>
                            {{ $project->employees->where('role.name', 'Copy Writer')->first()?->user->name ?? 'Not assigned' }}
                        </p>
                    </div>
                </div>

                <!-- All Team Members List -->
                @if($project->employees->isNotEmpty())
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h6 class="text-sm font-semibold text-gray-800 mb-2">Daftar Anggota Tim Terlibat:</h6>
                        <div class="flex flex-wrap gap-2">
                            @foreach($project->employees as $emp)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $emp->user?->name ?? 'Karyawan' }} - <span class="ml-1 text-gray-600">({{ $emp->role?->name ?? 'Tanpa Role' }})</span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <p class=" mt-1 text-gray-900 font-semibold">Description :</p>
                <p>{{ $project->description ?? 'No description provided.' }}</p>

                <div class="mt-10 ">
                    <a href="{{ route('projects.index') }}"
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
