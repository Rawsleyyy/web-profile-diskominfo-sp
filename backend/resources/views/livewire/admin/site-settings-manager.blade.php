<div class="p-6 md:p-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Identitas Website</h1>
        <p class="mt-1 text-sm text-slate-500">Ubah nama instansi, logo, kontak, sosial media, dan footer tanpa mengubah source code React.</p>
    </div>

    @if(session('site-settings-message'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('site-settings-message') }}</div>
    @endif

    <form wire:submit="save" class="space-y-6">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-5 text-lg font-bold">Identitas Instansi</h2>
            <div class="grid gap-5 md:grid-cols-2">
                <div><label class="mb-1 block text-sm font-semibold">Nama Instansi</label><input wire:model="site_name" class="w-full rounded-xl border border-slate-300 px-3 py-2.5">@error('site_name')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror</div>
                <div><label class="mb-1 block text-sm font-semibold">Nama Singkat</label><input wire:model="site_short_name" class="w-full rounded-xl border border-slate-300 px-3 py-2.5"></div>
                <div class="md:col-span-2"><label class="mb-1 block text-sm font-semibold">Deskripsi</label><textarea wire:model="site_description" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2.5"></textarea></div>
                <div><label class="mb-1 block text-sm font-semibold">Logo Baru</label><input type="file" wire:model="logo" accept="image/*" class="block w-full text-sm">@if($existingLogo)<p class="mt-1 text-xs text-slate-500">Saat ini: {{ $existingLogo }}</p>@endif</div>
                <div><label class="mb-1 block text-sm font-semibold">Favicon Baru</label><input type="file" wire:model="favicon" accept="image/*" class="block w-full text-sm">@if($existingFavicon)<p class="mt-1 text-xs text-slate-500">Saat ini: {{ $existingFavicon }}</p>@endif</div>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-5 text-lg font-bold">Kontak & Sosial Media</h2>
            <div class="grid gap-5 md:grid-cols-2">
                <div><label class="mb-1 block text-sm font-semibold">Telepon</label><input wire:model="phone" class="w-full rounded-xl border border-slate-300 px-3 py-2.5"></div>
                <div><label class="mb-1 block text-sm font-semibold">Email</label><input wire:model="email" class="w-full rounded-xl border border-slate-300 px-3 py-2.5"></div>
                <div class="md:col-span-2"><label class="mb-1 block text-sm font-semibold">Alamat</label><textarea wire:model="address" rows="2" class="w-full rounded-xl border border-slate-300 px-3 py-2.5"></textarea></div>
                @foreach(['instagram_url'=>'Instagram','facebook_url'=>'Facebook','youtube_url'=>'YouTube','tiktok_url'=>'TikTok'] as $field=>$label)
                    <div><label class="mb-1 block text-sm font-semibold">{{ $label }} URL</label><input wire:model="{{ $field }}" class="w-full rounded-xl border border-slate-300 px-3 py-2.5" placeholder="https://..."></div>
                @endforeach
                <div class="md:col-span-2"><label class="mb-1 block text-sm font-semibold">Teks Footer</label><textarea wire:model="footer_text" rows="2" class="w-full rounded-xl border border-slate-300 px-3 py-2.5"></textarea></div>
            </div>
        </section>

        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
            Event sementara seperti 17 Agustus sekarang dibuat melalui <strong>Manajemen Halaman</strong> lalu dimasukkan ke <strong>Manajemen Navbar</strong>. Tidak ada banner tambahan yang dipaksakan di atas navbar.
        </div>

        <button type="submit" wire:loading.attr="disabled" wire:target="save" class="rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50">
            Simpan Pengaturan Website
        </button>
    </form>
</div>
