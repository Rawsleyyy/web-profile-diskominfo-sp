<?php

namespace App\Livewire\Admin;

use App\Helpers\ActivityLogger;
use App\Models\CustomPage;
use App\Models\MenuItem;
use App\Models\SiteModule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class NavigationManager extends Component
{
    public ?int $editingId = null;
    public string $label = '';
    public string $type = 'route';
    public string $url = '';
    public string $module_slug = '';
    public $page_id = '';
    public $parent_id = '';
    public int $sort_order = 10;
    public string $target = '_self';
    public bool $is_active = true;
    public ?string $visible_from = null;
    public ?string $visible_until = null;
    public bool $showForm = false;

    public function openCreate(?int $parentId = null): void
    {
        $this->resetForm();
        $this->parent_id = $parentId ?: '';
        $this->sort_order = ((int) MenuItem::where('parent_id', $parentId)->max('sort_order')) + 10;
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $item = MenuItem::findOrFail($id);

        $this->editingId = $item->id;
        $this->label = $item->label;
        $this->type = $item->type;
        $this->url = (string) $item->url;
        $this->module_slug = (string) $item->module_slug;
        $this->page_id = $item->page_id ?: '';
        $this->parent_id = $item->parent_id ?: '';
        $this->sort_order = $item->sort_order;
        $this->target = $item->target;
        $this->is_active = $item->is_active;
        $this->visible_from = optional($item->visible_from)->format('Y-m-d\TH:i');
        $this->visible_until = optional($item->visible_until)->format('Y-m-d\TH:i');
        $this->showForm = true;
    }

    public function updatedType(string $value): void
    {
        if (! in_array($value, ['route', 'external'], true)) {
            $this->url = '';
        }
        if ($value !== 'module') {
            $this->module_slug = '';
        }
        if ($value !== 'page') {
            $this->page_id = '';
        }
        if ($value === 'external') {
            $this->target = '_blank';
        }
        if ($value === 'dropdown') {
            $this->target = '_self';
        }
        $this->resetValidation();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'label' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:route,module,page,external,dropdown'],
            'url' => ['nullable', 'string', 'max:1000'],
            'module_slug' => ['nullable', 'string', 'exists:site_modules,slug'],
            'page_id' => ['nullable', 'exists:custom_pages,id'],
            'parent_id' => ['nullable', 'exists:menu_items,id'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'target' => ['required', 'in:_self,_blank'],
            'is_active' => ['boolean'],
            'visible_from' => ['nullable', 'date'],
            'visible_until' => ['nullable', 'date', 'after_or_equal:visible_from'],
        ]);

        if ($this->editingId && (int) ($validated['parent_id'] ?: 0) === $this->editingId) {
            $this->addError('parent_id', 'Menu tidak dapat menjadi parent untuk dirinya sendiri.');
            return;
        }

        if (in_array($validated['type'], ['route', 'external'], true) && blank($validated['url'])) {
            $this->addError('url', 'URL wajib diisi untuk jenis menu ini.');
            return;
        }

        if ($validated['type'] === 'module' && blank($validated['module_slug'])) {
            $this->addError('module_slug', 'Pilih modul yang akan dituju.');
            return;
        }

        if ($validated['type'] === 'page' && blank($validated['page_id'])) {
            $this->addError('page_id', 'Pilih halaman yang akan dituju.');
            return;
        }

        $item = $this->editingId ? MenuItem::findOrFail($this->editingId) : new MenuItem();
        $method = $item->exists ? 'UPDATE' : 'CREATE';

        $item->fill([
            'label' => $validated['label'],
            'type' => $validated['type'],
            'url' => in_array($validated['type'], ['route', 'external'], true) ? ($validated['url'] ?: null) : null,
            'module_slug' => $validated['type'] === 'module' ? ($validated['module_slug'] ?: null) : null,
            'page_id' => $validated['type'] === 'page' ? ($validated['page_id'] ?: null) : null,
            'parent_id' => $validated['parent_id'] ?: null,
            'sort_order' => $validated['sort_order'],
            'target' => $validated['type'] === 'external' ? '_blank' : $validated['target'],
            'is_active' => $validated['is_active'],
            'visible_from' => $validated['visible_from'] ?: null,
            'visible_until' => $validated['visible_until'] ?: null,
        ]);
        $item->save();

        ActivityLogger::log('Navbar', $method, 'success', auth()->id(), $item->label);
        session()->flash('navigation-message', 'Menu navbar berhasil disimpan.');
        $this->resetForm();
    }

    public function toggle(int $id): void
    {
        $item = MenuItem::findOrFail($id);
        $item->update(['is_active' => ! $item->is_active]);

        ActivityLogger::log(
            'Navbar',
            'UPDATE',
            'success',
            auth()->id(),
            $item->label.' '.($item->is_active ? 'Aktif' : 'Nonaktif')
        );
    }

    public function delete(int $id): void
    {
        $item = MenuItem::findOrFail($id);
        $label = $item->label;
        $item->delete();

        ActivityLogger::log('Navbar', 'DELETE', 'success', auth()->id(), $label);
        session()->flash('navigation-message', 'Menu navbar berhasil dihapus.');
    }

    public function moveBefore(int $draggedId, int $targetId): void
    {
        if ($draggedId === $targetId) return;
        $dragged = MenuItem::findOrFail($draggedId);
        $target = MenuItem::findOrFail($targetId);
        if ((int) $dragged->parent_id !== (int) $target->parent_id) {
            session()->flash('navigation-message', 'Drag hanya dapat dilakukan pada level menu yang sama.');
            return;
        }
        $items = MenuItem::query()->where('parent_id', $dragged->parent_id)->orderBy('sort_order')->orderBy('id')->get()->reject(fn ($item) => $item->id === $dragged->id)->values();
        $targetIndex = $items->search(fn ($item) => $item->id === $target->id);
        $items->splice($targetIndex === false ? $items->count() : $targetIndex, 0, [$dragged]);
        foreach ($items as $index => $item) $item->update(['sort_order' => ($index + 1) * 10]);
        ActivityLogger::log('Navbar', 'REORDER', 'success', auth()->id(), $dragged->label);
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'label',
            'url',
            'module_slug',
            'page_id',
            'parent_id',
            'visible_from',
            'visible_until',
            'showForm',
        ]);

        $this->type = 'route';
        $this->sort_order = 10;
        $this->target = '_self';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.navigation-manager', [
            'rootItems' => MenuItem::query()
                ->with('children')
                ->whereNull('parent_id')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
            'parentOptions' => MenuItem::query()
                ->whereNull('parent_id')
                ->when($this->editingId, fn ($query) => $query->where('id', '!=', $this->editingId))
                ->orderBy('sort_order')
                ->get(),
            'modules' => SiteModule::query()->orderBy('sort_order')->get(),
            'pages' => CustomPage::query()->orderBy('title')->get(),
        ]);
    }
}
