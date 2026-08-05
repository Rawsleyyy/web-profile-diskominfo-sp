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
            <h1 class="text-2xl font-bold text-black dark:text-white">Kelola Berita</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Berita di sini otomatis tampil di website publik saat status "Terbit" diaktifkan.</p>
        </div>

        <button wire:click="openModal" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg flex items-center gap-2 shadow-sm transition">
            + Tambah Berita
        </button>
    </div>

    <!-- Tabel Data Berita -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        <th class="px-6 py-4">Thumbnail</th>
                        <th class="px-6 py-4">Judul</th>
                        <th class="px-6 py-4">Penulis</th>
                        <th class="px-6 py-4 text-center">Dilihat</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm text-gray-700 dark:text-gray-200">
                    @forelse ($beritas as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                            <td class="px-6 py-4">
                                @if($item->thumbnail)
                                    <img src="{{ asset('storage/' . $item->thumbnail) }}" class="w-16 h-12 object-cover rounded-lg border border-gray-200 dark:border-gray-600">
                                @else
                                    <div class="w-16 h-12 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center border border-gray-200">
                                        <span class="text-xs text-gray-400">No Image</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white max-w-xs">
                                {{ $item->judul }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                {{ $item->author?->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 font-semibold">
                                {{ number_format($item->views) }}
                            </td>
                            <td class="px-6 py-4">
                                <button wire:click="togglePublish({{ $item->id }})"
                                    class="px-3 py-1 rounded-full text-xs font-semibold transition {{ $item->status_publish ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                    {{ $item->status_publish ? 'Terbit' : 'Draft' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 text-center space-x-2 whitespace-nowrap">
                                <button wire:click="openEdit({{ $item->id }})" class="text-blue-600 dark:text-blue-400 hover:underline">Edit</button>
                                <button wire:click="delete({{ $item->id }})" wire:confirm="Yakin ingin menghapus berita ini?" class="text-red-600 dark:text-red-400 hover:underline">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                Belum ada berita. Klik tombol <b>+ Tambah Berita</b> untuk menambahkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($beritas->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $beritas->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL FORM TAMBAH / EDIT BERITA -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-full max-w-2xl p-6 relative max-h-[90vh] overflow-y-auto">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4">
                    {{ $editingId ? 'Edit Berita' : 'Tambah Berita Baru' }}
                </h2>

                <form wire:submit.prevent="save">
                    <!-- Judul -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul Berita</label>
                        <input type="text" wire:model="judul" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Masukkan judul berita...">
                        @error('judul') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Thumbnail -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Thumbnail Berita</label>

                        @if ($existingThumbnail && ! $thumbnail)
                            <div class="mb-2">
                                <p class="text-xs text-gray-500 mb-1">Thumbnail saat ini:</p>
                                <img src="{{ asset('storage/' . $existingThumbnail) }}" class="w-full h-40 object-cover rounded-lg border border-gray-200">
                            </div>
                        @endif

                        <input type="file" wire:model="thumbnail" accept="image/*" class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <div wire:loading wire:target="thumbnail" class="mt-1 text-xs text-gray-500">Mengunggah...</div>
                        @error('thumbnail') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror

                        @if ($thumbnail)
                            <div class="mt-3">
                                <p class="text-xs text-gray-500 mb-1">Preview Thumbnail Baru:</p>
                                <img src="{{ $thumbnail->temporaryUrl() }}" class="w-full h-40 object-cover rounded-lg border border-gray-200">
                            </div>
                        @endif
                    </div>

                    <!-- Konten -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Konten Berita</label>
                        <textarea wire:model="konten" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" rows="6" placeholder="Tulis isi berita di sini..."></textarea>
                        @error('konten') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Status Publish -->
                    <div class="mb-4 flex items-center gap-3">
                        <input type="checkbox" wire:model="status_publish" id="status_publish" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="status_publish" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Terbitkan berita ini sekarang (langsung tampil di website publik)
                        </label>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex justify-end gap-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            {{ $editingId ? 'Simpan Perubahan' : 'Simpan Berita' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
