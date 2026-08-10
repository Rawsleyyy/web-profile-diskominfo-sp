<?php

namespace App\Livewire\Admin;

use App\Helpers\ActivityLogger;
use App\Models\CustomPage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.admin')]
class PagesManager extends Component
{
    use WithFileUploads;

    public ?int $editingId = null;
    public string $title = '';
    public string $slug = '';
    public string $excerpt = '';
    public string $content = '';
    public $banner = null;
    public ?string $existingBanner = null;
    public bool $is_published = false;
    public ?string $published_at = null;
    public bool $showForm = false;

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $page = CustomPage::findOrFail($id);
        $this->editingId = $page->id;
        $this->title = $page->title;
        $this->slug = $page->slug;
        $this->excerpt = (string) $page->excerpt;
        $this->content = $page->content;
        $this->existingBanner = $page->banner_path;
        $this->is_published = $page->is_published;
        $this->published_at = optional($page->published_at)->format('Y-m-d\TH:i');
        $this->banner = null;
        $this->showForm = true;
    }

    public function updatedTitle(): void
    {
        if (! $this->editingId || blank($this->slug)) {
            $this->slug = Str::slug($this->title);
        }
    }

    public function save(): void
    {
        $this->slug = Str::slug($this->slug ?: $this->title);

        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('custom_pages', 'slug')->ignore($this->editingId)],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string', 'min:3'],
            'banner' => ['nullable', 'image', 'max:3072'],
            'is_published' => ['boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $page = $this->editingId ? CustomPage::findOrFail($this->editingId) : new CustomPage();
        $bannerPath = $page->banner_path;

        if ($this->banner) {
            if ($bannerPath) {
                Storage::disk('public')->delete($bannerPath);
            }
            $bannerPath = $this->banner->store('custom-pages', 'public');
        }

        $page->fill([
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'excerpt' => $validated['excerpt'] ?: null,
            'content' => $validated['content'],
            'banner_path' => $bannerPath,
            'is_published' => $validated['is_published'],
            'published_at' => $validated['is_published'] ? ($validated['published_at'] ?: now()) : null,
            'updated_by' => auth()->id(),
        ]);

        if (! $page->exists) {
            $page->created_by = auth()->id();
        }

        $method = $page->exists ? 'UPDATE' : 'CREATE';
        $page->save();

        ActivityLogger::log('Custom Page', $method, 'success', auth()->id(), $page->title);
        session()->flash('page-message', 'Halaman berhasil disimpan.');
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $page = CustomPage::findOrFail($id);
        $title = $page->title;
        if ($page->banner_path) {
            Storage::disk('public')->delete($page->banner_path);
        }
        $page->delete();
        ActivityLogger::log('Custom Page', 'DELETE', 'success', auth()->id(), $title);
        session()->flash('page-message', 'Halaman berhasil dihapus.');
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'title', 'slug', 'excerpt', 'content', 'banner', 'existingBanner', 'is_published', 'published_at', 'showForm']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.pages-manager', [
            'pages' => CustomPage::query()->latest()->get(),
        ]);
    }
}
