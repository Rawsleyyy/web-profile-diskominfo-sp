<?php

namespace App\Livewire\Admin;

use App\Helpers\ActivityLogger;
use App\Models\MediaAsset;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class MediaLibrary extends Component
{
    use WithFileUploads, WithPagination;

    public $file = null;
    public string $alt_text = '';
    public string $category = 'general';
    public string $search = '';

    public function upload(): void
    {
        $validated = $this->validate([
            'file' => ['required', 'file', 'max:10240', 'mimes:jpg,jpeg,png,webp,gif,svg,pdf,mp4,webm'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'in:general,image,document,video,branding'],
        ]);
        $path = $this->file->store('media-library', 'public');
        $asset = MediaAsset::create([
            'name' => $this->file->getClientOriginalName(), 'path' => $path, 'disk' => 'public',
            'mime_type' => $this->file->getMimeType(), 'size' => $this->file->getSize(),
            'alt_text' => $validated['alt_text'] ?: null, 'category' => $validated['category'], 'uploaded_by' => auth()->id(),
        ]);
        ActivityLogger::log('Media Library', 'CREATE', 'success', auth()->id(), $asset->name);
        $this->reset(['file', 'alt_text']);
        session()->flash('media-message', 'Media berhasil diunggah.');
    }

    public function delete(int $id): void
    {
        $asset = MediaAsset::findOrFail($id);
        Storage::disk($asset->disk)->delete($asset->path);
        $name = $asset->name;
        $asset->delete();
        ActivityLogger::log('Media Library', 'DELETE', 'success', auth()->id(), $name);
        session()->flash('media-message', 'Media berhasil dihapus. Pastikan file ini tidak sedang dipakai halaman lain.');
    }

    public function render()
    {
        return view('livewire.admin.media-library', [
            'assets' => MediaAsset::query()->when($this->search, fn($q) => $q->where(fn($x) => $x->where('name','like','%'.$this->search.'%')->orWhere('alt_text','like','%'.$this->search.'%')))->latest()->paginate(18),
        ]);
    }
}
