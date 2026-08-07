<div class="w-full rounded-[2rem] border border-slate-100 bg-white p-8 shadow-[0_20px_60px_rgba(30,58,138,0.14)] sm:p-10">
    <div class="mb-9 text-center">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-800 text-white shadow-lg shadow-blue-900/20">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </div>
        <h1 class="text-xl font-black uppercase tracking-wider text-blue-900">Admin Portal</h1>
        <p class="mt-1 text-sm font-medium text-slate-400">Dinas Kominfo SP Surakarta</p>
    </div>

    <form wire:submit="login" class="space-y-5">
        <div>
            <label for="admin-email" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-600">Email</label>
            <div class="relative">
                <svg class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H8m8 0l-4-4m4 4l-4 4M4 6h16v12H4z" />
                </svg>
                <input
                    id="admin-email"
                    type="email"
                    wire:model="email"
                    autocomplete="email"
                    autofocus
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pl-12 pr-4 text-sm font-medium outline-none transition focus:border-blue-700 focus:bg-white focus:ring-4 focus:ring-blue-100"
                    placeholder="admin@surakarta.go.id"
                >
            </div>
            @error('email') <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="admin-password" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-600">Password</label>
            <div class="relative">
                <svg class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3 1.343 3 3v3H9v-3c0-1.657 1.343-3 3-3zm0 0V8a4 4 0 118 0v3" />
                </svg>
                <input
                    id="admin-password"
                    type="password"
                    wire:model="password"
                    autocomplete="current-password"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pl-12 pr-4 text-sm font-medium outline-none transition focus:border-blue-700 focus:bg-white focus:ring-4 focus:ring-blue-100"
                    placeholder="Masukkan password"
                >
            </div>
            @error('password') <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
        </div>

        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:target="login"
            class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-800 py-4 text-sm font-black uppercase tracking-widest text-white shadow-lg shadow-blue-900/20 transition hover:bg-blue-900 active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-60"
        >
            <span wire:loading.remove wire:target="login">Masuk ke Sistem</span>
            <span wire:loading wire:target="login">Memverifikasi...</span>
        </button>
    </form>

    <div class="mt-9 border-t border-slate-100 pt-6 text-center">
        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">© 2026 Pemkot Surakarta</p>
    </div>
</div>
