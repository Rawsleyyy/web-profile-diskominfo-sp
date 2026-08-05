<?php

namespace App\Livewire\Admin;

use App\Helpers\ActivityLogger;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?int $editingId = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string|int $role_id = '';
    public string $status = 'active';

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $user = User::findOrFail($id);
        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role_id = $user->role_id;
        $this->status = $user->status;
        $this->password = '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->editingId)],
            'password' => [$this->editingId ? 'nullable' : 'required', 'string', 'min:8'],
            'role_id' => ['required', 'exists:roles,id'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $user = $this->editingId ? User::with('role')->findOrFail($this->editingId) : new User();

        if ($user->exists && $user->id === auth()->id() && $validated['status'] === 'inactive') {
            $this->addError('status', 'Akun yang sedang digunakan tidak dapat dinonaktifkan.');
            return;
        }

        if ($user->exists && $this->wouldRemoveLastSuperAdmin($user, (int) $validated['role_id'], $validated['status'])) {
            $this->addError('role_id', 'Sistem wajib memiliki minimal satu Super Admin aktif.');
            return;
        }

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => (int) $validated['role_id'],
            'status' => $validated['status'],
        ]);
        if ($validated['password']) {
            $user->password = Hash::make($validated['password']);
        }
        $method = $user->exists ? 'UPDATE' : 'CREATE';
        $user->save();

        ActivityLogger::log('Manajemen User', $method, 'success', auth()->id(), $user->email);
        $this->showModal = false;
        $this->resetForm();
        session()->flash('users-saved', 'Akun berhasil disimpan.');
    }

    public function toggleStatus(int $id): void
    {
        $user = User::with('role')->findOrFail($id);
        if ($user->id === auth()->id()) {
            session()->flash('users-error', 'Akun yang sedang digunakan tidak dapat dinonaktifkan.');
            return;
        }
        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        if ($this->wouldRemoveLastSuperAdmin($user, $user->role_id, $newStatus)) {
            session()->flash('users-error', 'Super Admin aktif terakhir tidak dapat dinonaktifkan.');
            return;
        }
        $user->update(['status' => $newStatus]);
        ActivityLogger::log('Manajemen User', 'UPDATE', 'success', auth()->id(), $user->email.' status='.$newStatus);
    }

    public function delete(int $id): void
    {
        $user = User::with('role')->findOrFail($id);
        if ($user->id === auth()->id()) {
            session()->flash('users-error', 'Akun yang sedang digunakan tidak dapat dihapus.');
            return;
        }
        if ($this->wouldRemoveLastSuperAdmin($user, $user->role_id, 'inactive')) {
            session()->flash('users-error', 'Super Admin aktif terakhir tidak dapat dihapus.');
            return;
        }
        $email = $user->email;
        $user->tokens()->delete();
        $user->delete();
        ActivityLogger::log('Manajemen User', 'DELETE', 'success', auth()->id(), $email);
        session()->flash('users-saved', 'Akun berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.admin.users', [
            'users' => User::with('role')->latest()->paginate(10),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'email', 'password', 'role_id']);
        $this->status = 'active';
        $this->resetValidation();
    }

    private function wouldRemoveLastSuperAdmin(User $user, int $newRoleId, string $newStatus): bool
    {
        $superRoleId = (int) Role::where('name', 'Super Admin')->value('id');
        if ($user->role_id !== $superRoleId || $user->status !== 'active') {
            return false;
        }
        if ($newRoleId === $superRoleId && $newStatus === 'active') {
            return false;
        }
        return User::where('role_id', $superRoleId)->where('status', 'active')->count() <= 1;
    }
}
