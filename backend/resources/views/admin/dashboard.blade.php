<x-layouts.admin title="Dashboard Admin">
    <div class="p-[25px]">
        <div class="mb-[25px]">
            <h1 class="text-2xl font-bold text-black dark:text-white">
                Dashboard {{ auth()->user()->role->name ?? 'Admin' }}
            </h1>
            <p class="mt-1 text-gray-500 dark:text-gray-400">
                Selamat datang di sistem pengelolaan Web Profile Diskominfo SP Kota Surakarta.
            </p>
        </div>

        <livewire:admin.dashboard />
    </div>
</x-layouts.admin>