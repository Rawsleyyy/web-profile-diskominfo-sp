<div class="p-6 md:p-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Manajemen Modul</h1>
        <p class="mt-1 text-sm text-slate-500">Aktifkan hanya fitur yang dibutuhkan oleh instansi. Modul nonaktif otomatis disembunyikan dari konfigurasi publik.</p>
    </div>

    @if (session('module-message'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('module-message') }}</div>
    @endif

    <div class="grid gap-4 lg:grid-cols-2">
        @foreach ($modules as $module)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="font-semibold text-slate-900">{{ $module->name }}</h2>
                            <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $module->is_enabled ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $module->is_enabled ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                        <p class="mt-2 text-sm leading-6 text-slate-500">{{ $module->description }}</p>
                        @if ($module->public_route)
                            <code class="mt-3 inline-block rounded bg-slate-100 px-2 py-1 text-xs text-slate-600">{{ $module->public_route }}</code>
                        @endif
                    </div>
                    <button wire:click="toggle({{ $module->id }})" wire:loading.attr="disabled" class="rounded-xl px-4 py-2 text-sm font-semibold text-white {{ $module->is_enabled ? 'bg-rose-600 hover:bg-rose-700' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                        {{ $module->is_enabled ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>
