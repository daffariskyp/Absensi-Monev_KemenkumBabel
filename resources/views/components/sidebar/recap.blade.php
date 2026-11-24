@props(['active'])

<div class="relative flex items-center group">
    <a class="bg-secondary-white p-2 flex items-center justify-center rounded-full cursor-pointer hover:bg-[#6FAEC9] 
              {{ in_array($active, ['attendance_recap', 'attendance_settings']) ? 'bg-sky-blue' : 'bg-white' }}" 
        href="{{ route('attendance.recap') }}">

        <svg class="size-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path stroke="{{ in_array($active, ['attendance_recap', 'attendance_settings']) ? '#FFFFFF' : '#616161' }}" class="group-hover:stroke-[#FFFFFF]" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>

        <!-- Tooltip -->
        <span class="hidden group-hover:flex absolute left-full ml-2 px-3 py-1 bg-sky-500 text-white text-sm rounded-md shadow-lg whitespace-nowrap z-50">
            Rekap Absensi Non-ASN
        </span>
    </a>
</div>
