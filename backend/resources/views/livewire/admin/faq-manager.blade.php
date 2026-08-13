<div class="p-[25px]">
    <!-- Notifikasi Sukses -->
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400 rounded-lg text-sm">
            {{ session('message') }}
        </div>
    @endif

    <!-- Header Halaman -->
    <div class="flex items-center justify-between mb-[25px]">
        <div>
            <h1 class="text-2xl font-bold text-black dark:text-white">FAQ & MONIKS</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola pertanyaan publik sekaligus sumber jawaban MONIKS. Gunakan kata kunci atau sinonim agar pertanyaan pengunjung lebih mudah dikenali.</p>
        </div>

        <button wire:click="openModal" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg flex items-center gap-2 shadow-sm transition">
            + Tambah FAQ
        </button>
    </div>

    <!-- Kartu Statistik -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-[25px]">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 px-5 py-4">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total FAQ</p>
            <p class="mt-1 text-xl font-bold text-black dark:text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 px-5 py-4">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Aktif di Publik</p>
            <p class="mt-1 text-xl font-bold text-green-600 dark:text-green-400">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 px-5 py-4">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Nonaktif</p>
            <p class="mt-1 text-xl font-bold text-gray-400">{{ $stats['inactive'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 px-5 py-4">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Kategori</p>
            <p class="mt-1 text-xl font-bold text-black dark:text-white">{{ $stats['categories'] }}</p>
        </div>
    </div>

    <!-- Info Alur Kerja -->
    <div class="grid gap-3 md:grid-cols-3 mb-[25px]">
        <div class="flex gap-3 rounded-xl border border-green-100 dark:border-green-900/40 bg-green-50 dark:bg-green-900/20 p-4">
            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-green-600 text-xs font-bold text-white">1</span>
            <div>
                <p class="text-sm font-semibold text-black dark:text-white">FAQ Publik</p>
                <p class="mt-1 text-xs leading-5 text-gray-500 dark:text-gray-400">FAQ aktif otomatis tampil pada halaman publik.</p>
            </div>
        </div>
        <div class="flex gap-3 rounded-xl border border-blue-100 dark:border-blue-900/40 bg-blue-50 dark:bg-blue-900/20 p-4">
            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">2</span>
            <div>
                <p class="text-sm font-semibold text-black dark:text-white">Basis MONIKS</p>
                <p class="mt-1 text-xs leading-5 text-gray-500 dark:text-gray-400">Pertanyaan, jawaban, dan keyword digunakan untuk pencarian jawaban.</p>
            </div>
        </div>
        <div class="flex gap-3 rounded-xl border border-amber-100 dark:border-amber-900/40 bg-amber-50 dark:bg-amber-900/20 p-4">
            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-amber-500 text-xs font-bold text-white">3</span>
            <div>
                <p class="text-sm font-semibold text-black dark:text-white">Fallback Aman</p>
                <p class="mt-1 text-xs leading-5 text-gray-500 dark:text-gray-400">Jika tidak cocok, MONIKS mengarahkan ke kanal resmi tanpa mengarang jawaban.</p>
            </div>
        </div>
    </div>

    <!-- Tabel Data FAQ -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 px-6 py-5 border-b border-gray-100 dark:border-gray-700">
            <div>
                <h2 class="font-bold text-black dark:text-white">Daftar FAQ</h2>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    @if($search)
                        Menampilkan {{ $faqs->count() }} hasil pencarian.
                    @else
                        Atur urutan, status, konten, dan keyword yang digunakan MONIKS.
                    @endif
                </p>
            </div>

            <div class="relative w-full md:max-w-sm">
                <input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari pertanyaan, jawaban, kategori, atau keyword..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                <div wire:loading wire:target="search" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">
                    Mencari...
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        <th class="px-6 py-4 text-center">Urutan</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Pertanyaan & Jawaban</th>
                        <th class="px-6 py-4">Keyword MONIKS</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm text-gray-700 dark:text-gray-200">
                    @forelse($faqs as $faq)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition" wire:key="faq-row-{{ $faq->id }}">
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex min-w-9 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700 px-2 py-1 text-xs font-semibold text-gray-600 dark:text-gray-300">{{ $faq->sort_order }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full bg-blue-50 dark:bg-blue-900/30 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:text-blue-400">{{ $faq->category }}</span>
                            </td>
                            <td class="px-6 py-4 max-w-md">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $faq->question }}</p>
                                <p class="mt-1 line-clamp-2 text-xs text-gray-500 dark:text-gray-400">{{ $faq->answer }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if($faq->keywords)
                                    <div class="flex max-w-[280px] flex-wrap gap-1.5">
                                        @foreach(array_slice(array_filter(array_map('trim', explode(',', $faq->keywords))), 0, 5) as $keyword)
                                            <span class="rounded-md bg-gray-100 dark:bg-gray-700 px-2 py-1 text-[11px] font-medium text-gray-600 dark:text-gray-300">{{ $keyword }}</span>
                                        @endforeach
                                        @if(count(array_filter(array_map('trim', explode(',', $faq->keywords)))) > 5)
                                            <span class="rounded-md bg-gray-100 dark:bg-gray-700 px-2 py-1 text-[11px] font-semibold text-gray-500">+{{ count(array_filter(array_map('trim', explode(',', $faq->keywords)))) - 5 }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs italic text-gray-400">Belum ada keyword</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button
                                    wire:click="toggleActive({{ $faq->id }})"
                                    title="Klik untuk mengubah status"
                                    class="px-3 py-1 rounded-full text-xs font-semibold transition {{ $faq->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200 dark:bg-green-900/40 dark:text-green-400' : 'bg-gray-100 text-gray-500 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-400' }}"
                                >
                                    {{ $faq->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 text-center space-x-2 whitespace-nowrap">
                                <button wire:click="edit({{ $faq->id }})" class="text-blue-600 dark:text-blue-400 hover:underline">Edit</button>
                                <button wire:click="delete({{ $faq->id }})" wire:confirm="Hapus FAQ ini?" class="text-red-600 dark:text-red-400 hover:underline">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                {{ $search ? 'FAQ tidak ditemukan. Coba kata kunci lain.' : 'Belum ada FAQ. Klik tombol' }}
                                @unless($search)
                                    <b>+ Tambah FAQ</b> untuk menambahkan.
                                @endunless
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-col gap-1 border-t border-gray-100 dark:border-gray-700 px-6 py-3 text-xs text-gray-500 dark:text-gray-400 sm:flex-row sm:items-center sm:justify-between">
            <span>{{ $faqs->count() }} item ditampilkan</span>
            <span>Tips: keyword singkat seperti <strong class="font-semibold text-gray-700 dark:text-gray-300">aduan, lapor, ULAS, pengaduan</strong> lebih mudah dicocokkan.</span>
        </div>
    </div>

    <!-- MODAL FORM TAMBAH / EDIT FAQ -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-full max-w-2xl p-6 relative max-h-[90vh] overflow-y-auto">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-1">
                    {{ $faqId ? 'Edit FAQ' : 'Tambah FAQ Baru' }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Pastikan jawaban merupakan informasi resmi yang boleh ditampilkan kepada publik.</p>

                <form wire:submit="save">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kategori</label>
                            <input wire:model="category" placeholder="Contoh: PPID / Layanan / Pengaduan" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('category') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Urutan</label>
                            <input type="number" min="0" wire:model="sortOrder" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-400">Angka kecil tampil lebih dahulu.</p>
                            @error('sortOrder') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pertanyaan</label>
                            <input wire:model="question" placeholder="Contoh: Bagaimana cara melaporkan aduan melalui ULAS?" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('question') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jawaban</label>
                            <textarea wire:model="answer" rows="5" placeholder="Tulis jawaban resmi yang akan ditampilkan kepada publik..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                            @error('answer') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <div class="flex flex-wrap items-end justify-between gap-2 mb-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kata Kunci / Sinonim MONIKS</label>
                                <span class="text-[11px] font-medium text-gray-400">Pisahkan dengan koma</span>
                            </div>
                            <textarea wire:model="keywords" rows="3" placeholder="Contoh: aduan, lapor, ulas, komplain, pengaduan" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Gunakan istilah singkat yang kemungkinan diketik pengunjung, bukan kalimat panjang.</p>
                            @error('keywords') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2 flex items-center gap-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/40 px-4 py-3">
                            <input type="checkbox" wire:model="isActive" id="isActive" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="isActive" class="text-sm">
                                <span class="block font-medium text-gray-700 dark:text-gray-300">Aktifkan FAQ</span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400">FAQ aktif akan tersedia pada halaman publik dan digunakan sebagai sumber jawaban MONIKS.</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-5 mt-2 border-t border-gray-100 dark:border-gray-700">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            {{ $faqId ? 'Simpan Perubahan' : 'Simpan FAQ' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>