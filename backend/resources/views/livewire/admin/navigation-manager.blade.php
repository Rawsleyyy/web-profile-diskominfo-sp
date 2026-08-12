<div class="p-6 md:p-8">
    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Manajemen Navbar</h1>
            <p class="mt-1 max-w-3xl text-sm text-slate-500">
                Susun menu publik tanpa mengubah source React. Gunakan <strong>Dropdown</strong> hanya sebagai wadah submenu,
                <strong>Modul</strong> untuk fitur bawaan, dan <strong>Custom Page</strong> untuk halaman event/kampanye.
            </p>
        </div>
        <button type="button" wire:click="openCreate" wire:loading.attr="disabled" wire:target="openCreate"
            class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50">
            + Tambah Menu Utama
        </button>
    </div>

    @if (session('navigation-message'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('navigation-message') }}
        </div>
    @endif

    <div class="mb-6 grid gap-3 md:grid-cols-5">
        @foreach([
            ['Route Internal', 'Menu menuju route React yang sudah ada, mis. /visi-misi.'],
            ['Modul', 'Menu menuju fitur bawaan yang dapat ON/OFF dari Manajemen Modul.'],
            ['Custom Page', 'Menu menuju halaman yang dibuat admin, cocok untuk event 17 Agustus.'],
            ['Link Eksternal', 'Menu menuju website lain dan dapat dibuka di tab baru.'],
            ['Dropdown', 'Wadah submenu. Tidak mempunyai halaman sendiri.'],
        ] as [$title, $desc])
            <div class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
                <div class="text-xs font-bold text-slate-800">{{ $title }}</div>
                <div class="mt-1 text-[11px] leading-4 text-slate-500">{{ $desc }}</div>
            </div>
        @endforeach
    </div>

    @if ($showForm)
        <form wire:submit="save" class="mb-7 rounded-2xl border border-blue-100 bg-white p-6 shadow-sm" wire:key="navigation-form-{{ $editingId ?? 'new' }}">
            <div class="mb-5 flex items-center justify-between gap-3">
                <div>
                    <h2 class="font-bold text-slate-900">{{ $editingId ? 'Edit Menu' : 'Tambah Menu' }}</h2>
                    <p class="text-xs text-slate-500">Hanya field yang relevan dengan jenis menu yang dipilih akan ditampilkan.</p>
                </div>
                <button type="button" wire:click="cancel" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-500 hover:bg-slate-100">Tutup</button>
            </div>

            <div class="grid gap-5 md:grid-cols-3">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold">Nama Menu</label>
                    <input wire:model="label" class="w-full rounded-xl border border-slate-300 px-3 py-2.5" placeholder="Contoh: 17 Agustus">
                    @error('label')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold">Jenis Menu</label>
                    <select wire:model.live="type" class="w-full rounded-xl border border-slate-300 px-3 py-2.5">
                        <option value="route">Route Internal</option>
                        <option value="module">Modul Bawaan</option>
                        <option value="page">Custom Page</option>
                        <option value="external">Link Eksternal</option>
                        <option value="dropdown">Dropdown / Wadah Submenu</option>
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold">Posisi</label>
                    <select wire:model="parent_id" class="w-full rounded-xl border border-slate-300 px-3 py-2.5">
                        <option value="">Menu Utama</option>
                        @foreach($parentOptions as $parent)
                            <option value="{{ $parent->id }}">Submenu dari: {{ $parent->label }}</option>
                        @endforeach
                    </select>
                    @error('parent_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                @if (in_array($type, ['route','external']))
                    <div class="md:col-span-2">
                        <label class="mb-1.5 block text-sm font-semibold">{{ $type === 'external' ? 'URL Website' : 'Route / URL Internal' }}</label>
                        <input wire:model="url" class="w-full rounded-xl border border-slate-300 px-3 py-2.5"
                            placeholder="{{ $type === 'external' ? 'https://contoh.go.id' : '/visi-misi' }}">
                        <p class="mt-1 text-xs text-slate-400">{{ $type === 'external' ? 'Gunakan URL lengkap https://...' : 'Contoh: /visi-misi, /struktur, /artikel' }}</p>
                        @error('url')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                @elseif ($type === 'module')
                    <div class="md:col-span-2">
                        <label class="mb-1.5 block text-sm font-semibold">Pilih Modul</label>
                        <select wire:model="module_slug" class="w-full rounded-xl border border-slate-300 px-3 py-2.5">
                            <option value="">Pilih Modul</option>
                            @foreach($modules as $module)
                                <option value="{{ $module->slug }}">{{ $module->name }} {{ $module->is_enabled ? '' : '(sedang nonaktif)' }}</option>
                            @endforeach
                        </select>
                        @error('module_slug')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                @elseif ($type === 'page')
                    <div class="md:col-span-2">
                        <label class="mb-1.5 block text-sm font-semibold">Pilih Custom Page</label>
                        <select wire:model="page_id" class="w-full rounded-xl border border-slate-300 px-3 py-2.5">
                            <option value="">Pilih Halaman</option>
                            @foreach($pages as $page)
                                <option value="{{ $page->id }}">{{ $page->title }} {{ $page->is_published ? '' : '(draft)' }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-slate-400">Buat halamannya terlebih dahulu melalui menu Manajemen Halaman.</p>
                        @error('page_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                @else
                    <div class="md:col-span-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        Dropdown tidak membutuhkan URL. Setelah disimpan, gunakan tombol <strong>+ Submenu</strong> untuk mengisi item di dalamnya.
                    </div>
                @endif

                <div>
                    <label class="mb-1.5 block text-sm font-semibold">Urutan</label>
                    <input type="number" min="0" wire:model="sort_order" class="w-full rounded-xl border border-slate-300 px-3 py-2.5">
                </div>

                @if($type !== 'dropdown')
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold">Buka Link</label>
                        <select wire:model="target" class="w-full rounded-xl border border-slate-300 px-3 py-2.5" @disabled($type === 'external')>
                            <option value="_self">Di tab yang sama</option>
                            <option value="_blank">Di tab baru</option>
                        </select>
                    </div>
                @endif

                <label class="mt-7 flex items-center gap-3 text-sm font-semibold">
                    <input type="checkbox" wire:model="is_active" class="h-4 w-4 rounded">
                    Aktif
                </label>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold">Mulai Tampil <span class="font-normal text-slate-400">(opsional)</span></label>
                    <input type="datetime-local" wire:model="visible_from" class="w-full rounded-xl border border-slate-300 px-3 py-2.5">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold">Berakhir <span class="font-normal text-slate-400">(opsional)</span></label>
                    <input type="datetime-local" wire:model="visible_until" class="w-full rounded-xl border border-slate-300 px-3 py-2.5">
                    @error('visible_until')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit" wire:loading.attr="disabled" wire:target="save"
                    class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50">
                    {{ $editingId ? 'Simpan Perubahan' : 'Tambah Menu' }}
                </button>
                <button type="button" wire:click="cancel" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold">Batal</button>
            </div>
        </form>
    @endif

    <div class="mb-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-3 text-xs text-slate-500">Tip: seret kartu menu utama dengan handle ☰ untuk mengubah urutan. Tombol naik/turun tidak diperlukan untuk root menu.</div>
    <div class="space-y-4" x-data="{ dragging: null }">
        @forelse ($rootItems as $item)
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm" wire:key="nav-root-{{ $item->id }}" draggable="true" @dragstart="dragging={{ $item->id }}" @dragover.prevent @drop.prevent="if(dragging && dragging!=={{ $item->id }}) { $wire.moveBefore(dragging, {{ $item->id }}); dragging=null }">
                <div class="flex flex-wrap items-center justify-between gap-4 p-5">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2"><span class="cursor-grab select-none text-slate-400" title="Drag untuk mengurutkan">☰</span>
                            <span class="font-semibold text-slate-900">{{ $item->label }}</span>
                            <span class="rounded bg-slate-100 px-2 py-0.5 text-xs uppercase text-slate-500">{{ $item->type }}</span>
                            <span class="rounded-full px-2 py-0.5 text-xs {{ $item->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                        <div class="mt-1 text-xs text-slate-500">
                            Urutan {{ $item->sort_order }}
                            @if($item->visible_from || $item->visible_until) • Terjadwal @endif
                            @if($item->type === 'route' || $item->type === 'external') • {{ $item->url }} @endif
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button type="button" wire:click="openCreate({{ $item->id }})" wire:loading.attr="disabled" wire:target="openCreate({{ $item->id }})"
                            class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50">
                            + Submenu
                        </button>
                        <button type="button" wire:click="toggle({{ $item->id }})" wire:loading.attr="disabled" wire:target="toggle({{ $item->id }})"
                            class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold hover:bg-slate-50 disabled:opacity-50">
                            {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                        <button type="button" wire:click="edit({{ $item->id }})" wire:loading.attr="disabled" wire:target="edit({{ $item->id }})"
                            class="rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100 disabled:opacity-50">
                            Edit
                        </button>
                        <button type="button" wire:click="delete({{ $item->id }})" wire:confirm="Hapus menu beserta semua submenu?"
                            class="rounded-lg bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100">
                            Hapus
                        </button>
                    </div>
                </div>

                @if($item->children->isNotEmpty())
                    <div class="border-t border-slate-100 bg-slate-50/70 px-5 py-2">
                        @foreach($item->children->sortBy('sort_order') as $child)
                            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 py-3 last:border-0" wire:key="nav-child-{{ $child->id }}">
                                <div class="min-w-0 pl-4">
                                    <span class="mr-2 text-slate-400">↳</span>
                                    <span class="font-medium text-slate-700">{{ $child->label }}</span>
                                    <span class="ml-2 text-xs uppercase text-slate-400">{{ $child->type }}</span>
                                    @if(!$child->is_active)<span class="ml-2 text-xs text-rose-500">Nonaktif</span>@endif
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" wire:click="toggle({{ $child->id }})" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
                                        {{ $child->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                    <button type="button" wire:click="edit({{ $child->id }})" class="text-xs font-semibold text-blue-600 hover:text-blue-800">Edit</button>
                                    <button type="button" wire:click="delete({{ $child->id }})" wire:confirm="Hapus submenu ini?" class="text-xs font-semibold text-rose-600 hover:text-rose-800">Hapus</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-500">
                Belum ada menu navbar. Klik <strong>+ Tambah Menu Utama</strong> untuk mulai menyusun navigasi.
            </div>
        @endforelse
    </div>
</div>
