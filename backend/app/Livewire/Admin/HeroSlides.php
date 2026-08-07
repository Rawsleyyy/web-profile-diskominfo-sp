<?php

namespace App\Livewire\Admin;

use App\Helpers\ActivityLogger;
use App\Models\HeroSlide;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.admin')]
class HeroSlides extends Component
{
    use WithFileUploads;

    public bool $isModalOpen = false;
    public ?int $editingId = null;

    public string $title = '';
    public string $subtitle = '';
    public $image = null;
    public ?string $existingImage = null;
    public string $altText = '';
    public string $buttonLabel = '';
    public string $buttonUrl = '';
    public int $urutan = 0;
    public bool $isActive = true;

    public function openCreate(): void
    {
        $this->resetForm();
        $this->urutan = (int) HeroSlide::max('urutan') + 1;
        $this->isModalOpen = true;
    }

    public function openEdit(int $id): void
    {
        $slide = HeroSlide::findOrFail($id);

        $this->resetValidation();
        $this->editingId = $slide->id;
        $this->title = $slide->title;
        $this->subtitle = $slide->subtitle ?? '';
        $this->existingImage = $slide->image_path;
        $this->altText = $slide->alt_text ?? '';
        $this->buttonLabel = $slide->button_label ?? '';
        $this->buttonUrl = $slide->button_url ?? '';
        $this->urutan = $slide->urutan;
        $this->isActive = $slide->is_active;
        $this->isModalOpen = true;
    }

    public function closeModal(): void
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function save(): void
    {
        $rules = [
            'title' => ['required', 'string', 'min:3', 'max:180'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'altText' => ['nullable', 'string', 'max:255'],
            'buttonLabel' => ['nullable', 'string', 'max:60'],
            'buttonUrl' => ['nullable', 'string', 'max:2048'],
            'urutan' => ['required', 'integer', 'min:0', 'max:999'],
            'isActive' => ['boolean'],
            'image' => [
                $this->editingId ? 'nullable' : 'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
                'dimensions:min_width=1000,min_height=350',
            ],
        ];

        $messages = [
            'image.required' => 'Gambar header wajib dipilih.',
            'image.max' => 'Ukuran gambar maksimal 5 MB.',
            'image.dimensions' => 'Resolusi gambar minimal 1000 × 350 piksel.',
        ];

        $this->validate($rules, $messages);

        $slide = $this->editingId
            ? HeroSlide::findOrFail($this->editingId)
            : new HeroSlide();

        $imagePath = $slide->image_path;

        if ($this->image) {
            $newImagePath = $this->image->store('hero-slides', 'public');

            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            $imagePath = $newImagePath;
        }

        $slide->fill([
            'title' => trim($this->title),
            'subtitle' => $this->nullableString($this->subtitle),
            'image_path' => $imagePath,
            'alt_text' => $this->nullableString($this->altText),
            'button_label' => $this->nullableString($this->buttonLabel),
            'button_url' => $this->nullableString($this->buttonUrl),
            'urutan' => $this->urutan,
            'is_active' => $this->isActive,
        ]);

        $isNew = ! $slide->exists;
        $slide->save();

        ActivityLogger::log(
            subject: 'Hero Header',
            method: $isNew ? 'CREATE' : 'UPDATE',
            status: 'success',
            userId: auth()->id(),
            description: $slide->title,
        );

        session()->flash('message', $isNew
            ? 'Header berhasil ditambahkan.'
            : 'Header berhasil diperbarui.');

        $this->closeModal();
    }

    public function toggleActive(int $id): void
    {
        $slide = HeroSlide::findOrFail($id);
        $slide->update(['is_active' => ! $slide->is_active]);

        ActivityLogger::log(
            subject: 'Hero Header',
            method: 'UPDATE',
            status: 'success',
            userId: auth()->id(),
            description: $slide->title.' - '.($slide->is_active ? 'diaktifkan' : 'dinonaktifkan'),
        );

        session()->flash('message', 'Status header berhasil diubah.');
    }

    public function delete(int $id): void
    {
        $slide = HeroSlide::findOrFail($id);
        $title = $slide->title;

        if ($slide->image_path && Storage::disk('public')->exists($slide->image_path)) {
            Storage::disk('public')->delete($slide->image_path);
        }

        $slide->delete();

        ActivityLogger::log(
            subject: 'Hero Header',
            method: 'DELETE',
            status: 'success',
            userId: auth()->id(),
            description: $title,
        );

        session()->flash('message', 'Header berhasil dihapus.');
    }

    private function resetForm(): void
    {
        $this->resetValidation();
        $this->reset([
            'editingId',
            'title',
            'subtitle',
            'image',
            'existingImage',
            'altText',
            'buttonLabel',
            'buttonUrl',
            'urutan',
            'isActive',
        ]);
        $this->isActive = true;
    }

    private function nullableString(string $value): ?string
    {
        $value = trim($value);

        return $value === '' ? null : $value;
    }

    public function render()
    {
        return view('livewire.admin.hero-slides', [
            'slides' => HeroSlide::query()
                ->orderBy('urutan')
                ->orderBy('id')
                ->get(),
        ]);
    }
}
