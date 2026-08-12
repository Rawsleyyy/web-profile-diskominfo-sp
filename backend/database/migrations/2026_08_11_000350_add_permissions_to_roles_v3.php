<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->json('permissions')->nullable()->after('name');
        });

        $all = [
            'dashboard.view', 'content.manage', 'content.publish', 'builder.manage',
            'theme.manage', 'media.manage', 'users.manage', 'roles.manage',
            'logs.view', 'site.publish', 'config.import',
        ];
        $admin = ['dashboard.view', 'content.manage', 'builder.manage', 'theme.manage', 'media.manage'];

        DB::table('roles')->where('name', 'Super Admin')->update(['permissions' => json_encode($all)]);
        DB::table('roles')->where('name', 'Admin')->update(['permissions' => json_encode($admin)]);

        $now = now();
        foreach ([
            'Editor' => ['dashboard.view', 'content.manage', 'media.manage'],
            'Publisher' => ['dashboard.view', 'content.manage', 'content.publish', 'media.manage'],
            'Viewer' => ['dashboard.view'],
        ] as $name => $permissions) {
            if (! DB::table('roles')->where('name', $name)->exists()) {
                DB::table('roles')->insert([
                    'name' => $name,
                    'permissions' => json_encode($permissions),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('roles')->whereIn('name', ['Editor', 'Publisher', 'Viewer'])->delete();
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('permissions');
        });
    }
};
