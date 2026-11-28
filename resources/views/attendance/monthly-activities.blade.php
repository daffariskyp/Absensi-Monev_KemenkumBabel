<table class="w-full border-collapse text-left text-sm">
    <thead class="bg-gray-100 text-xs font-semibold text-gray-700">
        <tr>
            <th scope="col" class="p-3">Tanggal</th>
            <th scope="col" class="p-3">Pegawai</th>
            <th scope="col" class="p-3">Kegiatan</th>
            <th scope="col" class="p-3">Kendala</th>
            <th scope="col" class="p-3">Bukti foto</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-200">
        @forelse($activities as $activity)
            <tr class="align-top">
                <td class="p-3 whitespace-nowrap">
                    {{ $activity->date->format('d/m/Y') }}
                    <span class="block text-xs text-gray-600">{{ substr($activity->time ?? '', 0, 5) }}</span>
                </td>
                <td class="p-3">{{ $activity->user?->name ?? '—' }}</td>
                <td class="p-3 break-words whitespace-pre-line">{{ $activity->activity_description }}</td>
                <td class="p-3 break-words whitespace-pre-line">{{ $activity->obstacle ?: '—' }}</td>
                <td class="p-3">
                    @if($printView ?? false)
                        {{ count($activity->photos ?? []) }}
                    @else
                        @forelse($activity->photos ?? [] as $photo)
                            <a class="block text-blue-800 underline whitespace-nowrap" target="_blank" rel="noopener"
                                href="{{ asset('storage/' . $photo) }}">Foto {{ $loop->iteration }}</a>
                        @empty
                            —
                        @endforelse
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="p-4 text-center text-gray-600">Belum ada aktivitas Monev pada periode ini.</td>
            </tr>
        @endforelse
    </tbody>
</table>
