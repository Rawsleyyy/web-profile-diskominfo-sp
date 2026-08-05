<x-layouts.guest title="Login Admin">
    <div class="w-full max-w-md space-y-4 px-4">
        @if (request()->boolean('expired'))
            <div class="rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                Session Anda telah berakhir. Silakan login kembali.
            </div>
        @endif

        <livewire:admin.login />
    </div>
</x-layouts.guest>
