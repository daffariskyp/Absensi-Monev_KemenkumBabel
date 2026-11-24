@props(['active'])

<div class="relative flex items-center group">
    <a class="bg-secondary-white p-2 flex items-center justify-center rounded-full cursor-pointer hover:bg-[#6FAEC9] 
              {{ $active === 'attendance' ? 'bg-sky-blue' : 'bg-white' }}" 
        href="{{ route('attendance.index') }}">

        <svg class="size-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path stroke="{{ $active === 'attendance' ? '#FFFFFF' : '#616161' }}" class="group-hover:stroke-[#FFFFFF]" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
            <path stroke="{{ $active === 'attendance' ? '#FFFFFF' : '#616161' }}" class="group-hover:stroke-[#FFFFFF]" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>

        <!-- Tooltip -->
        <span class="hidden group-hover:flex absolute left-full ml-2 px-3 py-1 bg-sky-500 text-white text-sm rounded-md shadow-lg whitespace-nowrap z-50">
            Absensi & Monev Non-ASN
        </span>
    </a>
</div>
