<aside class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-900 text-white flex flex-col h-screen">
    <div class="flex h-20 items-center border-b border-slate-700 px-6">
        <h1 class="text-xl font-bold">Diskominfo SP</h1>
    </div>

    <div class="px-4 pt-5 pb-2">
        <div class="flex items-center gap-3 rounded-lg bg-slate-800 px-3 py-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-600 text-sm font-semibold text-white">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div>
                <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                <p class="text-xs text-slate-400">{{ auth()->user()->role->name ?? 'Admin' }}</p>
            </div>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto p-4 pr-3">
        <p class="mb-3 px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">Main</p>

        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
            <span>&#128202;</span>
            <span>Dashboard</span>
        </a>
        @if (optional(auth()->user()->role)->name === 'Super Admin')
        <a href="{{ route('admin.log-activity') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.log-activity') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
            <span>&#128203;</span>
            <span>Log Activity</span>
        </a>
        @endif

        <a href="{{ route('admin.theme-settings') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.theme-settings') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
            <span>&#127912;</span>
            <span>Theme Settings</span>
        </a>

        <a href="{{ route('admin.berita') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.berita') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
            <span>&#128240;</span>
            <span>Kelola Berita</span>
        </a>

        <a href="{{ route('admin.pejabat') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.pejabat') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
            <span>&#128100;</span>
            <span>Struktur Organisasi</span>
        </a>

        <a href="{{ route('admin.layanan') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.layanan') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
            <span>&#128295;</span>
            <span>Kelola Layanan</span>
        </a>

        <a href="{{ route('admin.dokumen') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.dokumen') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
            <span>&#128196;</span>
            <span>Kelola Dokumen PPID</span>
        </a>

        <a href="{{ route('admin.dokumen-publik') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('admin.dokumen-publik') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white' }}">
    <!-- Icon Dokumen/File -->
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
    </svg>
    <span>Kelola Data Publik</span>
    </a>

<!-- 2. TAMBAHKAN MENU INI: Kelola Podcast / Kominpod -->
    <a href="{{ route('admin.podcast') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('admin.podcast') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white' }}">
    <!-- Icon Podcast/Microphone -->
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
    </svg>
    <span>Kelola Podcast</span>
    </a>

        <a href="{{ route('admin.skm') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.skm') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
            <span>&#128203;</span>
            <span>Kelola Data SKM</span>
        </a>

        <!-- Menu Kelola Artikel -->
        <a href="{{ route('admin.articles') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.articles') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
            <span>Kelola Artikel</span>
        </a>

        <!-- Menu Kelola Penghargaan -->
        <a href="{{ route('admin.awards') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.awards') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
            <span>Kelola Penghargaan</span>
        </a>

        @if (optional(auth()->user()->role)->name === 'Super Admin')
            <a href="{{ route('admin.users') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.users') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>&#128100;</span>
                <span>Kelola Akun</span>
            </a>
        @endif
    </nav>

    <div class="border-t border-slate-700 p-4">
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Logout
            </button>
        </form>
    </div>
</aside>
