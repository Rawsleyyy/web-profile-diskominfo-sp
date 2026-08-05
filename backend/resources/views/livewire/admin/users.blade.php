<div class="trezo-card bg-white dark:bg-[#0c1427] rounded-md">
    <div class="p-[25px] flex items-center justify-between border-b border-gray-100 dark:border-[#172036]">
        <h3 class="font-serif text-xl text-black dark:text-white">Daftar Akun Admin</h3>
        <button wire:click="openCreate" class="flex items-center gap-2 px-4 py-2 rounded-md bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            + Tambah Akun
        </button>
    </div>

    @if (session('users-saved'))
        <div class="mx-[25px] mt-[20px] flex items-center justify-between rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
            <span class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('users-saved') }}
            </span>
            <button class="text-green-600 hover:text-green-800">&times;</button>
        </div>
    @endif
    @if (session('users-error'))
        <div class="mx-[25px] mt-[20px] rounded-md bg-red-100 px-4 py-3 text-sm text-red-700">
            {{ session('users-error') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-gray-100 dark:border-[#172036] text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wider font-serif">
                    <th class="p-[15px]">Nama</th>
                    <th class="p-[15px]">Email</th>
                    <th class="p-[15px]">Role</th>
                    <th class="p-[15px]">Status</th>
                    <th class="p-[15px]">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="border-b border-gray-100 dark:border-[#172036] text-black dark:text-white">
                        <td class="p-[15px]">
                            <div class="flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                                {{ $user->name }}
                            </div>
                        </td>
                        <td class="p-[15px] text-gray-600 dark:text-gray-300">{{ $user->email }}</td>
                        <td class="p-[15px]">
                            <span class="px-3 py-1 rounded-full text-xs font-medium border border-gray-200 dark:border-[#172036] text-gray-700 dark:text-gray-300">
                                {{ $user->role->name ?? '-' }}
                            </span>
                        </td>
                        <td class="p-[15px]">
                            <button wire:click="toggleStatus({{ $user->id }})" class="px-3 py-1 rounded-full text-xs font-medium {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                                {{ $user->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </td>
                        <td class="p-[15px] space-x-2">
                            <button wire:click="openEdit({{ $user->id }})" class="text-blue-600 hover:underline font-medium">Edit</button>
                            <button wire:click="delete({{ $user->id }})" wire:confirm="Yakin hapus akun ini?" class="text-red-600 hover:underline font-medium">Hapus</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="px-[25px] py-[20px] flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
        <span>Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} entries</span>
        {{ $users->links() }}
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-[#0c1427] rounded-md shadow-lg w-full max-w-md p-6 relative">
                <h2 class="text-xl font-bold text-black dark:text-white mb-4">
                    {{ $editingId ? 'Edit Akun Admin' : 'Tambah Akun Admin Baru' }}
                </h2>

                <form wire:submit.prevent="save">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama</label>
                        <input type="text" wire:model="name" class="w-full px-3 py-2 border border-gray-300 dark:border-[#172036] rounded-md dark:bg-[#15203c] dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nama lengkap">
                        @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                        <input type="email" wire:model="email" class="w-full px-3 py-2 border border-gray-300 dark:border-[#172036] rounded-md dark:bg-[#15203c] dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="email@contoh.com">
                        @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Password {{ $editingId ? '(kosongkan jika tidak diubah)' : '' }}
                        </label>
                        <input type="password" wire:model="password" class="w-full px-3 py-2 border border-gray-300 dark:border-[#172036] rounded-md dark:bg-[#15203c] dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Minimal 8 karakter">
                        @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                        <select wire:model="role_id" class="w-full px-3 py-2 border border-gray-300 dark:border-[#172036] rounded-md dark:bg-[#15203c] dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Role --</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('role_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>


                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select wire:model="status" class="w-full px-3 py-2 border border-gray-300 dark:border-[#172036] rounded-md dark:bg-[#15203c] dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="active">Aktif</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
                        @error('status') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-gray-100 dark:border-[#172036]">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 bg-gray-200 dark:bg-[#172036] text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                            {{ $editingId ? 'Simpan Perubahan' : 'Simpan Akun' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
