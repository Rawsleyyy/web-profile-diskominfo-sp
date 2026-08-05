<div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-black">Diskominfo SP</h1>
        <p class="mt-1 text-sm text-gray-500">Masuk ke Panel Admin</p>
    </div>

    <form wire:submit="login" class="space-y-4">
        <div>
            <label class="block mb-1 text-sm font-medium text-black">Email</label>
            <input type="email" wire:model="email" autocomplete="email" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="admin@surakarta.go.id">
            @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block mb-1 text-sm font-medium text-black">Password</label>
            <input type="password" wire:model="password" autocomplete="current-password" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        <button type="submit" wire:loading.attr="disabled" class="w-full rounded-md bg-blue-600 py-2.5 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-60">
            <span wire:loading.remove>Masuk</span>
            <span wire:loading>Memverifikasi...</span>
        </button>
    </form>
</div>
