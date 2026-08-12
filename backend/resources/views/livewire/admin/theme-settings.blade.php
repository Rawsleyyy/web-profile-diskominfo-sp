<div class="p-6 md:p-8">
    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Theme Builder</h1>
            <p class="mt-1 max-w-3xl text-sm text-slate-500">Atur identitas visual tanpa CSS manual. Tema bekerja sebagai draft dan dipublikasikan bersama konfigurasi website.</p>
        </div>
        <a href="{{ route('admin.publish') }}" class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-2.5 text-sm font-semibold text-blue-700">Preview & Publish Website</a>
    </div>

    @if(session('theme-saved'))<div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('theme-saved') }}</div>@endif

    <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-lg font-bold">Preset Cepat</h2>
        <div class="grid gap-3 md:grid-cols-5">
            @foreach($presets as $key => $preset)
                <button type="button" wire:click="applyPreset('{{ $key }}')" class="rounded-xl border p-3 text-left {{ $preset_key === $key ? 'border-blue-500 bg-blue-50' : 'border-slate-200 hover:border-slate-300' }}">
                    <div class="flex gap-1.5">@foreach(['primary_color_hex','accent_color_hex','secondary_color_hex'] as $c)<span class="h-5 w-5 rounded-full border border-black/5" style="background:{{ $preset[$c] }}"></span>@endforeach</div>
                    <div class="mt-2 text-xs font-bold text-slate-800">{{ $preset['label'] }}</div>
                </button>
            @endforeach
        </div>
    </section>

    <form wire:submit="saveDraft" class="space-y-6">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-5 text-lg font-bold">Warna</h2>
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                @foreach([
                    'primary_color_hex'=>'Primary','secondary_color_hex'=>'Secondary','accent_color_hex'=>'Accent','background_color_hex'=>'Background',
                    'surface_color_hex'=>'Surface / Card','text_primary_hex'=>'Text Primary','text_secondary_hex'=>'Text Secondary'
                ] as $field=>$label)
                    <div><label class="mb-1.5 block text-sm font-semibold">{{ $label }}</label><div class="flex gap-2"><input type="color" wire:model.live="{{ $field }}" class="h-11 w-14 rounded-lg border"><input wire:model.live="{{ $field }}" class="min-w-0 flex-1 rounded-xl border border-slate-300 px-3 py-2.5"></div>@error($field)<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror</div>
                @endforeach
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-5 text-lg font-bold">Tipografi & Komponen</h2>
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                <div><label class="mb-1.5 block text-sm font-semibold">Font Heading</label><select wire:model="font_heading" class="w-full rounded-xl border border-slate-300 px-3 py-2.5">@foreach(['Inter','Poppins','Arial','Georgia'] as $v)<option>{{ $v }}</option>@endforeach</select></div>
                <div><label class="mb-1.5 block text-sm font-semibold">Font Body</label><select wire:model="font_body" class="w-full rounded-xl border border-slate-300 px-3 py-2.5">@foreach(['Inter','Poppins','Arial','Georgia'] as $v)<option>{{ $v }}</option>@endforeach</select></div>
                <div><label class="mb-1.5 block text-sm font-semibold">Sudut / Radius</label><select wire:model="radius_style" class="w-full rounded-xl border border-slate-300 px-3 py-2.5"><option value="square">Kotak</option><option value="small">Sedikit Rounded</option><option value="rounded">Rounded</option><option value="large">Sangat Rounded</option><option value="pill">Pill</option></select></div>
                <div><label class="mb-1.5 block text-sm font-semibold">Button</label><select wire:model="button_style" class="w-full rounded-xl border border-slate-300 px-3 py-2.5"><option value="solid">Solid</option><option value="outline">Outline</option><option value="soft">Soft</option></select></div>
                <div><label class="mb-1.5 block text-sm font-semibold">Card</label><select wire:model="card_style" class="w-full rounded-xl border border-slate-300 px-3 py-2.5"><option value="flat">Flat</option><option value="soft">Soft Shadow</option><option value="bordered">Bordered</option></select></div>
                <div><label class="mb-1.5 block text-sm font-semibold">Lebar Container</label><select wire:model="container_width" class="w-full rounded-xl border border-slate-300 px-3 py-2.5">@foreach(['1100','1200','1280','1400'] as $v)<option value="{{ $v }}">{{ $v }} px</option>@endforeach</select></div>
                <div><label class="mb-1.5 block text-sm font-semibold">Navbar Style</label><select wire:model="navbar_style" class="w-full rounded-xl border border-slate-300 px-3 py-2.5"><option value="solid">Solid</option><option value="gradient">Gradient</option><option value="minimal">Minimal</option></select></div>
                <div><label class="mb-1.5 block text-sm font-semibold">Mode Warna</label><select wire:model="color_mode" class="w-full rounded-xl border border-slate-300 px-3 py-2.5"><option value="auto">Auto</option><option value="light">Light</option><option value="dark">Dark</option></select></div>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 p-6 shadow-sm" style="background:{{ $background_color_hex }};color:{{ $text_primary_hex }}">
            <div class="mx-auto" style="max-width:{{ $container_width }}px;font-family:{{ $font_body }}">
                <h2 class="text-2xl font-bold" style="font-family:{{ $font_heading }}">Preview Tema</h2>
                <p class="mt-2" style="color:{{ $text_secondary_hex }}">Preview aman di dashboard. Publik hanya berubah setelah Publish Website.</p>
                <div class="mt-5 grid gap-4 md:grid-cols-3">
                    <div class="border p-5" style="background:{{ $surface_color_hex }};border-color:{{ $primary_color_hex }}33;border-radius:{{ $radius_style==='square'?'0':($radius_style==='small'?'.35rem':($radius_style==='large'?'1.5rem':($radius_style==='pill'?'999px':'.85rem'))) }}"><strong>Contoh Card</strong><p class="mt-2 text-sm">Komponen mengikuti warna, radius, dan tipografi.</p></div>
                    <div class="md:col-span-2 flex items-center gap-3"><button type="button" class="px-5 py-2.5 font-semibold text-white" style="background:{{ $primary_color_hex }};border-radius:.75rem">Primary Button</button><span class="rounded-lg px-4 py-2 text-sm font-semibold" style="background:{{ $accent_color_hex }}22;color:{{ $accent_color_hex }}">Accent</span></div>
                </div>
            </div>
        </section>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold">Simpan Draft Tema</button>
            <button type="button" wire:click="applyToDraftSite" wire:loading.attr="disabled" class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white">Terapkan ke Draft Website</button>
            <a href="{{ route('admin.publish') }}" class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white">Preview / Publish</a>
        </div>
    </form>
</div>
