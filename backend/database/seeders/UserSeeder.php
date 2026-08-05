<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            $this->command?->warn('UserSeeder dilewati di luar environment local/testing.');
            return;
        }

        $email = env('SEED_SUPER_ADMIN_EMAIL');
        $password = env('SEED_SUPER_ADMIN_PASSWORD');

        if (! $email || ! $password || strlen($password) < 12) {
            throw new RuntimeException('Isi SEED_SUPER_ADMIN_EMAIL dan SEED_SUPER_ADMIN_PASSWORD (minimal 12 karakter) pada .env.');
        }

        $role = Role::firstOrCreate(['name' => 'Super Admin']);
        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => env('SEED_SUPER_ADMIN_NAME', 'Super Admin'),
                'password' => Hash::make($password),
                'role_id' => $role->id,
                'status' => 'active',
            ]
        );
    }
}
