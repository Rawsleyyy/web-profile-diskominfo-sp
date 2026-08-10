<div class="p-6 md:p-8">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Manajemen Halaman</h1>
            <p class="mt-1 text-sm text-slate-500">Buat halaman temporer atau permanen tanpa menambah file React baru, misalnya HUT RI, beasiswa, atau program khusus.</p>
        </div>
        <button wire:click="openCreate" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">+ Tambah Halaman</button>
    </div>

    @if (session('page-message'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('page-message') }}</div>
    @endif

    @if ($showForm)
        <form wire:submit="save" class="mb-7 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">Judul</label>
                    <input wire:model.live.debounce.400ms="title" class="w-full rounded-xl border border-slate-300 px-3 py-2.5" placeholder="Semarak HUT RI ke-81">
                    @error('title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">Slug URL</label>
                    <input wire:model="slug" class="w-full rounded-xl border border-slate-300 px-3 py-2.5" placeholder="semarak-hut-ri-81">
                    @error('slug') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">Ringkasan</label>
                    <textarea wire:model="excerpt" rows="2" class="w-full rounded-xl border border-slate-300 px-3 py-2.5" placeholder="Ringkasan singkat halaman..."></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">Konten</label>
                    <textarea wire:model="content" rows="12" class="w-full rounded-xl border border-slate-300 px-3 py-2.5" placeholder="Tulis isi halaman. Baris baru akan dipertahankan pada halaman publik."></textarea>
                    @error('content') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">Banner (opsional)</label>
                    <input type="file" wire:model="banner" accept="image/*" class="block w-full text-sm text-slate-600">
                    @if ($existingBanner)<p class="mt-1 text-xs text-slate-500">Banner lama: {{ $existingBanner }}</p>@endif
                    @error('banner') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">Jadwal Publikasi</label>
                    <input type="datetime-local" wire:model="published_at" class="w-full rounded-xl border border-slate-300 px-3 py-2.5">
                </div>
                <label class="flex items-center gap-3 text-sm font-semibold text-slate-700">
                    <input type="checkbox" wire:model="is_published" class="h-4 w-4 rounded"> Publish halaman
                </label>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="submit" wire:loading.attr="disabled" class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white">Simpan</button>
                <button type="button" wire:click="cancel" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700">Batal</button>
            </div>
        </form>
    @endif

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr><th class="px-5 py-3">Halaman</th><th class="px-5 py-3">URL</th><th class="px-5 py-3">Status</th><th class="px-5 py-3 text-right">Aksi</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pages as $page)
                        <tr>
                            <td class="px-5 py-4"><div class="font-semibold text-slate-900">{{ $page->title }}</div><div class="mt-1 max-w-lg truncate text-xs text-slate-500">{{ $page->excerpt }}</div></td>
                            <td class="px-5 py-4"><code class="text-xs">/page/{{ $page->slug }}</code></td>
                            <td class="px-5 py-4"><span class="rounded-full px-2 py-1 text-xs font-semibold {{ $page->is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $page->is_published ? 'Published' : 'Draft' }}</span></td>
                            <td class="px-5 py-4"><div class="flex justify-end gap-3"><button wire:click="edit({{ $page->id }})" class="font-semibold text-blue-600">Edit</button><button wire:click="delete({{ $page->id }})" wire:confirm="Hapus halaman ini? Menu navbar yang mengarah ke halaman ini juga akan kehilangan target." class="font-semibold text-rose-600">Hapus</button></div></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-10 text-center text-slate-500">Belum ada custom page.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
