<div class="max-w-2xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">
        Theme Settings
    </h1>

    @if (session('theme-saved'))
        <div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/30 border border-green-300 dark:border-green-700 px-4 py-3 text-green-700 dark:text-green-300 text-sm">
            {{ session('theme-saved') }}
        </div>
    @endif

    <form wire:submit="save" class="space-y-6">

        {{-- Primary Color --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Primary Color
            </label>
            <div class="flex items-center gap-3">
                <input
                    type="color"
                    wire:model.live="primary_color_hex"
                    class="h-11 w-16 rounded-lg border border-gray-300 dark:border-gray-600 cursor-pointer bg-transparent"
                >
                <input
                    type="text"
                    wire:model.live="primary_color_hex"
                    placeholder="#1E3A8A"
                    maxlength="7"
                    class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                >
            </div>
            @error('primary_color_hex')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Accent Color --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Accent Color
            </label>
            <div class="flex items-center gap-3">
                <input
                    type="color"
                    wire:model.live="accent_color_hex"
                    class="h-11 w-16 rounded-lg border border-gray-300 dark:border-gray-600 cursor-pointer bg-transparent"
                >
                <input
                    type="text"
                    wire:model.live="accent_color_hex"
                    placeholder="#DC2626"
                    maxlength="7"
                    class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                >
            </div>
            @error('accent_color_hex')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Live Preview --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Live Preview
            </label>
            <div class="flex gap-4 items-center p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="flex flex-col items-center gap-1">
                    <div class="w-16 h-16 rounded-lg shadow" style="background-color: {{ $primary_color_hex }}"></div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Primary</span>
                </div>
                <div class="flex flex-col items-center gap-1">
                    <div class="w-16 h-16 rounded-lg shadow" style="background-color: {{ $accent_color_hex }}"></div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Accent</span>
                </div>
                <button
                    type="button"
                    class="ml-auto px-4 py-2 rounded-lg text-white text-sm font-medium shadow"
                    style="background-color: {{ $primary_color_hex }}; border: 1px solid {{ $accent_color_hex }};"
                >
                    Contoh Tombol
                </button>
            </div>
        </div>

        <button
            type="submit"
            wire:loading.attr="disabled"
            class="px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium disabled:opacity-50"
        >
            <span wire:loading.remove>Simpan Perubahan</span>
            <span wire:loading>Menyimpan...</span>
        </button>
    </form>
</div>
