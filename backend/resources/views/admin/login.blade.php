<x-layouts.guest title="Login Admin">
    <div class="w-full max-w-md space-y-4 px-4">
        @if (request()->boolean('expired'))
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-800">
                Session Anda telah berakhir. Silakan login kembali.
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <livewire:admin.login />
    </div>
</x-layouts.guest>
