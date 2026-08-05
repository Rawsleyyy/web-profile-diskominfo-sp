<div class="p-[25px]">
    <div class="mb-[25px]">
        <h1 class="text-2xl font-bold text-black dark:text-white">Kelola Layanan</h1>
        <p class="mt-1 text-gray-500 dark:text-gray-400">Data aktif otomatis ditampilkan pada halaman publik React.</p>
    </div>

    <div class="w-full bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('message') }}</div>
        @endif

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama layanan..." class="w-full md:w-80 px-4 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg dark:text-white">
            <button type="button" wire:click="openModal" class="bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm px-5 py-2.5 rounded-lg">+ Tambah Layanan</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead><tr class="border-b dark:border-gray-700 text-gray-600 dark:text-gray-300">
                    <th class="p-3">Urutan</th><th class="p-3">Ikon</th><th class="p-3">Layanan</th><th class="p-3">URL</th><th class="p-3">Status</th><th class="p-3">Aksi</th>
                </tr></thead>
                <tbody class="divide-y dark:divide-gray-700">
                    @forelse($services as $service)
                        <tr>
                            <td class="p-3">{{ $service->urutan }}</td>
                            <td class="p-3">
                                @if($service->icon_path)
                                    <img src="{{ Storage::url($service->icon_path) }}" alt="" class="w-12 h-12 rounded-lg object-cover">
                                @else
                                    <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">—</div>
                                @endif
                            </td>
                            <td class="p-3"><p class="font-semibold dark:text-white">{{ $service->nama_layanan }}</p><p class="text-xs text-gray-500">{{ $service->kategori }} · {{ $service->deskripsi }}</p></td>
                            <td class="p-3 max-w-xs"><a href="{{ $service->url_eksternal }}" target="_blank" rel="noopener" class="text-blue-600 break-all">{{ $service->url_eksternal }}</a></td>
                            <td class="p-3"><button wire:click="toggleActive({{ $service->id }})" class="px-3 py-1 rounded-full text-xs {{ $service->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">{{ $service->is_active ? 'Aktif' : 'Nonaktif' }}</button></td>
                            <td class="p-3 whitespace-nowrap"><button wire:click="edit({{ $service->id }})" class="text-blue-600 mr-3">Edit</button><button wire:click="delete({{ $service->id }})" wire:confirm="Hapus layanan ini?" class="text-red-600">Hapus</button></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-10 text-center text-gray-500">Belum ada layanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-bold dark:text-white mb-4">{{ $service_id ? 'Edit Layanan' : 'Tambah Layanan' }}</h3>
                <form wire:submit="save" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-sm mb-1 dark:text-gray-200">Nama layanan</label><input wire:model="nama_layanan" class="w-full rounded-lg border p-2 dark:bg-gray-900 dark:text-white">@error('nama_layanan')<p class="text-xs text-red-500">{{ $message }}</p>@enderror</div>
                    <div><label class="block text-sm mb-1 dark:text-gray-200">Kategori</label><input wire:model="kategori" class="w-full rounded-lg border p-2 dark:bg-gray-900 dark:text-white">@error('kategori')<p class="text-xs text-red-500">{{ $message }}</p>@enderror</div>
                    <div class="md:col-span-2"><label class="block text-sm mb-1 dark:text-gray-200">Deskripsi singkat</label><textarea wire:model="deskripsi" rows="3" class="w-full rounded-lg border p-2 dark:bg-gray-900 dark:text-white"></textarea>@error('deskripsi')<p class="text-xs text-red-500">{{ $message }}</p>@enderror</div>
                    <div class="md:col-span-2"><label class="block text-sm mb-1 dark:text-gray-200">URL eksternal</label><input type="url" wire:model="url_eksternal" placeholder="https://..." class="w-full rounded-lg border p-2 dark:bg-gray-900 dark:text-white">@error('url_eksternal')<p class="text-xs text-red-500">{{ $message }}</p>@enderror</div>
                    <div><label class="block text-sm mb-1 dark:text-gray-200">Urutan</label><input type="number" wire:model="urutan" min="0" class="w-full rounded-lg border p-2 dark:bg-gray-900 dark:text-white">@error('urutan')<p class="text-xs text-red-500">{{ $message }}</p>@enderror</div>
                    <div><label class="block text-sm mb-1 dark:text-gray-200">Ikon (maks. 2 MB)</label><input type="file" wire:model="icon" accept="image/*" class="w-full text-sm dark:text-white">@error('icon')<p class="text-xs text-red-500">{{ $message }}</p>@enderror</div>
                    <label class="md:col-span-2 flex items-center gap-2 dark:text-gray-200"><input type="checkbox" wire:model="is_active"> Tampilkan pada halaman publik</label>
                    <div class="md:col-span-2 flex justify-end gap-3 pt-4 border-t dark:border-gray-700"><button type="button" wire:click="closeModal" class="px-4 py-2 rounded-lg bg-gray-200">Batal</button><button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif
</div>
