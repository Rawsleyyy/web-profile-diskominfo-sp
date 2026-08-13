<div class="p-[25px]">
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Kelola FAQ & MONIKS</h1>
            <p class="mt-1 max-w-3xl text-sm text-slate-500 dark:text-slate-400">
                FAQ aktif tampil di halaman publik dan sekaligus menjadi basis pengetahuan MONIKS. Tambahkan kata kunci/sinonim agar MONIKS lebih mudah mengenali variasi pertanyaan pengunjung.
            </p>
        </div>
        <button type="button" wire:click="openModal" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700">+ Tambah FAQ</button>
    </div>

    <div class="mb-6 grid gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">1. FAQ Publik</p>
            <p class="mt-2 text-sm text-emerald-900">Pertanyaan aktif otomatis muncul sebagai accordion di halaman beranda.</p>
        </div>
        <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">2. Basis MONIKS</p>
            <p class="mt-2 text-sm text-blue-900">Pertanyaan, jawaban, dan kata kunci yang sama dipakai MONIKS untuk mencari jawaban.</p>
        </div>
        <div class="rounded-xl border border-amber-100 bg-amber-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">3. Fallback Aman</p>
            <p class="mt-2 text-sm text-amber-900">Jika tidak cocok, MONIKS tidak mengarang. Ia mengarahkan ke FAQ, kontak, atau layanan resmi.</p>
        </div>
    </div>

    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        @if (session()->has('message'))
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700">{{ session('message') }}</div>
        @endif

        <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari pertanyaan, jawaban, kategori, atau kata kunci..." class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white md:w-[420px]">
            <p class="text-xs text-gray-500">Tips keyword: <strong>aduan, lapor, ulas, pengaduan</strong></p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b text-gray-600 dark:border-gray-700 dark:text-gray-300">
                        <th class="p-3">Urutan</th>
                        <th class="p-3">Kategori</th>
                        <th class="p-3">Pertanyaan / Jawaban</th>
                        <th class="p-3">Kata Kunci MONIKS</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y dark:divide-gray-700">
                    @forelse($faqs as $faq)
                        <tr class="align-top">
                            <td class="p-3">{{ $faq->sort_order }}</td>
                            <td class="p-3"><span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">{{ $faq->category }}</span></td>
                            <td class="p-3 max-w-xl">
                                <p class="font-semibold text-slate-900 dark:text-white">{{ $faq->question }}</p>
                                <p class="mt-1 line-clamp-2 text-xs leading-5 text-gray-500">{{ $faq->answer }}</p>
                            </td>
                            <td class="p-3 max-w-xs text-xs leading-5 text-gray-500">{{ $faq->keywords ?: '—' }}</td>
                            <td class="p-3">
                                <button wire:click="toggleActive({{ $faq->id }})" class="rounded-full px-3 py-1 text-xs {{ $faq->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                                    {{ $faq->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                <button wire:click="edit({{ $faq->id }})" class="mr-3 text-blue-600">Edit</button>
                                <button wire:click="delete({{ $faq->id }})" wire:confirm="Hapus FAQ ini?" class="text-red-600">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-10 text-center text-gray-500">Belum ada FAQ.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-xl bg-white p-6 shadow-xl dark:bg-gray-800">
                <div class="mb-5 flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $faqId ? 'Edit FAQ' : 'Tambah FAQ' }}</h3>
                        <p class="mt-1 text-xs text-gray-500">Jawaban harus berupa informasi yang memang boleh disampaikan kepada publik.</p>
                    </div>
                    <button type="button" wire:click="closeModal" class="text-2xl leading-none text-gray-400 hover:text-gray-700">×</button>
                </div>

                <form wire:submit="save" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm dark:text-gray-200">Kategori</label>
                        <input wire:model="category" placeholder="Contoh: PPID / Layanan / Pengaduan" class="w-full rounded-lg border p-2 dark:bg-gray-900 dark:text-white">
                        @error('category')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm dark:text-gray-200">Urutan</label>
                        <input type="number" min="0" wire:model="sortOrder" class="w-full rounded-lg border p-2 dark:bg-gray-900 dark:text-white">
                        @error('sortOrder')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm dark:text-gray-200">Pertanyaan</label>
                        <input wire:model="question" placeholder="Contoh: Bagaimana cara melaporkan aduan melalui ULAS?" class="w-full rounded-lg border p-2 dark:bg-gray-900 dark:text-white">
                        @error('question')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm dark:text-gray-200">Jawaban</label>
                        <textarea wire:model="answer" rows="5" placeholder="Jawaban resmi yang akan ditampilkan ke publik..." class="w-full rounded-lg border p-2 dark:bg-gray-900 dark:text-white"></textarea>
                        @error('answer')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm dark:text-gray-200">Kata Kunci / Sinonim untuk MONIKS</label>
                        <textarea wire:model="keywords" rows="3" placeholder="Pisahkan dengan koma. Contoh: aduan, lapor, ulas, komplain, pengaduan" class="w-full rounded-lg border p-2 dark:bg-gray-900 dark:text-white"></textarea>
                        <p class="mt-1 text-xs text-gray-500">Tidak perlu menulis kalimat panjang. Gunakan istilah yang mungkin diketik pengunjung.</p>
                        @error('keywords')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-gray-200 md:col-span-2">
                        <input type="checkbox" wire:model="isActive" class="rounded border-gray-300">
                        Aktifkan FAQ ini di publik dan MONIKS
                    </label>
                    <div class="mt-2 flex justify-end gap-3 md:col-span-2">
                        <button type="button" wire:click="closeModal" class="rounded-lg border border-gray-200 px-5 py-2.5 text-sm font-medium text-gray-600">Batal</button>
                        <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700">Simpan FAQ</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
