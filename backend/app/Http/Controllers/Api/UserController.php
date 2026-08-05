<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(User::with('role')->latest()->paginate(20));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role_id' => ['required', 'exists:roles,id'],
            'instansi' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', Rule::in(['active', 'inactive'])],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = $validated['status'] ?? 'active';
        $user = User::create($validated);

        ActivityLogger::log('Manajemen User', 'CREATE', 'success', $request->user()->id, $user->email);

        return response()->json($user->load('role'), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::with('role')->findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['sometimes', 'nullable', 'string', 'min:8'],
            'role_id' => ['sometimes', 'required', 'exists:roles,id'],
            'instansi' => ['sometimes', 'nullable', 'string', 'max:255'],
            'status' => ['sometimes', Rule::in(['active', 'inactive'])],
        ]);

        $this->guardSuperAdminAvailability($request, $user, $validated);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);
        ActivityLogger::log('Manajemen User', 'UPDATE', 'success', $request->user()->id, $user->email);

        return response()->json($user->fresh()->load('role'));
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = User::with('role')->findOrFail($id);

        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Akun yang sedang digunakan tidak dapat dihapus.'], 422);
        }

        if ($this->isLastActiveSuperAdmin($user)) {
            return response()->json(['message' => 'Super Admin aktif terakhir tidak dapat dihapus.'], 422);
        }

        $email = $user->email;
        $user->tokens()->delete();
        $user->delete();
        ActivityLogger::log('Manajemen User', 'DELETE', 'success', $request->user()->id, $email);

        return response()->json(['message' => 'User berhasil dihapus.']);
    }

    private function guardSuperAdminAvailability(Request $request, User $user, array $validated): void
    {
        if ($user->id === $request->user()->id && ($validated['status'] ?? null) === 'inactive') {
            throw new HttpResponseException(response()->json(['message' => 'Akun yang sedang digunakan tidak dapat dinonaktifkan.'], 422));
        }

        $newRoleId = (int) ($validated['role_id'] ?? $user->role_id);
        $newStatus = $validated['status'] ?? $user->status;
        $superAdminRoleId = (int) Role::where('name', 'Super Admin')->value('id');

        if ($this->isLastActiveSuperAdmin($user)
            && ($newRoleId !== $superAdminRoleId || $newStatus !== 'active')) {
            throw new HttpResponseException(response()->json(['message' => 'Sistem wajib memiliki minimal satu Super Admin aktif.'], 422));
        }
    }

    private function isLastActiveSuperAdmin(User $user): bool
    {
        if ($user->role?->name !== 'Super Admin' || $user->status !== 'active') {
            return false;
        }

        return User::query()
            ->where('status', 'active')
            ->whereHas('role', fn ($query) => $query->where('name', 'Super Admin'))
            ->count() <= 1;
    }
}
