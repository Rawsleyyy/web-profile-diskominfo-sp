<div class="p-6 md:p-8">
    <div class="mb-6"><h1 class="text-2xl font-bold text-slate-900">Pengaturan Beranda</h1><p class="mt-1 text-sm text-slate-500">Atur bagian mana yang tampil dan urutannya. Jika modul terkait dinonaktifkan, section otomatis tidak ditampilkan ke publik.</p></div>
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        @foreach($sections as $section)
            @php $module = $section->module_slug ? ($modules[$section->module_slug] ?? null) : null; @endphp
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 px-5 py-4 last:border-0">
                <div><div class="flex items-center gap-2"><span class="font-semibold text-slate-900">{{ $section->label }}</span><span class="rounded bg-slate-100 px-2 py-0.5 text-xs text-slate-500">{{ $section->section_key }}</span></div><p class="mt-1 text-xs text-slate-500">@if($module) Modul: {{ $module->name }} — {{ $module->is_enabled ? 'aktif' : 'NONAKTIF' }} @else Section inti @endif</p></div>
                <div class="flex items-center gap-2"><button wire:click="moveUp({{ $section->id }})" class="rounded-lg border px-3 py-1.5 text-sm">↑</button><button wire:click="moveDown({{ $section->id }})" class="rounded-lg border px-3 py-1.5 text-sm">↓</button><button wire:click="toggle({{ $section->id }})" class="rounded-xl px-4 py-2 text-sm font-semibold text-white {{ $section->is_enabled ? 'bg-rose-600' : 'bg-emerald-600' }}">{{ $section->is_enabled ? 'Sembunyikan' : 'Tampilkan' }}</button></div>
            </div>
        @endforeach
    </div>
</div>
