<div class="p-[25px]">
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400 rounded-lg text-sm">
            {{ session('message') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-[25px]">
        <div>
            <h1 class="text-2xl font-bold text-black dark:text-white">Kelola Dokumen PPID</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Dokumen legalitas & informasi publik yang tampil di halaman PPID website.
            </p>
        </div>

        <button wire:click="openModal" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg flex items-center gap-2 shadow-sm transition">
            + Tambah Dokumen
        </button>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        <th class="px-6 py-4">Judul Dokumen</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Format</th>
                        <th class="px-6 py-4">Ukuran</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm text-gray-700 dark:text-gray-200">
                    @forelse ($dokumenList as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white max-w-xs">{{ $item->judul }}</td>
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-xs">{{ $item->kategori }}</td>
                            <td class="px-6 py-4">
                                <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded text-[10px] font-black">{{ $item->format }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-xs">{{ $item->ukuran_formatted }}</td>
                            <td class="px-6 py-4 text-center space-x-2 whitespace-nowrap">
                                <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" class="text-green-600 hover:underline">Lihat</a>
                                <button wire:click="openEdit({{ $item->id }})" class="text-blue-600 dark:text-blue-400 hover:underline">Edit</button>
                                <button wire:click="delete({{ $item->id }})" wire:confirm="Yakin ingin menghapus dokumen ini?" class="text-red-600 dark:text-red-400 hover:underline">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                                Belum ada dokumen. Klik tombol <b>+ Tambah Dokumen</b> untuk menambahkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-full max-w-lg p-6 relative max-h-[90vh] overflow-y-auto">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4">
                    {{ $editingId ? 'Edit Dokumen' : 'Tambah Dokumen Baru' }}
                </h2>

                <form wire:submit.prevent="save">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul Dokumen</label>
                        <input type="text" wire:model="judul" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Laporan Keuangan Dinas 2026">
                        @error('judul') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kategori PPID</label>
                        <select wire:model="kategori" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach ($kategoriOptions as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                        @error('kategori') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">File Dokumen</label>

                        @if ($existingFileName && ! $file)
                            <p class="text-xs text-gray-500 mb-2">File saat ini: <b>{{ $existingFileName }}</b></p>
                        @endif

                        <input type="file" wire:model="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-xs text-gray-400 mt-1">Format & ukuran file terisi otomatis. Maks 10MB (PDF, Word, Excel, PowerPoint).</p>
                        <div wire:loading wire:target="file" class="mt-1 text-xs text-gray-500">Mengunggah...</div>
                        @error('file') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            {{ $editingId ? 'Simpan Perubahan' : 'Simpan Dokumen' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
