<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-[25px]">
    @foreach ([
        ['label' => 'Total Berita', 'value' => $totalBerita],
        ['label' => 'Total Layanan', 'value' => $totalLayanan],
        ['label' => 'Total Dokumen', 'value' => $totalDokumen],
        ['label' => 'Total Artikel', 'value' => $totalArtikel],
    ] as $stat)
        <div class="trezo-card bg-white dark:bg-[#0c1427] p-[25px] rounded-md">
            <p class="text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</p>
            <h2 class="mt-2 text-2xl font-bold text-black dark:text-white">{{ number_format($stat['value']) }}</h2>
        </div>
    @endforeach
</div>
