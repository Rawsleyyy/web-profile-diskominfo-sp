<header class="sticky top-0 z-30 bg-white border-b border-gray-200 px-6 py-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-serif text-lg text-gray-900">
                Panel {{ auth()->user()->role->name ?? 'Admin' }} — Diskominfo SP
            </h2>
        </div>

        <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-600">
            Session berakhir dalam
            <strong id="admin-session-countdown" class="ml-1 font-mono text-gray-900">--:--</strong>
        </div>
    </div>
</header>