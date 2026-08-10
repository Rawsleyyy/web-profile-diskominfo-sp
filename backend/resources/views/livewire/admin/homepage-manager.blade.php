<div class="p-6 md:p-8">
    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Pengaturan Beranda</h1>
            <p class="mt-1 max-w-3xl text-sm text-slate-500">
                Atur section bawaan dan tambahkan bagian kustom tanpa mengubah source React. Section bawaan tetap aman; section kustom dapat diedit, diduplikasi, atau dihapus.
            </p>
        </div>

        <button
            type="button"
            wire:click="openCreate"
            wire:loading.attr="disabled"
            wire:target="openCreate"
            class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 disabled:opacity-60"
        >
            + Tambah Bagian Beranda
        </button>
    </div>

    @if (session('homepage-message'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('homepage-message') }}
        </div>
    @endif

    @if ($showForm)
        <form wire:submit="save" class="mb-7 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5">
                <h2 class="text-lg font-bold text-slate-900">{{ $editingId ? 'Edit Bagian Beranda' : 'Tambah Bagian Beranda' }}</h2>
                <p class="mt-1 text-sm text-slate-500">Pilih jenis section. Hanya field yang relevan dengan jenis tersebut yang akan ditampilkan.</p>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">Nama di Dashboard</label>
                    <input wire:model="label" class="w-full rounded-xl border border-slate-300 px-3 py-2.5" placeholder="Contoh: Semarak 17 Agustus">
                    <p class="mt-1 text-xs text-slate-500">Nama ini hanya membantu admin mengenali section.</p>
                    @error('label') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">Jenis Bagian</label>
                    <select wire:model.live="section_type" class="w-full rounded-xl border border-slate-300 px-3 py-2.5">
                        <option value="custom_content">Konten Kustom</option>
                        <option value="page_highlight">Sorotan Custom Page</option>
                        <option value="cta">Call To Action / Tombol Utama</option>
                        <option value="video">Embed Video</option>
                        <option value="spacer">Pemisah / Spacer</option>
                    </select>
                    <p class="mt-1 text-xs text-slate-500">
                        Konten Kustom cocok untuk program/event; Sorotan Page mengambil konten dari Manajemen Halaman.
                    </p>
                </div>

                @if (in_array($section_type, ['custom_content', 'page_highlight', 'cta'], true))
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Judul Publik</label>
                        <input wire:model="public_title" class="w-full rounded-xl border border-slate-300 px-3 py-2.5" placeholder="Judul yang tampil di website">
                        @error('public_title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Subjudul / Ringkasan</label>
                        <input wire:model="subtitle" class="w-full rounded-xl border border-slate-300 px-3 py-2.5" placeholder="Teks pendukung singkat">
                        @error('subtitle') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                @endif

                @if ($section_type === 'custom_content')
                    <div class="md:col-span-2">
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Konten</label>
                        <textarea wire:model="content" rows="7" class="w-full rounded-xl border border-slate-300 px-3 py-2.5" placeholder="Tulis isi section. Baris baru akan dipertahankan."></textarea>
                        @error('content') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Gambar (opsional)</label>
                        <input type="file" wire:model="image" accept="image/*" class="block w-full text-sm text-slate-600">
                        @if ($existingImage)
                            <p class="mt-1 text-xs text-slate-500">Gambar saat ini: {{ $existingImage }}</p>
                        @endif
                        <div wire:loading wire:target="image" class="mt-1 text-xs font-semibold text-blue-600">Mengunggah gambar...</div>
                        @error('image') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Layout</label>
                        <select wire:model="layout" class="w-full rounded-xl border border-slate-300 px-3 py-2.5">
                            <option value="image_right">Teks kiri — gambar kanan</option>
                            <option value="image_left">Gambar kiri — teks kanan</option>
                            <option value="centered">Konten di tengah</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Teks Tombol (opsional)</label>
                        <input wire:model="button_text" class="w-full rounded-xl border border-slate-300 px-3 py-2.5" placeholder="Lihat Selengkapnya">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Link Tombol (opsional)</label>
                        <input wire:model="button_url" class="w-full rounded-xl border border-slate-300 px-3 py-2.5" placeholder="/page/semarak-hut-ri atau https://...">
                    </div>
                @endif

                @if ($section_type === 'page_highlight')
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Custom Page</label>
                        <select wire:model="source_page_id" class="w-full rounded-xl border border-slate-300 px-3 py-2.5">
                            <option value="">-- Pilih halaman --</option>
                            @foreach ($pages as $page)
                                <option value="{{ $page->id }}">{{ $page->title }}{{ $page->is_published ? '' : ' (Draft)' }}</option>
                            @endforeach
                        </select>
                        @error('source_page_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Layout</label>
                        <select wire:model="layout" class="w-full rounded-xl border border-slate-300 px-3 py-2.5">
                            <option value="image_left">Gambar kiri — teks kanan</option>
                            <option value="image_right">Teks kiri — gambar kanan</option>
                            <option value="centered">Kartu di tengah</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Teks Tombol</label>
                        <input wire:model="button_text" class="w-full rounded-xl border border-slate-300 px-3 py-2.5" placeholder="Lihat Selengkapnya">
                    </div>

                    <div class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-xs leading-5 text-blue-700">
                        Banner, ringkasan, dan URL diambil otomatis dari Custom Page. Jika halaman masih Draft atau belum masuk jadwal publikasi, section tidak ditampilkan ke publik.
                    </div>
                @endif

                @if ($section_type === 'cta')
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Teks Tombol</label>
                        <input wire:model="button_text" class="w-full rounded-xl border border-slate-300 px-3 py-2.5" placeholder="Daftar Sekarang">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Link Tombol</label>
                        <input wire:model="button_url" class="w-full rounded-xl border border-slate-300 px-3 py-2.5" placeholder="/page/program atau https://...">
                    </div>
                @endif

                @if ($section_type === 'video')
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Judul (opsional)</label>
                        <input wire:model="public_title" class="w-full rounded-xl border border-slate-300 px-3 py-2.5" placeholder="Video Profil Instansi">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">URL YouTube / Vimeo</label>
                        <input wire:model="video_url" class="w-full rounded-xl border border-slate-300 px-3 py-2.5" placeholder="https://www.youtube.com/watch?v=...">
                        @error('video_url') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Subjudul (opsional)</label>
                        <input wire:model="subtitle" class="w-full rounded-xl border border-slate-300 px-3 py-2.5" placeholder="Deskripsi singkat video">
                    </div>
                @endif

                @if ($section_type === 'spacer')
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Ukuran Ruang</label>
                        <select wire:model="spacer_size" class="w-full rounded-xl border border-slate-300 px-3 py-2.5">
                            <option value="sm">Kecil</option>
                            <option value="md">Sedang</option>
                            <option value="lg">Besar</option>
                            <option value="xl">Sangat besar</option>
                        </select>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs leading-5 text-slate-600">
                        Spacer hanya menambah jarak vertikal antar-section. Gunakan seperlunya agar halaman tetap konsisten.
                    </div>
                @endif

                <label class="flex items-center gap-3 text-sm font-semibold text-slate-700">
                    <input type="checkbox" wire:model="is_enabled" class="h-4 w-4 rounded">
                    Langsung tampilkan section setelah disimpan
                </label>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <button type="submit" wire:loading.attr="disabled" class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-60">Simpan Bagian</button>
                <button type="button" wire:click="cancel" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
            </div>
        </form>
    @endif

    <div class="space-y-3">
        @foreach($sections as $section)
            @php
                $module = $section->module_slug ? ($modules[$section->module_slug] ?? null) : null;
                $isBuiltin = $section->section_type === 'builtin';
                $typeLabels = [
                    'builtin' => 'Section Bawaan',
                    'custom_content' => 'Konten Kustom',
                    'page_highlight' => 'Sorotan Halaman',
                    'cta' => 'CTA',
                    'video' => 'Video',
                    'spacer' => 'Spacer',
                ];
            @endphp

            <div wire:key="homepage-section-{{ $section->id }}" class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-semibold text-slate-900">{{ $section->label }}</span>
                            <span class="rounded bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-500">{{ $typeLabels[$section->section_type] ?? $section->section_type }}</span>
                            @if ($isBuiltin)
                                <span class="rounded bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-600">{{ $section->section_key }}</span>
                            @endif
                            <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $section->is_enabled ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $section->is_enabled ? 'Tampil' : 'Tersembunyi' }}
                            </span>
                        </div>

                        <p class="mt-1 text-xs text-slate-500">
                            @if($module)
                                Modul: {{ $module->name }} — {{ $module->is_enabled ? 'aktif' : 'NONAKTIF' }}
                            @elseif($section->section_type === 'page_highlight')
                                Sumber: Custom Page #{{ $section->source_id }}
                            @elseif($section->section_type === 'custom_content')
                                Layout: {{ $section->layout }}
                            @elseif($section->section_type === 'video')
                                Embed video publik
                            @elseif($section->section_type === 'cta')
                                Call To Action
                            @elseif($section->section_type === 'spacer')
                                Jarak antar-section
                            @else
                                Section inti
                            @endif
                            · Urutan {{ $section->sort_order }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" wire:click="moveUp({{ $section->id }})" wire:loading.attr="disabled" wire:target="moveUp({{ $section->id }})" class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm hover:bg-slate-50">↑</button>
                        <button type="button" wire:click="moveDown({{ $section->id }})" wire:loading.attr="disabled" wire:target="moveDown({{ $section->id }})" class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm hover:bg-slate-50">↓</button>

                        <button type="button" wire:click="toggle({{ $section->id }})" wire:loading.attr="disabled" wire:target="toggle({{ $section->id }})" class="rounded-xl px-4 py-2 text-sm font-semibold text-white {{ $section->is_enabled ? 'bg-rose-600 hover:bg-rose-700' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                            {{ $section->is_enabled ? 'Sembunyikan' : 'Tampilkan' }}
                        </button>

                        @if (! $isBuiltin)
                            <button type="button" wire:click="edit({{ $section->id }})" wire:loading.attr="disabled" wire:target="edit({{ $section->id }})" class="rounded-xl bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-600 hover:bg-blue-100">Edit</button>
                            <button type="button" wire:click="duplicate({{ $section->id }})" wire:loading.attr="disabled" wire:target="duplicate({{ $section->id }})" class="rounded-xl bg-violet-50 px-4 py-2 text-sm font-semibold text-violet-600 hover:bg-violet-100">Duplikat</button>
                            <button type="button" wire:click="delete({{ $section->id }})" wire:confirm="Hapus section '{{ $section->label }}' dari beranda?" wire:loading.attr="disabled" wire:target="delete({{ $section->id }})" class="rounded-xl bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-600 hover:bg-rose-100">Hapus</button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm leading-6 text-amber-800">
        <strong>Catatan:</strong> section bawaan seperti Hero, Berita, Layanan, SKM, dan lainnya tidak dihapus dari sistem. Anda cukup menyembunyikannya. Untuk kebutuhan instansi/event baru gunakan <strong>Tambah Bagian Beranda</strong>.
    </div>
</div>
