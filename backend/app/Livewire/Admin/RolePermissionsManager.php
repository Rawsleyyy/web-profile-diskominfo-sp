<?php

namespace App\Livewire\Admin;

use App\Helpers\ActivityLogger;
use App\Models\Role;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class RolePermissionsManager extends Component
{
    public array $permissionsByRole = [];

    public const PERMISSIONS = [
        'dashboard.view'=>'Lihat Dashboard', 'content.manage'=>'Kelola Konten', 'content.publish'=>'Publish Konten',
        'builder.manage'=>'Kelola Navbar/Homepage/Modul', 'theme.manage'=>'Kelola Theme & Identitas', 'media.manage'=>'Kelola Media',
        'users.manage'=>'Kelola Akun', 'roles.manage'=>'Kelola Role & Permission', 'logs.view'=>'Lihat Audit Log',
        'site.publish'=>'Preview / Publish Website', 'config.import'=>'Import Konfigurasi',
    ];

    public function mount(): void
    {
        foreach(Role::orderBy('id')->get() as $role) $this->permissionsByRole[$role->id]=$role->permissions ?? [];
    }

    public function save(int $roleId): void
    {
        $role=Role::findOrFail($roleId);
        if($role->name==='Super Admin'){session()->flash('role-message','Super Admin selalu memiliki seluruh permission.');return;}
        $selected=array_values(array_intersect($this->permissionsByRole[$roleId] ?? [], array_keys(self::PERMISSIONS)));
        $role->update(['permissions'=>$selected]);
        ActivityLogger::log('Role Permission','UPDATE','success',auth()->id(),$role->name);
        session()->flash('role-message','Permission role '.$role->name.' berhasil disimpan.');
    }

    public function render(){return view('livewire.admin.role-permissions-manager',['roles'=>Role::orderBy('id')->get(),'permissionOptions'=>self::PERMISSIONS]);}
}
