<?php

namespace App\Livewire\Admin;

use App\Helpers\ActivityLogger;
use App\Models\HomepageSection;
use App\Models\SiteModule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class HomepageManager extends Component
{
    public function toggle(int $id): void
    {
        $section = HomepageSection::findOrFail($id);
        $section->update(['is_enabled' => ! $section->is_enabled]);
        ActivityLogger::log('Homepage Section', 'UPDATE', 'success', auth()->id(), $section->label.' '.($section->is_enabled ? 'Aktif' : 'Nonaktif'));
    }

    public function moveUp(int $id): void
    {
        $current = HomepageSection::findOrFail($id);
        $previous = HomepageSection::where('sort_order', '<', $current->sort_order)->orderByDesc('sort_order')->first();
        if (! $previous) return;
        [$currentOrder, $previousOrder] = [$current->sort_order, $previous->sort_order];
        $current->update(['sort_order' => $previousOrder]);
        $previous->update(['sort_order' => $currentOrder]);
    }

    public function moveDown(int $id): void
    {
        $current = HomepageSection::findOrFail($id);
        $next = HomepageSection::where('sort_order', '>', $current->sort_order)->orderBy('sort_order')->first();
        if (! $next) return;
        [$currentOrder, $nextOrder] = [$current->sort_order, $next->sort_order];
        $current->update(['sort_order' => $nextOrder]);
        $next->update(['sort_order' => $currentOrder]);
    }

    public function render()
    {
        $modules = SiteModule::query()->get()->keyBy('slug');
        return view('livewire.admin.homepage-manager', [
            'sections' => HomepageSection::query()->orderBy('sort_order')->get(),
            'modules' => $modules,
        ]);
    }
}
