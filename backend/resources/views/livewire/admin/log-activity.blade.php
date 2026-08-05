<div class="trezo-card bg-white dark:bg-[#0c1427] rounded-md">
    <div class="p-[25px] flex flex-col md:flex-row md:items-center md:justify-between gap-[15px] border-b border-gray-100 dark:border-[#172036]">
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Cari berdasarkan aktivitas..."
            class="w-full md:w-[300px] rounded-md border border-gray-200 dark:border-[#172036] bg-transparent px-3 py-2 text-sm text-black dark:text-white"
        />

        <select
            wire:model.live="statusFilter"
            class="w-full md:w-[180px] rounded-md border border-gray-200 dark:border-[#172036] bg-transparent px-3 py-2 text-sm text-black dark:text-white"
        >
            <option value="">Semua Status</option>
            <option value="success">Berhasil</option>
            <option value="failed">Gagal</option>
        </select>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-gray-100 dark:border-[#172036] text-gray-500 dark:text-gray-400">
                    <th class="p-[15px]">Waktu</th>
                    <th class="p-[15px]">User</th>
                    <th class="p-[15px]">Aktivitas</th>
                    <th class="p-[15px]">Method</th>
                    <th class="p-[15px]">Keterangan</th>
                    <th class="p-[15px]">IP Address</th>
                    <th class="p-[15px]">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr class="border-b border-gray-100 dark:border-[#172036] text-black dark:text-white">
                        <td class="p-[15px] whitespace-nowrap">{{ $log->created_at->format('d M Y, H:i') }}</td>
                        <td class="p-[15px]">{{ $log->user->name ?? 'Unknown/Anonim' }}</td>
                        <td class="p-[15px]">{{ $log->subject }}</td>
                        <td class="p-[15px]">{{ $log->method }}</td>
                        <td class="p-[15px]">{{ $log->description ?: '-' }}</td>
                        <td class="p-[15px]">{{ $log->ip_address }}</td>
                        <td class="p-[15px]">
                            @if ($log->status === 'success')
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Berhasil</span>
                            @else
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Gagal</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-[25px] text-center text-gray-500 dark:text-gray-400">
                            Belum ada aktivitas tercatat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-[25px]">
        {{ $logs->links() }}
    </div>
</div>
