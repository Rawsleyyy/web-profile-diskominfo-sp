<?php

namespace App\Livewire\Admin;

use App\Helpers\ActivityLogger;
use App\Models\CustomPage;
use App\Models\HomepageSection;
use App\Models\SiteModule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.admin')]
class HomepageManager extends Component
{
    use WithFileUploads;

    public ?int $editingId = null;
    public bool $showForm = false;

    public string $label = '';
    public string $section_type = HomepageSection::TYPE_CUSTOM_CONTENT;
    public string $layout = 'image_right';
    public bool $is_enabled = true;

    public string $public_title = '';
    public string $subtitle = '';
    public string $content = '';
    public $image = null;
    public ?string $existingImage = null;
    public string $button_text = '';
    public string $button_url = '';

    public $source_page_id = '';
    public string $video_url = '';
    public string $spacer_size = 'md';

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $section = HomepageSection::findOrFail($id);

        if ($section->isBuiltin()) {
            session()->flash('homepage-message', 'Section bawaan cukup diatur urutan dan visibilitasnya. Gunakan Tambah Bagian untuk membuat section kustom.');
            return;
        }

        $settings = $section->settings ?? [];

        $this->editingId = $section->id;
        $this->label = $section->label;
        $this->section_type = $section->section_type;
        $this->layout = $section->layout ?: 'default';
        $this->is_enabled = $section->is_enabled;
        $this->public_title = (string) ($settings['title'] ?? '');
        $this->subtitle = (string) ($settings['subtitle'] ?? '');
        $this->content = (string) ($settings['content'] ?? '');
        $this->existingImage = $settings['image_path'] ?? null;
        $this->button_text = (string) ($settings['button_text'] ?? '');
        $this->button_url = (string) ($settings['button_url'] ?? '');
        $this->source_page_id = $section->source_type === 'custom_page' ? ($section->source_id ?: '') : '';
        $this->video_url = (string) ($settings['video_url'] ?? '');
        $this->spacer_size = (string) ($settings['size'] ?? 'md');
        $this->image = null;
        $this->showForm = true;
        $this->resetValidation();
    }

    public function updatedSectionType(): void
    {
        $this->layout = match ($this->section_type) {
            HomepageSection::TYPE_CUSTOM_CONTENT => 'image_right',
            HomepageSection::TYPE_PAGE_HIGHLIGHT => 'image_left',
            HomepageSection::TYPE_CTA => 'centered',
            default => 'default',
        };

        $this->resetValidation();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'label' => ['required', 'string', 'max:120'],
            'section_type' => ['required', 'in:custom_content,page_highlight,cta,video,spacer'],
            'layout' => ['required', 'string', 'max:40'],
            'is_enabled' => ['boolean'],
            'public_title' => ['nullable', 'string', 'max:180'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string', 'max:20000'],
            'image' => ['nullable', 'image', 'max:4096'],
            'button_text' => ['nullable', 'string', 'max:80'],
            'button_url' => ['nullable', 'string', 'max:1000'],
            'source_page_id' => ['nullable', 'exists:custom_pages,id'],
            'video_url' => ['nullable', 'url', 'max:1000'],
            'spacer_size' => ['required', 'in:sm,md,lg,xl'],
        ]);

        if ($validated['section_type'] === HomepageSection::TYPE_PAGE_HIGHLIGHT && blank($validated['source_page_id'])) {
            $this->addError('source_page_id', 'Pilih Custom Page yang akan ditampilkan.');
            return;
        }

        if ($validated['section_type'] === HomepageSection::TYPE_VIDEO && blank($validated['video_url'])) {
            $this->addError('video_url', 'URL video wajib diisi. Gunakan URL YouTube atau Vimeo.');
            return;
        }

        if (in_array($validated['section_type'], [HomepageSection::TYPE_CUSTOM_CONTENT, HomepageSection::TYPE_CTA], true)
            && blank($validated['public_title'])) {
            $this->addError('public_title', 'Judul publik wajib diisi untuk jenis section ini.');
            return;
        }

        $section = $this->editingId
            ? HomepageSection::findOrFail($this->editingId)
            : new HomepageSection();

        if ($section->exists && $section->isBuiltin()) {
            abort(422, 'Section bawaan tidak dapat diubah menjadi section kustom.');
        }

        $settings = $section->settings ?? [];
        $imagePath = $settings['image_path'] ?? null;

        if ($this->image) {
            if ($imagePath && ! $this->imagePathUsedByOtherSection($imagePath, $section->id)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $this->image->store('homepage-sections', 'public');
        }

        $settings = match ($validated['section_type']) {
            HomepageSection::TYPE_CUSTOM_CONTENT => [
                'title' => $validated['public_title'] ?: null,
                'subtitle' => $validated['subtitle'] ?: null,
                'content' => $validated['content'] ?: null,
                'image_path' => $imagePath,
                'button_text' => $validated['button_text'] ?: null,
                'button_url' => $validated['button_url'] ?: null,
            ],
            HomepageSection::TYPE_PAGE_HIGHLIGHT => [
                'title' => $validated['public_title'] ?: null,
                'subtitle' => $validated['subtitle'] ?: null,
                'button_text' => $validated['button_text'] ?: 'Lihat Selengkapnya',
            ],
            HomepageSection::TYPE_CTA => [
                'title' => $validated['public_title'] ?: null,
                'subtitle' => $validated['subtitle'] ?: null,
                'button_text' => $validated['button_text'] ?: 'Selengkapnya',
                'button_url' => $validated['button_url'] ?: '/',
            ],
            HomepageSection::TYPE_VIDEO => [
                'title' => $validated['public_title'] ?: null,
                'subtitle' => $validated['subtitle'] ?: null,
                'video_url' => $validated['video_url'],
            ],
            HomepageSection::TYPE_SPACER => [
                'size' => $validated['spacer_size'],
            ],
            default => [],
        };

        $isNew = ! $section->exists;

        $section->fill([
            'section_key' => $section->section_key ?: $this->generateSectionKey($validated['section_type']),
            'label' => $validated['label'],
            'section_type' => $validated['section_type'],
            'module_slug' => null,
            'source_type' => $validated['section_type'] === HomepageSection::TYPE_PAGE_HIGHLIGHT ? 'custom_page' : null,
            'source_id' => $validated['section_type'] === HomepageSection::TYPE_PAGE_HIGHLIGHT ? (int) $validated['source_page_id'] : null,
            'layout' => $validated['layout'],
            'settings' => $settings,
            'is_enabled' => $validated['is_enabled'],
            'sort_order' => $section->sort_order ?: (((int) HomepageSection::max('sort_order')) + 10),
        ]);
        $section->save();

        ActivityLogger::log(
            'Homepage Section',
            $isNew ? 'CREATE' : 'UPDATE',
            'success',
            auth()->id(),
            $section->label
        );

        session()->flash('homepage-message', 'Bagian beranda berhasil disimpan.');
        $this->resetForm();
    }

    public function duplicate(int $id): void
    {
        $source = HomepageSection::findOrFail($id);

        if ($source->isBuiltin()) {
            session()->flash('homepage-message', 'Section bawaan tidak diduplikasi. Buat section kustom jika membutuhkan blok tambahan.');
            return;
        }

        $copy = $source->replicate();
        $copy->section_key = $this->generateSectionKey($source->section_type);
        $copy->label = $source->label.' (Salinan)';
        $copy->sort_order = ((int) HomepageSection::max('sort_order')) + 10;
        $copy->save();

        ActivityLogger::log('Homepage Section', 'CREATE', 'success', auth()->id(), 'Duplikat: '.$source->label);
        session()->flash('homepage-message', 'Section berhasil diduplikasi.');
    }

    public function delete(int $id): void
    {
        $section = HomepageSection::findOrFail($id);

        if ($section->isBuiltin()) {
            session()->flash('homepage-message', 'Section bawaan tidak dapat dihapus. Gunakan tombol Sembunyikan.');
            return;
        }

        $settings = $section->settings ?? [];
        $imagePath = $settings['image_path'] ?? null;
        $label = $section->label;
        $section->delete();

        if ($imagePath && ! $this->imagePathUsedByOtherSection($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }

        ActivityLogger::log('Homepage Section', 'DELETE', 'success', auth()->id(), $label);
        session()->flash('homepage-message', 'Section kustom berhasil dihapus.');
    }

    public function toggle(int $id): void
    {
        $section = HomepageSection::findOrFail($id);
        $section->update(['is_enabled' => ! $section->is_enabled]);

        ActivityLogger::log(
            'Homepage Section',
            'UPDATE',
            'success',
            auth()->id(),
            $section->label.' '.($section->is_enabled ? 'Aktif' : 'Nonaktif')
        );
    }

    public function moveUp(int $id): void
    {
        $current = HomepageSection::findOrFail($id);
        $previous = HomepageSection::where('sort_order', '<', $current->sort_order)
            ->orderByDesc('sort_order')
            ->first();

        if (! $previous) {
            return;
        }

        [$currentOrder, $previousOrder] = [$current->sort_order, $previous->sort_order];
        $current->update(['sort_order' => $previousOrder]);
        $previous->update(['sort_order' => $currentOrder]);
    }

    public function moveDown(int $id): void
    {
        $current = HomepageSection::findOrFail($id);
        $next = HomepageSection::where('sort_order', '>', $current->sort_order)
            ->orderBy('sort_order')
            ->first();

        if (! $next) {
            return;
        }

        [$currentOrder, $nextOrder] = [$current->sort_order, $next->sort_order];
        $current->update(['sort_order' => $nextOrder]);
        $next->update(['sort_order' => $currentOrder]);
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'showForm',
            'label',
            'public_title',
            'subtitle',
            'content',
            'image',
            'existingImage',
            'button_text',
            'button_url',
            'source_page_id',
            'video_url',
        ]);

        $this->section_type = HomepageSection::TYPE_CUSTOM_CONTENT;
        $this->layout = 'image_right';
        $this->is_enabled = true;
        $this->spacer_size = 'md';
        $this->resetValidation();
    }

    private function generateSectionKey(string $type): string
    {
        do {
            $key = 'custom-'.$type.'-'.Str::lower(Str::random(10));
        } while (HomepageSection::where('section_key', $key)->exists());

        return $key;
    }

    private function imagePathUsedByOtherSection(string $path, ?int $exceptId = null): bool
    {
        return HomepageSection::query()
            ->when($exceptId, fn ($query) => $query->where('id', '!=', $exceptId))
            ->get(['id', 'settings'])
            ->contains(fn (HomepageSection $section) => ($section->settings['image_path'] ?? null) === $path);
    }

    public function render()
    {
        $modules = SiteModule::query()->get()->keyBy('slug');

        return view('livewire.admin.homepage-manager', [
            'sections' => HomepageSection::query()->orderBy('sort_order')->get(),
            'modules' => $modules,
            'pages' => CustomPage::query()->orderByDesc('is_published')->orderBy('title')->get(),
        ]);
    }
}
