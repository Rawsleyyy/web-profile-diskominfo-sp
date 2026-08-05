<div wire:poll.5s class="w-full bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
    <div class="grid grid-cols-2 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-gray-100 dark:divide-gray-700 text-center">

        <!-- Hari Ini -->
        <div class="p-3">
            <span class="block text-2xl font-bold text-gray-900 dark:text-white transition-all">
                {{ number_format($hariIni) }}
            </span>
            <span class="text-xs font-semibold tracking-wider text-blue-600 dark:text-blue-400 uppercase mt-1 block">
                HARI INI
            </span>
        </div>

        <!-- Kemarin -->
        <div class="p-3">
            <span class="block text-2xl font-bold text-gray-900 dark:text-white transition-all">
                {{ number_format($kemarin) }}
            </span>
            <span class="text-xs font-semibold tracking-wider text-blue-600 dark:text-blue-400 uppercase mt-1 block">
                KEMARIN
            </span>
        </div>

        <!-- Bulan Ini -->
        <div class="p-3">
            <span class="block text-2xl font-bold text-gray-900 dark:text-white transition-all">
                {{ number_format($bulanIni) }}
            </span>
            <span class="text-xs font-semibold tracking-wider text-blue-600 dark:text-blue-400 uppercase mt-1 block">
                BULAN INI
            </span>
        </div>

        <!-- Total Pengunjung -->
        <div class="p-3">
            <span class="block text-2xl font-bold text-gray-900 dark:text-white transition-all">
                {{ number_format($total) }}
            </span>
            <span class="text-xs font-semibold tracking-wider text-blue-600 dark:text-blue-400 uppercase mt-1 block">
                TOTAL PENGUNJUNG
            </span>
        </div>

    </div>
</div>
