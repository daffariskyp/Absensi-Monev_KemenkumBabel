<x-layouts.layout :title="$title" :active="$active">
<main class="flex space-x-4">
        <form id="editProjectForm" action="{{ route('projects.update', $project->id) }}" method="POST" enctype="multipart/form-data" class="flex-1 flex">
            @csrf
            @method('PUT')
            <div class="bg-white rounded shadow w-2/5 pt-4 px-5 pb-4 mr-4">
                    <div class="flex flex-row items-center justify-between mb-2">
                        <h1 class="text-2xl font-semibold ">Edit Project</h1>
                </div>
             
                <div class="space-y-4 mt-6">
                    <div class="flex flex-col space-y-2">
                    <label class="block text-lg text-gray-400" for="name">Project Name</label>
                    <input type="text" name="name" id="name" class="w-full bg-primary-white border border-primary-white px-3 py-2 text-sm rounded focus:outline-none"    placeholder="Project name.." required value="{{old('name', $project->name)}}">
                        @error('name')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex flex-col space-y-2">
                    <label class="block text-lg text-gray-400" >Date</label>
                            <div class="flex items-center justify-between w-full gap-4">
                                <div class="flex flex-col w-1/2 space-y-2">
                                    <label for="start_date" class="block text-xs" style="color: #a0a0a0;">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="w-full bg-primary-white border border-primary-white px-3 py-2 text-sm rounded focus:outline-none" required value="{{old('start_date', $project->start_date)}}">
                                @error('start_date')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="flex flex-col w-1/2 space-y-2">
                            <label for="end_date" class="block text-xs" style="color: #a0a0a0;">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="w-full bg-primary-white border border-primary-white px-3 py-2 text-sm rounded focus:outline-none" required value="{{old('end_date', $project->end_date)}}">
                                @error('end_date')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col space-y-2">
                    <label class="block text-lg text-gray-400" for="level">Project Level</label>
                    <select name="project_level_id" class="w-full bg-gray-50 border border-gray-300 px-3 py-2 text-sm text-gray-500 rounded focus:outline-none focus:ring focus:ring-blue-300"  id="level" required>
                            <option value="" class="text-gray-500">Select level project</option>
                            @foreach ($levels as $level)
                                <option value="{{ $level->id }}" {{ old('project_level_id', $project->project_level_id) == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
                            @endforeach
                        </select>
                        @error('project_level_id')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex flex-col space-y-2">
                        <label class="block text-lg text-gray-400"for="status">Status</label>
                        <select name="project_status_id" class="w-full bg-gray-50 border border-gray-300 px-3 py-2 text-sm text-gray-500 rounded focus:outline-none focus:ring focus:ring-blue-300"id="status" required>
                            <option value="">Select status project</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->id }}" {{old('project_status_id', $project->project_status_id) == $status->id ? 'selected' : ''}}>{{ $status->name }}</option>
                            @endforeach
                        </select>
                        @error('project_status_id')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex flex-col space-y-2">
                        <label class="block text-lg text-gray-400" for="description">Project Description</label>
                        <textarea name="description" id="description" rows="2" placeholder="Project description.." class="w-full bg-gray-50 border border-gray-300 px-3 py-2 text-sm text-gray-500 rounded focus:outline-none focus:ring focus:ring-blue-300">{{ old('description', $project->description) }}</textarea>
                        @error('description')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="bg-white rounded shadow w-3/5 pt-4 px-5 pb-4">
                    <h1 class="text-2xl font-semibold mb-2">SDM</h1>
                    <div class="space-y-4 mt-6">
                        <div class="flex flex-col space-y-2">
                            <label class="block text-lg text-gray-400" for="director">Project Director</label>
                            <select name="director_id"
                                class="w-full bg-gray-50 border border-gray-300 px-3 py-2 text-sm rounded focus:outline-none focus:ring focus:ring-blue-300"
                                id="director">
                                <option value="">Select project director</option>
                                @php
                                    $selectedDirectorId = old('director_id', $director->id ?? '');
                                    $directorEmps = $employees->where('role.name', 'Project Director');
                                    $otherEmps = $employees->where('role.name', '!=', 'Project Director');
                                @endphp
                                @foreach ($directorEmps as $employee)
                                    <option value="{{ $employee->id }}" {{ $selectedDirectorId == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->user?->name ?? 'N/A' }} (Project Director)
                                    </option>
                                @endforeach
                                @if($otherEmps->isNotEmpty())
                                    <optgroup label="Karyawan Lainnya">
                                        @foreach ($otherEmps as $employee)
                                            <option value="{{ $employee->id }}" {{ $selectedDirectorId == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->user?->name ?? 'N/A' }} ({{ $employee->role?->name ?? 'Belum ada role' }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            </select>
                            @error('director_id')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="grid grid-cols-3 gap-4 p-3 bg-blue-50/60 rounded-xl border border-blue-200 mb-2">
                            {{-- Satpam --}}
                            <div class="flex flex-col space-y-1">
                                <label class="block text-sm font-semibold text-gray-800" for="satpam">Petugas Satpam</label>
                                <select name="satpam_id" class="w-full bg-white border border-gray-300 px-3 py-2 text-sm rounded focus:outline-none focus:ring focus:ring-blue-300" id="satpam">
                                    <option value="">Pilih Petugas Satpam</option>
                                    @php
                                        $selectedSatpamId = old('satpam_id', $satpam->id ?? '');
                                        $satpamEmps = $employees->filter(fn($e) => $e->role?->name === 'Satpam');
                                        $otherSatpam = $employees->filter(fn($e) => $e->role?->name !== 'Satpam');
                                    @endphp
                                    @foreach ($satpamEmps as $employee)
                                        <option value="{{ $employee->id }}" {{ $selectedSatpamId == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->user?->name ?? 'N/A' }} (Satpam)
                                        </option>
                                    @endforeach
                                    @if($otherSatpam->isNotEmpty())
                                        <optgroup label="Pegawai Lainnya">
                                            @foreach ($otherSatpam as $employee)
                                                <option value="{{ $employee->id }}" {{ $selectedSatpamId == $employee->id ? 'selected' : '' }}>
                                                    {{ $employee->user?->name ?? 'N/A' }} ({{ $employee->role?->name ?? 'Belum ada role' }})
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                            </div>

                            {{-- Driver --}}
                            <div class="flex flex-col space-y-1">
                                <label class="block text-sm font-semibold text-gray-800" for="driver">Driver / Pengemudi</label>
                                <select name="driver_id" class="w-full bg-white border border-gray-300 px-3 py-2 text-sm rounded focus:outline-none focus:ring focus:ring-blue-300" id="driver">
                                    <option value="">Pilih Driver</option>
                                    @php
                                        $selectedDriverId = old('driver_id', $driver->id ?? '');
                                        $driverEmps = $employees->filter(fn($e) => $e->role?->name === 'Driver');
                                        $otherDriver = $employees->filter(fn($e) => $e->role?->name !== 'Driver');
                                    @endphp
                                    @foreach ($driverEmps as $employee)
                                        <option value="{{ $employee->id }}" {{ $selectedDriverId == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->user?->name ?? 'N/A' }} (Driver)
                                        </option>
                                    @endforeach
                                    @if($otherDriver->isNotEmpty())
                                        <optgroup label="Pegawai Lainnya">
                                            @foreach ($otherDriver as $employee)
                                                <option value="{{ $employee->id }}" {{ $selectedDriverId == $employee->id ? 'selected' : '' }}>
                                                    {{ $employee->user?->name ?? 'N/A' }} ({{ $employee->role?->name ?? 'Belum ada role' }})
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                            </div>

                            {{-- Petugas Kebersihan --}}
                            <div class="flex flex-col space-y-1">
                                <label class="block text-sm font-semibold text-gray-800" for="petugas_kebersihan">Petugas Kebersihan</label>
                                <select name="petugas_kebersihan_id" class="w-full bg-white border border-gray-300 px-3 py-2 text-sm rounded focus:outline-none focus:ring focus:ring-blue-300" id="petugas_kebersihan">
                                    <option value="">Pilih Petugas Kebersihan</option>
                                    @php
                                        $selectedKebersihanId = old('petugas_kebersihan_id', $petugasKebersihan->id ?? '');
                                        $kebersihanEmps = $employees->filter(fn($e) => $e->role?->name === 'Petugas Kebersihan');
                                        $otherKebersihan = $employees->filter(fn($e) => $e->role?->name !== 'Petugas Kebersihan');
                                    @endphp
                                    @foreach ($kebersihanEmps as $employee)
                                        <option value="{{ $employee->id }}" {{ $selectedKebersihanId == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->user?->name ?? 'N/A' }} (Petugas Kebersihan)
                                        </option>
                                    @endforeach
                                    @if($otherKebersihan->isNotEmpty())
                                        <optgroup label="Pegawai Lainnya">
                                            @foreach ($otherKebersihan as $employee)
                                                <option value="{{ $employee->id }}" {{ $selectedKebersihanId == $employee->id ? 'selected' : '' }}>
                                                    {{ $employee->user?->name ?? 'N/A' }} ({{ $employee->role?->name ?? 'Belum ada role' }})
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            {{-- Engineer Web --}}
                            <div class="flex flex-col space-y-2">
                                <label class="block text-lg text-gray-400" for="engineer_web">Engineer Web</label>
                                <select name="engineer_web_id" class="w-full bg-gray-50 border border-gray-300 px-3 py-2 text-sm rounded focus:outline-none focus:ring focus:ring-blue-300" id="engineer_web">
                                    <option value="">Select engineer web</option>
                                    @php
                                        $selectedWebId = old('engineer_web_id', $engineerWeb->id ?? '');
                                        $webEmps = $employees->filter(fn($e) => $e->role?->name === 'Engineer Web');
                                        $otherWeb = $employees->filter(fn($e) => $e->role?->name !== 'Engineer Web');
                                    @endphp
                                    @foreach ($webEmps as $employee)
                                        <option value="{{ $employee->id }}" {{ $selectedWebId == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->user?->name ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                    @if($otherWeb->isNotEmpty())
                                        <optgroup label="Karyawan Lainnya">
                                            @foreach ($otherWeb as $employee)
                                                <option value="{{ $employee->id }}" {{ $selectedWebId == $employee->id ? 'selected' : '' }}>
                                                    {{ $employee->user?->name ?? 'N/A' }} ({{ $employee->role?->name ?? 'Belum ada role' }})
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                                @error('engineer_web_id')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Project Analyst --}}
                            <div class="flex flex-col space-y-2">
                                <label class="block text-lg text-gray-400" for="analyst">Project Analyst</label>
                                <select name="analyst_id" class="w-full bg-gray-50 border border-gray-300 px-3 py-2 text-sm rounded focus:outline-none focus:ring focus:ring-blue-300" id="analyst">
                                    <option value="">Select project analyst</option>
                                    @php
                                        $selectedAnalystId = old('analyst_id', $analyst->id ?? '');
                                        $analystEmps = $employees->filter(fn($e) => $e->role?->name === 'Analyst');
                                        $otherAnalyst = $employees->filter(fn($e) => $e->role?->name !== 'Analyst');
                                    @endphp
                                    @foreach ($analystEmps as $employee)
                                        <option value="{{ $employee->id }}" {{ $selectedAnalystId == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->user?->name ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                    @if($otherAnalyst->isNotEmpty())
                                        <optgroup label="Karyawan Lainnya">
                                            @foreach ($otherAnalyst as $employee)
                                                <option value="{{ $employee->id }}" {{ $selectedAnalystId == $employee->id ? 'selected' : '' }}>
                                                    {{ $employee->user?->name ?? 'N/A' }} ({{ $employee->role?->name ?? 'Belum ada role' }})
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                                @error('analyst_id')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Engineer Android --}}
                            <div class="flex flex-col space-y-2">
                                <label class="block text-lg text-gray-400" for="engineer_android">Engineer Android</label>
                                <select name="engineer_android_id" class="w-full bg-gray-50 border border-gray-300 px-3 py-2 text-sm rounded focus:outline-none focus:ring focus:ring-blue-300" id="engineer_android">
                                    <option value="">Select engineer android</option>
                                    @php
                                        $selectedAndroidId = old('engineer_android_id', $engineerAndroid->id ?? '');
                                        $androidEmps = $employees->filter(fn($e) => in_array($e->role?->name, ['Engineer Android', 'Engineer Mobile']));
                                        $otherAndroid = $employees->filter(fn($e) => !in_array($e->role?->name, ['Engineer Android', 'Engineer Mobile']));
                                    @endphp
                                    @foreach ($androidEmps as $employee)
                                        <option value="{{ $employee->id }}" {{ $selectedAndroidId == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->user?->name ?? 'N/A' }} ({{ $employee->role?->name }})
                                        </option>
                                    @endforeach
                                    @if($otherAndroid->isNotEmpty())
                                        <optgroup label="Karyawan Lainnya">
                                            @foreach ($otherAndroid as $employee)
                                                <option value="{{ $employee->id }}" {{ $selectedAndroidId == $employee->id ? 'selected' : '' }}>
                                                    {{ $employee->user?->name ?? 'N/A' }} ({{ $employee->role?->name ?? 'Belum ada role' }})
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                                @error('engineer_android_id')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Engineer IOS --}}
                            <div class="flex flex-col space-y-2">
                                <label class="block text-lg text-gray-400" for="engineer_IOS">Engineer IOS</label>
                                <select name="engineer_IOS_id" class="w-full bg-gray-50 border border-gray-300 px-3 py-2 text-sm rounded focus:outline-none focus:ring focus:ring-blue-300" id="engineer_IOS">
                                    <option value="">Select engineer mobile</option>
                                    @php
                                        $selectedIosId = old('engineer_IOS_id', $engineerIos->id ?? '');
                                        $iosEmps = $employees->filter(fn($e) => in_array($e->role?->name, ['Engineer IOS', 'Engineer Mobile']));
                                        $otherIos = $employees->filter(fn($e) => !in_array($e->role?->name, ['Engineer IOS', 'Engineer Mobile']));
                                    @endphp
                                    @foreach ($iosEmps as $employee)
                                        <option value="{{ $employee->id }}" {{ $selectedIosId == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->user?->name ?? 'N/A' }} ({{ $employee->role?->name }})
                                        </option>
                                    @endforeach
                                    @if($otherIos->isNotEmpty())
                                        <optgroup label="Karyawan Lainnya">
                                            @foreach ($otherIos as $employee)
                                                <option value="{{ $employee->id }}" {{ $selectedIosId == $employee->id ? 'selected' : '' }}>
                                                    {{ $employee->user?->name ?? 'N/A' }} ({{ $employee->role?->name ?? 'Belum ada role' }})
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                                @error('engineer_IOS_id')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Content Creator --}}
                            <div class="flex flex-col space-y-2">
                                <label class="block text-lg text-gray-400" for="content_creator">Content Creator</label>
                                <select name="content_creator_id" class="w-full bg-gray-50 border border-gray-300 px-3 py-2 text-sm rounded focus:outline-none focus:ring focus:ring-blue-300" id="content_creator">
                                    <option value="">Select Content Creator</option>
                                    @php
                                        $selectedCcId = old('content_creator_id', $contentCreator->id ?? '');
                                        $ccEmps = $employees->filter(fn($e) => $e->role?->name === 'Content Creator');
                                        $otherCc = $employees->filter(fn($e) => $e->role?->name !== 'Content Creator');
                                    @endphp
                                    @foreach ($ccEmps as $employee)
                                        <option value="{{ $employee->id }}" {{ $selectedCcId == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->user?->name ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                    @if($otherCc->isNotEmpty())
                                        <optgroup label="Karyawan Lainnya">
                                            @foreach ($otherCc as $employee)
                                                <option value="{{ $employee->id }}" {{ $selectedCcId == $employee->id ? 'selected' : '' }}>
                                                    {{ $employee->user?->name ?? 'N/A' }} ({{ $employee->role?->name ?? 'Belum ada role' }})
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                                @error('content_creator_id')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Designer --}}
                            <div class="flex flex-col space-y-2">
                                <label class="block text-lg text-gray-400" for="designer"> UI/UX</label>
                                <select name="designer_id" class="w-full bg-gray-50 border border-gray-300 px-3 py-2 text-sm rounded focus:outline-none focus:ring focus:ring-blue-300" id="designer">
                                    <option value="">Select project designer</option>
                                    @php
                                        $selectedDesignerId = old('designer_id', $designer->id ?? '');
                                        $designerEmps = $employees->filter(fn($e) => $e->role?->name === 'Designer');
                                        $otherDesigner = $employees->filter(fn($e) => $e->role?->name !== 'Designer');
                                    @endphp
                                    @foreach ($designerEmps as $employee)
                                        <option value="{{ $employee->id }}" {{ $selectedDesignerId == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->user?->name ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                    @if($otherDesigner->isNotEmpty())
                                        <optgroup label="Karyawan Lainnya">
                                            @foreach ($otherDesigner as $employee)
                                                <option value="{{ $employee->id }}" {{ $selectedDesignerId == $employee->id ? 'selected' : '' }}>
                                                    {{ $employee->user?->name ?? 'N/A' }} ({{ $employee->role?->name ?? 'Belum ada role' }})
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                                @error('designer_id')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Engineer Tester --}}
                            <div class="flex flex-col space-y-2">
                                <label class="block text-lg text-gray-400" for="engineer_tester">Engineer Tester</label>
                                <select name="engineer_tester_id" class="w-full bg-gray-50 border border-gray-300 px-3 py-2 text-sm rounded focus:outline-none focus:ring focus:ring-blue-300" id="engineer_tester">
                                    <option value="">Select engineer tester</option>
                                    @php
                                        $selectedTesterId = old('engineer_tester_id', $engineerTester->id ?? '');
                                        $testerEmps = $employees->filter(fn($e) => $e->role?->name === 'Engineer Tester');
                                        $otherTester = $employees->filter(fn($e) => $e->role?->name !== 'Engineer Tester');
                                    @endphp
                                    @foreach ($testerEmps as $employee)
                                        <option value="{{ $employee->id }}" {{ $selectedTesterId == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->user?->name ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                    @if($otherTester->isNotEmpty())
                                        <optgroup label="Karyawan Lainnya">
                                            @foreach ($otherTester as $employee)
                                                <option value="{{ $employee->id }}" {{ $selectedTesterId == $employee->id ? 'selected' : '' }}>
                                                    {{ $employee->user?->name ?? 'N/A' }} ({{ $employee->role?->name ?? 'Belum ada role' }})
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                                @error('engineer_tester_id')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Copy Writer --}}
                            <div class="flex flex-col space-y-2">
                                <label class="block text-lg text-gray-400" for="copy_writer">Copy Writer</label>
                                <select name="copy_writer_id" class="w-full bg-gray-50 border border-gray-300 px-3 py-2 text-sm rounded focus:outline-none focus:ring focus:ring-blue-300" id="copy_writer">
                                    <option value="">Select engineer Copy Writer</option>
                                    @php
                                        $selectedWriterId = old('copy_writer_id', $copyWriter->id ?? '');
                                        $writerEmps = $employees->filter(fn($e) => $e->role?->name === 'Copy Writer');
                                        $otherWriter = $employees->filter(fn($e) => $e->role?->name !== 'Copy Writer');
                                    @endphp
                                    @foreach ($writerEmps as $employee)
                                        <option value="{{ $employee->id }}" {{ $selectedWriterId == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->user?->name ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                    @if($otherWriter->isNotEmpty())
                                        <optgroup label="Karyawan Lainnya">
                                            @foreach ($otherWriter as $employee)
                                                <option value="{{ $employee->id }}" {{ $selectedWriterId == $employee->id ? 'selected' : '' }}>
                                                    {{ $employee->user?->name ?? 'N/A' }} ({{ $employee->role?->name ?? 'Belum ada role' }})
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                                @error('copy_writer_id')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        </div>
                    </div>
          
        </form>
    </main>
            <!-- Form Buttons -->
            <div class="flex justify-end space-x-4 mt-6">
            <a href="{{ url()->previous() }}" class="w-40 border border-black bg-white text-black px-6 py-1 rounded-md hover:bg-gray-100 flex items-center justify-center text-center"> Cancel</a>

                 <button type="submit" form="editProjectForm" class="w-40 bg-black text-white px-6 py-1 rounded-md !bg-black"> Update </button>
            </div>

</x-layouts.layout>

 
            
<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("editProjectForm");
    const inputs = form.querySelectorAll("input, select, textarea");

    // Pastikan data yang tampil berasal dari server, bukan localStorage
    inputs.forEach(input => {
        localStorage.removeItem(input.name);
    });

    // Simpan perubahan saat user mengetik atau memilih opsi
    inputs.forEach(input => {
        input.addEventListener("input", function () {
            localStorage.setItem(input.name, input.value);
        });
    });

    // Hapus localStorage setelah form dikirim
    form.addEventListener("submit", function () {
        inputs.forEach(input => {
            localStorage.removeItem(input.name);
        });
    });
});
</script>
