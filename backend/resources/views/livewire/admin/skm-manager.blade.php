<div class="p-[25px]">
    <!-- Header -->
    <div class="mb-[25px]">
        <h1 class="text-2xl font-bold text-black dark:text-white">Kelola Data SKM</h1>
        <p class="mt-1 text-gray-500 dark:text-gray-400">Lihat dan kelola data responden Survei Kepuasan Masyarakat.</p>
    </div>

    <!-- CARD UTAMA -->
    <div class="w-full bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">

        <!-- Flash Message -->
        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm flex items-center justify-between">
                <span>{{ session('message') }}</span>
                <button type="button" class="text-green-700 font-bold" onclick="this.parentElement.remove()">✕</button>
            </div>
        @endif

        <!-- Top Actions -->
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3 mb-6">
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <input
                    type="text"
                    wire:model.live.debounce.400ms="search"
                    placeholder="Cari nama / no. WhatsApp..."
                    class="w-full sm:w-64 px-4 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white placeholder-gray-400"
                >

                <select wire:model.live="filterLayanan" class="w-full sm:w-56 px-4 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white">
                    <option value="">Semua Layanan</option>
                    @foreach ($daftarLayanan as $layanan)
                        <option value="{{ $layanan->id }}">{{ $layanan->nama_layanan }}</option>
                    @endforeach
                </select>
            </div>

            <span class="text-sm text-gray-400 whitespace-nowrap">
                Total: {{ $responden->total() }} responden
            </span>
        </div>

        <!-- Tabel Data Responden -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse table-fixed">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700 text-[13px] font-bold text-gray-700 dark:text-gray-300">
                        <th class="pb-4 px-4 w-[20%]">Nama</th>
                        <th class="pb-4 px-4 w-[15%]">No. WhatsApp</th>
                        <th class="pb-4 px-4 w-[25%]">Layanan Dinilai</th>
                        <th class="pb-4 px-4 w-[15%]">Tanggal Isi</th>
                        <th class="pb-4 px-4 text-center w-[10%]">L/P</th>
                        <th class="pb-4 px-4 text-center w-[15%]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                    @forelse ($responden as $r)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/50">
                            <td class="py-4 px-4 font-semibold text-gray-900 dark:text-white align-top break-words">
                                {{ $r->nama }}
                            </td>
                            <td class="py-4 px-4 text-gray-600 dark:text-gray-300 align-top break-words">
                                {{ $r->no_whatsapp }}
                            </td>
                            <td class="py-4 px-4 text-gray-600 dark:text-gray-300 align-top break-words">
                                {{ $r->jenisLayanan->nama_layanan ?? '-' }}
                            </td>
                            <td class="py-4 px-4 text-gray-600 dark:text-gray-300 align-top break-words">
                                {{ $r->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="py-4 px-4 text-center align-top">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $r->jenis_kelamin === 'P' || $r->jenis_kelamin === 'Perempuan' ? 'bg-pink-100 text-pink-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $r->jenis_kelamin === 'P' || $r->jenis_kelamin === 'Perempuan' ? 'P' : 'L' }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-center align-top whitespace-nowrap space-x-2">
                                <button type="button" wire:click="detail({{ $r->id }})" class="text-blue-600 hover:text-blue-800 font-medium">Detail</button>
                                <button type="button" wire:click="delete({{ $r->id }})" onclick="confirm('Hapus data responden ini? Tindakan tidak bisa dibatalkan.') || event.stopImmediatePropagation()" class="text-red-600 hover:text-red-800 font-medium">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-sm text-gray-400 dark:text-gray-500">
                                Belum ada data responden SKM.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $responden->links() }}
        </div>

    </div>

    <!-- MODAL DETAIL JAWABAN -->
    @if ($isDetailOpen && $selected)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" wire:key="detail-{{ $selected->id }}">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-2xl w-full p-6 border border-gray-100 dark:border-gray-700 max-h-[85vh] overflow-y-auto">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $selected->nama }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $selected->jenisLayanan->nama_layanan ?? '-' }} &middot; {{ $selected->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <button type="button" wire:click="closeDetail" class="text-gray-400 hover:text-gray-700 dark:hover:text-white text-xl leading-none">✕</button>
                </div>

                <!-- Data Diri Responden -->
                <div class="grid grid-cols-2 gap-3 text-sm mb-6 bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                    <div><span class="text-gray-400 text-xs block">No. WhatsApp</span>{{ $selected->no_whatsapp }}</div>
                    <div><span class="text-gray-400 text-xs block">Usia</span>{{ $selected->usia }} tahun</div>
                    <div><span class="text-gray-400 text-xs block">Pendidikan</span>{{ $selected->pendidikan }}</div>
                    <div><span class="text-gray-400 text-xs block">Pekerjaan</span>{{ $selected->pekerjaan }}</div>
                    <div><span class="text-gray-400 text-xs block">Kecamatan</span>{{ $selected->kecamatan }}</div>
                    <div><span class="text-gray-400 text-xs block">Kelurahan</span>{{ $selected->kelurahan }}</div>
                </div>

                <!-- Jawaban Survei -->
                <div class="space-y-3">
                    @foreach ($pertanyaan as $no => $teks)
                        <div class="flex items-start justify-between gap-4 py-2 border-b border-gray-100 dark:border-gray-700 text-sm">
                            <span class="text-gray-600 dark:text-gray-300">{{ $no }}. {{ $teks }}</span>
                            <span class="font-semibold text-gray-900 dark:text-white text-right whitespace-nowrap">{{ $selected->{"jawaban_$no"} }}</span>
                        </div>
                    @endforeach
                </div>

                @if ($selected->saran)
                    <div class="mt-4">
                        <span class="text-gray-400 text-xs block mb-1">Saran & Masukan</span>
                        <p class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-900 rounded-lg p-3">{{ $selected->saran }}</p>
                    </div>
                @endif

                <div class="flex justify-end gap-2 mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" wire:click="closeDetail" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">Tutup</button>
                    <button type="button" wire:click="delete({{ $selected->id }})" onclick="confirm('Hapus data responden ini? Tindakan tidak bisa dibatalkan.') || event.stopImmediatePropagation()" class="px-4 py-2 text-sm bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg shadow-sm">Hapus Data Ini</button>
                </div>
            </div>
        </div>
    @endif
</div>
