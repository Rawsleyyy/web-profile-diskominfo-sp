<?php

namespace App\Livewire\Admin;

use App\Helpers\ActivityLogger;
use App\Models\SiteModule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class ModulesManager extends Component
{
    public function toggle(int $id): void
    {
        $module = SiteModule::findOrFail($id);
        $module->update(['is_enabled' => ! $module->is_enabled]);
        SiteModule::flushCache();

        ActivityLogger::log(
            'Module Website',
            'UPDATE',
            'success',
            auth()->id(),
            sprintf('%s: %s', $module->name, $module->is_enabled ? 'Aktif' : 'Nonaktif')
        );

        session()->flash('module-message', 'Status modul berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.admin.modules-manager', [
            'modules' => SiteModule::query()->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }
}
