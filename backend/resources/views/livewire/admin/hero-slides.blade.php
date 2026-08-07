<div class="p-[25px]">
    @if (session()->has('message'))
        <div class="mb-4 rounded-lg bg-green-100 p-4 text-sm text-green-700">
            {{ session('message') }}
        </div>
    @endif

    <div class="mb-[25px] flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-black">Kelola Header</h1>
            <p class="mt-1 text-sm text-gray-500">
                Atur gambar, teks, urutan, tombol, dan status slide pada header halaman publik.
            </p>
        </div>

        <button
            type="button"
            wire:click="openCreate"
            class="flex shrink-0 items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 font-medium text-white shadow-sm transition hover:bg-blue-700"
        >
            + Tambah Header
        </button>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
                        <th class="px-6 py-4">Gambar</th>
                        <th class="px-6 py-4">Judul</th>
                        <th class="px-6 py-4 text-center">Urutan</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                    @forelse ($slides as $slide)
                        <tr wire:key="hero-slide-{{ $slide->id }}" class="transition hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <img
                                    src="{{ asset('storage/'.$slide->image_path) }}"
                                    alt="{{ $slide->alt_text ?: $slide->title }}"
                                    class="h-20 w-36 rounded-lg border border-gray-200 object-cover"
                                >
                            </td>
                            <td class="max-w-md px-6 py-4">
                                <p class="font-semibold text-gray-900">{{ $slide->title }}</p>
                                <p class="mt-1 line-clamp-2 text-xs text-gray-500">
                                    {{ $slide->subtitle ?: 'Tanpa deskripsi' }}
                                </p>
                                @if ($slide->button_label)
                                    <p class="mt-2 text-xs font-medium text-blue-600">
                                        Tombol: {{ $slide->button_label }}
                                    </p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center font-semibold">{{ $slide->urutan }}</td>
                            <td class="px-6 py-4 text-center">
                                <button
                                    type="button"
                                    wire:click="toggleActive({{ $slide->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="toggleActive({{ $slide->id }})"
                                    class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $slide->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}"
                                    title="Klik untuk mengubah status"
                                >
                                    {{ $slide->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-center">
                                <button
                                    type="button"
                                    wire:click="openEdit({{ $slide->id }})"
                                    class="mr-3 text-blue-600 hover:underline"
                                >
                                    Edit
                                </button>
                                <button
                                    type="button"
                                    wire:click="delete({{ $slide->id }})"
                                    wire:confirm="Yakin ingin menghapus header ini? File gambarnya juga akan dihapus."
                                    wire:loading.attr="disabled"
                                    wire:target="delete({{ $slide->id }})"
                                    class="text-red-600 hover:underline disabled:opacity-50"
                                >
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                Belum ada header dari dashboard. Halaman publik masih menggunakan gambar cadangan bawaan React.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800">
        Rekomendasi gambar: rasio lebar, minimal 1000 × 350 piksel, format JPG/PNG/WebP, maksimal 5 MB.
    </div>

    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm">
            <div class="max-h-[92vh] w-full max-w-3xl overflow-y-auto rounded-xl bg-white p-6 shadow-xl">
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">
                            {{ $editingId ? 'Edit Header' : 'Tambah Header' }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">Data aktif akan dibaca oleh halaman React melalui API.</p>
                    </div>
                    <button type="button" wire:click="closeModal" class="text-2xl leading-none text-gray-400 hover:text-gray-700">&times;</button>
                </div>

                <form wire:submit="save" class="space-y-5">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Judul Header <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            wire:model="title"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                            placeholder="Contoh: DISKOMINFO SP KOTA SURAKARTA"
                        >
                        @error('title') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea
                            wire:model="subtitle"
                            rows="3"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                            placeholder="Teks singkat di bawah judul header"
                        ></textarea>
                        @error('subtitle') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Gambar {{ $editingId ? '(kosongkan jika tidak diganti)' : '' }} <span class="text-red-500">*</span>
                        </label>

                        @if ($existingImage && ! $image)
                            <img src="{{ asset('storage/'.$existingImage) }}" alt="Preview lama" class="mb-3 h-48 w-full rounded-lg border border-gray-200 object-cover">
                        @endif

                        <input
                            type="file"
                            wire:model="image"
                            accept="image/jpeg,image/png,image/webp"
                            class="w-full rounded-lg border border-gray-300 p-2 text-sm"
                        >
                        <div wire:loading wire:target="image" class="mt-1 text-xs text-gray-500">Mengunggah preview...</div>
                        @error('image') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror

                        @if ($image)
                            <img src="{{ $image->temporaryUrl() }}" alt="Preview baru" class="mt-3 h-48 w-full rounded-lg border border-gray-200 object-cover">
                        @endif
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Teks alternatif gambar</label>
                            <input type="text" wire:model="altText" class="w-full rounded-lg border border-gray-300 px-3 py-2" placeholder="Deskripsi gambar untuk aksesibilitas">
                            @error('altText') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Urutan</label>
                            <input type="number" min="0" max="999" wire:model="urutan" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                            @error('urutan') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Teks tombol (opsional)</label>
                            <input type="text" wire:model="buttonLabel" class="w-full rounded-lg border border-gray-300 px-3 py-2" placeholder="Contoh: Lihat Selengkapnya">
                            @error('buttonLabel') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Tujuan tombol (opsional)</label>
                            <input type="text" wire:model="buttonUrl" class="w-full rounded-lg border border-gray-300 px-3 py-2" placeholder="/artikel atau https://surakarta.go.id">
                            @error('buttonUrl') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <label class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">
                        <input type="checkbox" wire:model="isActive" class="h-4 w-4 rounded border-gray-300 text-blue-600">
                        <span class="text-sm font-medium text-gray-700">Tampilkan header ini di halaman publik</span>
                    </label>

                    <div class="flex justify-end gap-3 border-t border-gray-100 pt-4">
                        <button type="button" wire:click="closeModal" class="rounded-lg bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">Batal</button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="save,image" class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 disabled:opacity-60">
                            <span wire:loading.remove wire:target="save">Simpan Header</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
