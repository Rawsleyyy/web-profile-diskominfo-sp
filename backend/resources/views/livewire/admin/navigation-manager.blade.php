<div class="p-6 md:p-8">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Manajemen Navbar</h1>
            <p class="mt-1 text-sm text-slate-500">Tambah menu, dropdown, custom page, modul, link eksternal, serta jadwal tampil otomatis.</p>
        </div>
        <button wire:click="openCreate" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white">+ Tambah Menu Utama</button>
    </div>

    @if (session('navigation-message'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('navigation-message') }}</div>
    @endif

    @if ($showForm)
        <form wire:submit="save" class="mb-7 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="grid gap-5 md:grid-cols-3">
                <div><label class="mb-1.5 block text-sm font-semibold">Nama Menu</label><input wire:model="label" class="w-full rounded-xl border border-slate-300 px-3 py-2.5">@error('label')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror</div>
                <div><label class="mb-1.5 block text-sm font-semibold">Jenis</label><select wire:model.live="type" class="w-full rounded-xl border border-slate-300 px-3 py-2.5"><option value="route">Route Internal</option><option value="module">Modul</option><option value="page">Custom Page</option><option value="external">Link Eksternal</option><option value="dropdown">Dropdown</option></select></div>
                <div><label class="mb-1.5 block text-sm font-semibold">Parent Menu</label><select wire:model="parent_id" class="w-full rounded-xl border border-slate-300 px-3 py-2.5"><option value="">Menu Utama</option>@foreach($parentOptions as $parent)<option value="{{ $parent->id }}">{{ $parent->label }}</option>@endforeach</select>@error('parent_id')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror</div>

                @if (in_array($type, ['route','external']))
                    <div class="md:col-span-2"><label class="mb-1.5 block text-sm font-semibold">URL</label><input wire:model="url" class="w-full rounded-xl border border-slate-300 px-3 py-2.5" placeholder="{{ $type === 'external' ? 'https://...' : '/visi-misi' }}">@error('url')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror</div>
                @elseif ($type === 'module')
                    <div class="md:col-span-2"><label class="mb-1.5 block text-sm font-semibold">Modul</label><select wire:model="module_slug" class="w-full rounded-xl border border-slate-300 px-3 py-2.5"><option value="">Pilih Modul</option>@foreach($modules as $module)<option value="{{ $module->slug }}">{{ $module->name }} {{ $module->is_enabled ? '' : '(nonaktif)' }}</option>@endforeach</select>@error('module_slug')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror</div>
                @elseif ($type === 'page')
                    <div class="md:col-span-2"><label class="mb-1.5 block text-sm font-semibold">Custom Page</label><select wire:model="page_id" class="w-full rounded-xl border border-slate-300 px-3 py-2.5"><option value="">Pilih Halaman</option>@foreach($pages as $page)<option value="{{ $page->id }}">{{ $page->title }} {{ $page->is_published ? '' : '(draft)' }}</option>@endforeach</select>@error('page_id')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror</div>
                @endif

                <div><label class="mb-1.5 block text-sm font-semibold">Urutan</label><input type="number" min="0" wire:model="sort_order" class="w-full rounded-xl border border-slate-300 px-3 py-2.5"></div>
                <div><label class="mb-1.5 block text-sm font-semibold">Target</label><select wire:model="target" class="w-full rounded-xl border border-slate-300 px-3 py-2.5"><option value="_self">Tab yang sama</option><option value="_blank">Tab baru</option></select></div>
                <label class="mt-7 flex items-center gap-3 text-sm font-semibold"><input type="checkbox" wire:model="is_active" class="h-4 w-4 rounded"> Aktif</label>
                <div><label class="mb-1.5 block text-sm font-semibold">Tampil Mulai</label><input type="datetime-local" wire:model="visible_from" class="w-full rounded-xl border border-slate-300 px-3 py-2.5"></div>
                <div><label class="mb-1.5 block text-sm font-semibold">Tampil Sampai</label><input type="datetime-local" wire:model="visible_until" class="w-full rounded-xl border border-slate-300 px-3 py-2.5">@error('visible_until')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror</div>
            </div>
            <div class="mt-6 flex gap-3"><button type="submit" class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white">Simpan</button><button type="button" wire:click="cancel" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold">Batal</button></div>
        </form>
    @endif

    <div class="space-y-4">
        @foreach ($rootItems as $item)
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3 p-5">
                    <div><div class="flex items-center gap-2"><span class="font-semibold text-slate-900">{{ $item->label }}</span><span class="rounded bg-slate-100 px-2 py-0.5 text-xs uppercase text-slate-500">{{ $item->type }}</span><span class="rounded-full px-2 py-0.5 text-xs {{ $item->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</span></div><div class="mt-1 text-xs text-slate-500">Urutan {{ $item->sort_order }} @if($item->visible_from || $item->visible_until) • Terjadwal @endif</div></div>
                    <div class="flex flex-wrap gap-2"><button wire:click="openCreate({{ $item->id }})" class="rounded-lg border px-3 py-1.5 text-xs font-semibold text-slate-700">+ Submenu</button><button wire:click="toggle({{ $item->id }})" class="rounded-lg border px-3 py-1.5 text-xs font-semibold">{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button><button wire:click="edit({{ $item->id }})" class="rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">Edit</button><button wire:click="delete({{ $item->id }})" wire:confirm="Hapus menu beserta semua submenu?" class="rounded-lg bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700">Hapus</button></div>
                </div>
                @if($item->children->isNotEmpty())
                    <div class="border-t border-slate-100 bg-slate-50/70 px-5 py-3">
                        @foreach($item->children->sortBy('sort_order') as $child)
                            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 py-3 last:border-0"><div class="pl-4"><span class="mr-2 text-slate-400">↳</span><span class="font-medium text-slate-700">{{ $child->label }}</span><span class="ml-2 text-xs uppercase text-slate-400">{{ $child->type }}</span></div><div class="flex gap-2"><button wire:click="edit({{ $child->id }})" class="text-xs font-semibold text-blue-600">Edit</button><button wire:click="delete({{ $child->id }})" wire:confirm="Hapus submenu ini?" class="text-xs font-semibold text-rose-600">Hapus</button></div></div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
