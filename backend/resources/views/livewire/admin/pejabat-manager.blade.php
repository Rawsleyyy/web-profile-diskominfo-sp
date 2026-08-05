<div class="p-[25px]">
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400 rounded-lg text-sm">
            {{ session('message') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-[25px]">
        <div>
            <h1 class="text-2xl font-bold text-black dark:text-white">Kelola Struktur Organisasi</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                2 pejabat dengan status "Tampilkan di Beranda" akan muncul di homepage. Pilih "Atasan" untuk menyusun hierarki lengkap yang tampil di halaman "Struktur Organisasi".
            </p>
        </div>

        <button wire:click="openModal" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg flex items-center gap-2 shadow-sm transition">
            + Tambah Pejabat
        </button>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        <th class="px-6 py-4 text-center">Urutan</th>
                        <th class="px-6 py-4">Foto</th>
                        <th class="px-6 py-4">Nama</th>
                        <th class="px-6 py-4">Jabatan</th>
                        <th class="px-6 py-4">Atasan</th>
                        <th class="px-6 py-4 text-center">Tampil di Beranda</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm text-gray-700 dark:text-gray-200">
                    @forelse ($pejabatList as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                            <td class="px-6 py-4 text-center font-bold text-gray-400">{{ $item->urutan }}</td>
                            <td class="px-6 py-4">
                                @if($item->foto)
                                    <img src="{{ asset('storage/' . $item->foto) }}" class="w-12 h-12 object-cover rounded-full border border-gray-200 dark:border-gray-600">
                                @else
                                    <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center border border-gray-200">
                                        <span class="text-[10px] text-gray-400">No Foto</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $item->nama }}</td>
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $item->jabatan }}</td>
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                @if ($item->parent)
                                    <span class="text-xs">↳ {{ $item->parent->nama }}</span>
                                @else
                                    <span class="text-xs italic text-gray-400">— Level Teratas</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($item->tampil_utama)
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Ya</span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Tidak</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center space-x-2 whitespace-nowrap">
                                <button wire:click="openEdit({{ $item->id }})" class="text-blue-600 dark:text-blue-400 hover:underline">Edit</button>
                                <button wire:click="delete({{ $item->id }})" wire:confirm="Yakin ingin menghapus pejabat ini? Bawahan langsungnya (jika ada) tidak akan ikut terhapus, hanya jadi tidak punya atasan." class="text-red-600 dark:text-red-400 hover:underline">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                                Belum ada data pejabat. Klik tombol <b>+ Tambah Pejabat</b> untuk menambahkan.
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
                    {{ $editingId ? 'Edit Pejabat' : 'Tambah Pejabat Baru' }}
                </h2>

                <form wire:submit.prevent="save">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap</label>
                        <input type="text" wire:model="nama" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Budi Santoso, S.Kom">
                        @error('nama') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jabatan</label>
                        <input type="text" wire:model="jabatan" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Kepala Dinas / Kepala Bidang X">
                        @error('jabatan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Atasan</label>
                        <select wire:model="parent_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">— Tidak ada (Level Teratas) —</option>
                            @foreach ($pejabatOptions as $opt)
                                <option value="{{ $opt->id }}">{{ $opt->nama }} ({{ $opt->jabatan }})</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Menentukan posisi pejabat ini di struktur pohon halaman publik.</p>
                        @error('parent_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Urutan Tampil</label>
                        <input type="number" wire:model="urutan" min="0" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-400 mt-1">Angka lebih kecil tampil lebih dulu (di antara pejabat dengan atasan yang sama).</p>
                        @error('urutan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Foto</label>

                        @if ($existingFoto && ! $foto)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $existingFoto) }}" class="w-24 h-24 object-cover rounded-full border border-gray-200">
                            </div>
                        @endif

                        <input type="file" wire:model="foto" accept="image/*" class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <div wire:loading wire:target="foto" class="mt-1 text-xs text-gray-500">Mengunggah...</div>
                        @error('foto') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror

                        @if ($foto)
                            <div class="mt-3">
                                <img src="{{ $foto->temporaryUrl() }}" class="w-24 h-24 object-cover rounded-full border border-gray-200">
                            </div>
                        @endif
                    </div>

                    <div class="mb-4 flex items-center gap-3">
                        <input type="checkbox" wire:model="tampil_utama" id="tampil_utama" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="tampil_utama" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Tampilkan di Beranda (biasanya untuk Kepala Dinas & Sekretaris Dinas)
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            {{ $editingId ? 'Simpan Perubahan' : 'Simpan Pejabat' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
