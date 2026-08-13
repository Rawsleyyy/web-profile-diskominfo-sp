@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div>
    <div class="p-[25px]">
        <div class="flex items-center justify-between mb-[25px]">
            <div>
                <h1 class="text-2xl font-bold text-black dark:text-white">Kelola Dokumen & Data Publik</h1>
                <p class="mt-1 text-gray-500 dark:text-gray-400">Tambah, edit, dan kelola dokumen publik (Rilis Data, LKJIP, Statistik).</p>
            </div>
            <button wire:click="openModal"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                + Tambah Dokumen
            </button>
        </div>

        @if (session('message'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">
                {{ session('message') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm dark:bg-gray-800 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-700 text-sm text-gray-500 dark:text-gray-300">
                    <tr>
                        <th class="px-6 py-3">Judul</th>
                        <th class="px-6 py-3">Kategori</th>
                        <th class="px-6 py-3">Format</th>
                        <th class="px-6 py-3">Ukuran</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($dokumenList as $dokumen)
                        <tr>
                            <td class="px-6 py-3">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $dokumen->judul }}</p>
                                @if ($dokumen->file_path)
                                    <a href="{{ Storage::url($dokumen->file_path) }}" target="_blank"
                                        class="text-xs text-blue-600 hover:underline">Lihat file</a>
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">
                                    {{ $dokumen->kategori }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-300">
                                {{ $dokumen->format ?? '-' }}
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-300">
                                {{ $dokumen->ukuran_kb ? number_format($dokumen->ukuran_kb) . ' KB' : '-' }}
                            </td>
                            <td class="px-6 py-3 text-right space-x-2">
                                <button wire:click="openEdit({{ $dokumen->id }})"
                                    class="text-blue-600 hover:underline text-sm">Edit</button>
                                <button wire:click="delete({{ $dokumen->id }})"
                                    wire:confirm="Yakin ingin menghapus dokumen ini?"
                                    class="text-red-600 hover:underline text-sm">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                                Belum ada dokumen. Klik "Tambah Dokumen" untuk membuat yang pertama.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($isModalOpen)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg w-full max-w-lg max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                        {{ $editingId ? 'Edit Dokumen' : 'Tambah Dokumen' }}
                    </h2>

                    <form wire:submit="save" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul Dokumen</label>
                            <input type="text" wire:model="judul"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('judul') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kategori</label>
                            <select wire:model="kategori"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @foreach ($kategoriOptions as $opsi)
                                    <option value="{{ $opsi }}">{{ $opsi }}</option>
                                @endforeach
                            </select>
                            @error('kategori') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                File Dokumen (PDF, Word, Excel, PowerPoint)
                            </label>
                            <input type="file" wire:model="file"
                                class="w-full text-sm text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <div wire:loading wire:target="file" class="text-xs text-gray-400 mt-1">Mengunggah file...</div>
                            @if ($existingFileName && !$file)
                                <p class="text-xs text-gray-400 mt-1">File saat ini: {{ $existingFileName }}</p>
                            @endif
                            @error('file') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" wire:click="closeModal"
                                class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:underline">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                                wire:loading.attr="disabled" wire:target="save">
                                <span wire:loading.remove wire:target="save">{{ $editingId ? 'Update' : 'Simpan' }}</span>
                                <span wire:loading wire:target="save">Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
