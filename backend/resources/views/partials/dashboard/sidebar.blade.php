@php
    $hasModuleTable = \Illuminate\Support\Facades\Schema::hasTable('site_modules');
    $moduleEnabled = fn (string $slug) => ! $hasModuleTable || \App\Models\SiteModule::isEnabled($slug);
    $siteShortName = 'Diskominfo SP';
    if (\Illuminate\Support\Facades\Schema::hasTable('site_settings')) {
        $siteShortName = \App\Models\SiteSetting::query()->value('site_short_name') ?: $siteShortName;
    }
@endphp

<aside class="fixed inset-y-0 left-0 z-40 flex h-screen w-64 flex-col bg-slate-900 text-white">
    <div class="flex h-20 items-center border-b border-slate-700 px-6">
        <h1 class="truncate text-xl font-bold">{{ $siteShortName }}</h1>
    </div>

    <div class="px-4 pb-2 pt-5">
        <div class="flex items-center gap-3 rounded-lg bg-slate-800 px-3 py-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-600 text-sm font-semibold text-white">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <p class="truncate text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                <p class="text-xs text-slate-400">{{ auth()->user()->role->name ?? 'Admin' }}</p>
            </div>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto p-4 pr-3">
        <p class="mb-3 px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">Main</p>
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
            <span>📊</span><span>Dashboard</span>
        </a>

        <p class="mb-2 mt-6 px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">Website Builder</p>
        <a href="{{ route('admin.site-settings') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.site-settings') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}"><span>🏢</span><span>Identitas Website</span></a>
        <a href="{{ route('admin.header-settings') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.header-settings') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}"><span>🧱</span><span>Kelola Header</span></a>
        <a href="{{ route('admin.hero-slides') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.hero-slides') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}"><span>🖼️</span><span>Kelola Banner</span></a>
        <a href="{{ route('admin.theme-settings') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.theme-settings') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}"><span>🎨</span><span>Theme Settings</span></a>
        <a href="{{ route('admin.modules') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.modules') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}"><span>🧩</span><span>Manajemen Modul</span></a>
        <a href="{{ route('admin.pages') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.pages') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}"><span>📄</span><span>Manajemen Halaman</span></a>
        <a href="{{ route('admin.navigation') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.navigation') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}"><span>🧭</span><span>Manajemen Navbar</span></a>
        <a href="{{ route('admin.homepage') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.homepage') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}"><span>🏠</span><span>Pengaturan Beranda</span></a>

        <p class="mb-2 mt-6 px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">Konten & Modul</p>
        @if ($moduleEnabled('faq'))
            <a href="{{ route('admin.faq') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.faq') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}"><span>💬</span><span>FAQ & MONIKS</span></a>
        @endif
        @if ($moduleEnabled('berita'))
            <a href="{{ route('admin.berita') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.berita') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}"><span>📰</span><span>Kelola Berita</span></a>
        @endif
        @if ($moduleEnabled('profil') || $moduleEnabled('struktur'))
            <a href="{{ route('admin.pejabat') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.pejabat') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}"><span>👤</span><span>Struktur Organisasi</span></a>
        @endif
        @if ($moduleEnabled('layanan'))
            <a href="{{ route('admin.layanan') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.layanan') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}"><span>🛠</span><span>Kelola Layanan</span></a>
        @endif
        @if ($moduleEnabled('ppid'))
            <a href="{{ route('admin.dokumen') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.dokumen') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}"><span>📑</span><span>Dokumen PPID</span></a>
            <a href="{{ route('admin.dokumen-publik') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.dokumen-publik') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}"><span>📂</span><span>Data Publik</span></a>
        @endif
        @if ($moduleEnabled('podcast'))
            <a href="{{ route('admin.podcast') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.podcast') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}"><span>🎙</span><span>Kelola Podcast</span></a>
        @endif
        @if ($moduleEnabled('skm'))
            <a href="{{ route('admin.skm') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.skm') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}"><span>📋</span><span>Kelola Data SKM</span></a>
        @endif
        @if ($moduleEnabled('articles'))
            <a href="{{ route('admin.articles') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.articles') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}"><span>✍️</span><span>Kelola Artikel</span></a>
        @endif
        @if ($moduleEnabled('awards'))
            <a href="{{ route('admin.awards') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.awards') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}"><span>🏆</span><span>Kelola Penghargaan</span></a>
        @endif

        @if (optional(auth()->user()->role)->name === 'Super Admin')
            <p class="mb-2 mt-6 px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">Keamanan</p>
            <a href="{{ route('admin.log-activity') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.log-activity') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}"><span>📋</span><span>Log Activity</span></a>
            <a href="{{ route('admin.users') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.users') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}"><span>👥</span><span>Kelola Akun</span></a>
        @endif
    </nav>

    <div class="border-t border-slate-700 p-4">
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Logout
            </button>
        </form>
    </div>
</aside>
