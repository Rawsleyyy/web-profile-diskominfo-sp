@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div>
    <div class="p-[25px]">
        <div class="flex items-center justify-between mb-[25px]">
            <div>
                <h1 class="text-2xl font-bold text-black dark:text-white">Kelola Podcast (KOMINPOD)</h1>
                <p class="mt-1 text-gray-500 dark:text-gray-400">Tambah, edit, dan kelola episode podcast.</p>
            </div>
            <button wire:click="openCreateModal"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                + Tambah Podcast
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
                        <th class="px-6 py-3">Thumbnail</th>
                        <th class="px-6 py-3">Judul</th>
                        <th class="px-6 py-3">Episode</th>
                        <th class="px-6 py-3">Sumber Audio</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($podcasts as $podcast)
                        <tr>
                            <td class="px-6 py-3">
                                @if ($podcast->thumbnail)
                                    <img src="{{ Storage::url($podcast->thumbnail) }}" class="w-14 h-14 object-cover rounded-lg">
                                @else
                                    <div class="w-14 h-14 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center text-gray-400 text-xs">
                                        No Img
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $podcast->judul }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-1">{{ $podcast->deskripsi }}</p>
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-300">
                                {{ $podcast->episode ?: '-' }}
                            </td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-1 text-xs rounded-full {{ $podcast->isLink() ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $podcast->isLink() ? 'Link' : 'Upload File' }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-1 text-xs rounded-full {{ $podcast->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $podcast->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right space-x-2">
                                <button wire:click="openEditModal({{ $podcast->id }})"
                                    class="text-blue-600 hover:underline text-sm">Edit</button>
                                <button wire:click="delete({{ $podcast->id }})"
                                    wire:confirm="Yakin ingin menghapus podcast ini?"
                                    class="text-red-600 hover:underline text-sm">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                Belum ada podcast. Klik "Tambah Podcast" untuk membuat yang pertama.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg w-full max-w-lg max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                        {{ $isEditMode ? 'Edit Podcast' : 'Tambah Podcast' }}
                    </h2>

                    <form wire:submit="save" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul</label>
                            <input type="text" wire:model="judul"
                                class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @error('judul') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Episode</label>
                            <input type="text" wire:model="episode" placeholder="Contoh: Episode 12"
                                class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @error('episode') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
                            <textarea wire:model="deskripsi" rows="3"
                                class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                            @error('deskripsi') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe Sumber Audio</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2">
                                    <input type="radio" wire:model.live="type" value="file">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Upload File</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" wire:model.live="type" value="link">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Link (YouTube/Spotify)</span>
                                </label>
                            </div>
                        </div>

                        @if ($type === 'file')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">File Audio (mp3/wav/ogg)</label>
                                <input type="file" wire:model="audio" accept="audio/*"
                                    class="w-full text-sm text-gray-600 dark:text-gray-300">
                                <div wire:loading wire:target="audio" class="text-xs text-gray-400 mt-1">Mengunggah file...</div>
                                @if ($existingUrlAudio && !$audio)
                                    <p class="text-xs text-gray-400 mt-1">File saat ini: {{ basename($existingUrlAudio) }}</p>
                                @endif
                                @error('audio') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        @else
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Link Audio</label>
                                <input type="url" wire:model="audio_link" placeholder="https://youtube.com/..."
                                    class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('audio_link') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Thumbnail (opsional)</label>
                            <input type="file" wire:model="thumbnail" accept="image/*"
                                class="w-full text-sm text-gray-600 dark:text-gray-300">
                            <div wire:loading wire:target="thumbnail" class="text-xs text-gray-400 mt-1">Mengunggah gambar...</div>
                            @error('thumbnail') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-center gap-2">
                            <input type="checkbox" wire:model="is_active" id="is_active">
                            <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">Aktifkan sekarang</label>
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" wire:click="closeModal"
                                class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:underline">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                                wire:loading.attr="disabled" wire:target="save">
                                <span wire:loading.remove wire:target="save">{{ $isEditMode ? 'Update' : 'Simpan' }}</span>
                                <span wire:loading wire:target="save">Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
