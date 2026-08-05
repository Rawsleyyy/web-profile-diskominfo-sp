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
            <h1 class="text-2xl font-bold text-black dark:text-white">Kelola Artikel</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manajemen postingan dan artikel di sistem.</p>
        </div>

        <button wire:click="openModal" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg flex items-center gap-2 shadow-sm transition">
            + Tambah Artikel
        </button>
    </div>

    <!-- Tabel Data Artikel -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        <th class="px-6 py-4">Judul Artikel</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Penulis</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm text-gray-700 dark:text-gray-200">
                    @forelse ($articles as $item)
                        <tr wire:key="article-{{ $item->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                {{ $item->title }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400 rounded-full">
                                    {{ $item->category }}
                                </span>
                            </td>
                            <td class="px-6 py-4">{{ $item->author }}</td>
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($item->published_at)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-center space-x-2">
                                <button class="text-blue-600 dark:text-blue-400 hover:underline">Edit</button>
                                <button
                                    type="button"
                                    wire:click="delete({{ $item->id }})"
                                    wire:confirm="Yakin ingin menghapus artikel ini? Gambar artikel juga akan dihapus."
                                    wire:loading.attr="disabled"
                                    wire:target="delete({{ $item->id }})"
                                    class="text-red-600 dark:text-red-400 hover:underline disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                                Belum ada data artikel. Klik tombol <b>+ Tambah Artikel</b> untuk menambahkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL FORM TAMBAH ARTIKEL -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-full max-w-2xl p-6 relative max-h-[90vh] overflow-y-auto">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4">Tambah Artikel Baru</h2>

                <form wire:submit.prevent="save">
                    <!-- Judul Artikel -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul Artikel</label>
                        <input type="text" wire:model="title" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Masukkan judul artikel...">
                        @error('title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Gambar / Thumbnail</label>
                        <input type="file" wire:model="image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer border border-gray-300 rounded-lg">
                    </div>

                    <!-- Grid: Kategori, Penulis, Tanggal -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <!-- Kategori -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kategori</label>
                            <select wire:model="category" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="Berita Utama">Berita Utama</option>
                                <option value="Pengumuman">Pengumuman</option>
                                <option value="Edukasi">Edukasi</option>
                                <option value="Teknologi">Teknologi</option>
                            </select>
                            @error('category') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Penulis -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Penulis</label>
                            <input type="text" wire:model="author" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nama penulis...">
                            @error('author') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Tanggal -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal</label>
                            <input type="date" wire:model="published_at" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('published_at') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Isi Artikel -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Isi Artikel</label>
                        <textarea wire:model="content" rows="5" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Tuliskan isi artikel lengkap di sini..."></textarea>
                        @error('content') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex justify-end gap-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            Simpan Artikel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
