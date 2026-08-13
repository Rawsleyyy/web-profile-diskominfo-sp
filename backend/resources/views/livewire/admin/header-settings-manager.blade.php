<div class="p-6 md:p-8">
    <div class="mx-auto max-w-6xl">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-900">Kelola Header</h1>
            <p class="mt-1 text-sm text-slate-500">Atur tampilan area header/navbar. Isi menu tetap dikelola melalui <strong>Manajemen Navbar</strong>.</p>
        </div>

        @if(session('header-settings-message'))
            <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('header-settings-message') }}
            </div>
        @endif

        @php
            $previewBackground = match($header_style) {
                'solid' => $header_custom_color_start,
                'custom_gradient' => "linear-gradient({$header_gradient_angle}deg, {$header_custom_color_start} 0%, {$header_custom_color_end} 100%)",
                default => "linear-gradient(135deg, {$themePrimary} 0%, {$themePrimary} 52%, {$themeAccent} 130%)",
            };

            $previewLogo = null;
            if ($header_logo) {
                try {
                    $previewLogo = $header_logo->temporaryUrl();
                } catch (\Throwable $e) {
                    $previewLogo = null;
                }
            } elseif ($existingLogo) {
                $previewLogo = \Illuminate\Support\Facades\Storage::disk('public')->url($existingLogo);
            }
        @endphp

        <form wire:submit="save" class="space-y-6">
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-6 py-4">
                    <h2 class="font-bold text-slate-900">Live Preview</h2>
                    <p class="mt-1 text-xs text-slate-500">Preview ini mendekati tampilan desktop. Menu publik tetap mengikuti Manajemen Navbar.</p>
                </div>

                <div class="bg-slate-100 p-5">
                    @if($header_topbar_enabled)
                        <div class="mx-auto max-w-5xl rounded-t-xl px-5 py-2 text-[10px] font-semibold text-white/80" style="background: {{ $header_topbar_color }};">
                            <div class="flex items-center justify-between">
                                <span>WAKTU & TANGGAL</span>
                                <span>TELEPON & EMAIL</span>
                            </div>
                        </div>
                    @endif

                    <div class="mx-auto flex max-w-5xl items-center justify-between gap-4 px-5 py-3 text-white {{ $header_width_mode === 'boxed' ? 'rounded-2xl' : ($header_topbar_enabled ? 'rounded-b-xl' : 'rounded-xl') }}" style="background: {{ $previewBackground }}; {{ $header_shadow_enabled ? 'box-shadow: 0 10px 30px rgba(15,23,42,.18);' : '' }}">
                        <div class="flex min-w-0 items-center gap-3">
                            @if($previewLogo)
                                <img src="{{ $previewLogo }}" alt="Logo Header" class="max-w-48 object-contain" style="height: {{ $header_logo_height }}px;">
                            @else
                                <div class="flex items-center justify-center rounded-lg border border-white/25 bg-white/10 px-3 text-xs font-bold" style="height: {{ $header_logo_height }}px;">LOGO</div>
                            @endif

                            @if($header_show_site_name)
                                <span class="truncate text-sm font-extrabold">{{ $site_short_name }}</span>
                            @endif
                        </div>

                        <div class="hidden items-center gap-5 text-[10px] font-bold md:flex">
                            <span>HOME</span><span>PPID</span><span>PROFIL</span><span>INFORMASI</span>
                        </div>

                        <div class="flex items-center gap-2">
                            @if($header_search_enabled)<span class="flex h-8 w-8 items-center justify-center rounded-xl border border-white/20 bg-white/10">⌕</span>@endif
                            @if($header_dark_toggle_enabled)<span class="flex h-8 w-8 items-center justify-center rounded-xl border border-white/20 bg-white/10">☾</span>@endif
                        </div>
                    </div>
                </div>
            </section>

            <div class="grid gap-6 lg:grid-cols-2">
                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="mb-5 text-lg font-bold text-slate-900">Branding Header</h2>

                    <div class="space-y-5">
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-700">Logo Header Baru</label>
                            <input type="file" wire:model="header_logo" accept="image/*" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm">
                            <p class="mt-1 text-xs text-slate-500">Logo ini tersinkron dengan logo pada Identitas Website. Maks. 4 MB.</p>
                            @error('header_logo')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>

                        <label class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 px-4 py-3">
                            <span><strong class="block text-sm text-slate-800">Tampilkan nama singkat</strong><small class="text-slate-500">Tampilkan {{ $site_short_name }} di samping logo.</small></span>
                            <input type="checkbox" wire:model.live="header_show_site_name" class="h-5 w-5 rounded border-slate-300 text-blue-600">
                        </label>

                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <label class="text-sm font-semibold text-slate-700">Tinggi Logo</label>
                                <span class="rounded-lg bg-slate-100 px-2 py-1 text-xs font-bold text-slate-600">{{ $header_logo_height }} px</span>
                            </div>
                            <input type="range" min="28" max="72" step="1" wire:model.live="header_logo_height" class="w-full">
                            @error('header_logo_height')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">Lebar Header</label>
                            <select wire:model.live="header_width_mode" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                                <option value="adaptive">Adaptif — penuh di atas, boxed saat scroll</option>
                                <option value="full">Selalu Full Width</option>
                                <option value="boxed">Selalu Boxed / Floating</option>
                            </select>
                            @error('header_width_mode')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="mb-5 text-lg font-bold text-slate-900">Warna & Gaya</h2>

                    <div class="space-y-5">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">Gaya Warna Header</label>
                            <select wire:model.live="header_style" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                                <option value="theme_gradient">Gradient mengikuti Theme Settings</option>
                                <option value="custom_gradient">Gradient Custom</option>
                                <option value="solid">Warna Solid</option>
                            </select>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-semibold text-slate-700">Warna Awal / Solid</label>
                                <div class="flex gap-2">
                                    <input type="color" wire:model.live="header_custom_color_start" class="h-11 w-14 rounded-lg border border-slate-300">
                                    <input type="text" wire:model.live="header_custom_color_start" maxlength="7" class="min-w-0 flex-1 rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                </div>
                                @error('header_custom_color_start')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-semibold text-slate-700">Warna Akhir Gradient</label>
                                <div class="flex gap-2">
                                    <input type="color" wire:model.live="header_custom_color_end" class="h-11 w-14 rounded-lg border border-slate-300">
                                    <input type="text" wire:model.live="header_custom_color_end" maxlength="7" class="min-w-0 flex-1 rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                </div>
                                @error('header_custom_color_end')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <label class="text-sm font-semibold text-slate-700">Sudut Gradient</label>
                                <span class="rounded-lg bg-slate-100 px-2 py-1 text-xs font-bold text-slate-600">{{ $header_gradient_angle }}°</span>
                            </div>
                            <input type="range" min="0" max="360" step="5" wire:model.live="header_gradient_angle" class="w-full" @disabled($header_style !== 'custom_gradient')>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-700">Warna Top Bar</label>
                            <div class="flex gap-2">
                                <input type="color" wire:model.live="header_topbar_color" class="h-11 w-14 rounded-lg border border-slate-300">
                                <input type="text" wire:model.live="header_topbar_color" maxlength="7" class="min-w-0 flex-1 rounded-xl border border-slate-300 px-3 py-2 text-sm">
                            </div>
                            @error('header_topbar_color')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>
            </div>

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="mb-5 text-lg font-bold text-slate-900">Elemen Header</h2>
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                    @foreach([
                        ['header_topbar_enabled', 'Top Bar', 'Waktu, telepon, dan email.'],
                        ['header_search_enabled', 'Tombol Pencarian', 'Ikon pencarian di sisi kanan.'],
                        ['header_dark_toggle_enabled', 'Dark Mode', 'Tombol mode terang/gelap.'],
                        ['header_glass_enabled', 'Glass Effect', 'Blur dan highlight lembut.'],
                        ['header_shadow_enabled', 'Shadow', 'Bayangan header agar lebih berdimensi.'],
                    ] as [$field, $label, $description])
                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-4 hover:bg-slate-50">
                            <input type="checkbox" wire:model.live="{{ $field }}" class="mt-0.5 h-5 w-5 rounded border-slate-300 text-blue-600">
                            <span><strong class="block text-sm text-slate-800">{{ $label }}</strong><small class="mt-1 block leading-5 text-slate-500">{{ $description }}</small></span>
                        </label>
                    @endforeach
                </div>
            </section>

            <div class="flex flex-wrap items-center gap-3">
                <button type="submit" wire:loading.attr="disabled" wire:target="save" class="rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 disabled:opacity-50">
                    <span wire:loading.remove wire:target="save">Simpan Pengaturan Header</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
                <span class="text-xs text-slate-500">Setelah disimpan, refresh website publik. Konfigurasi header dibaca dari API site-config.</span>
            </div>
        </form>
    </div>
</div>
