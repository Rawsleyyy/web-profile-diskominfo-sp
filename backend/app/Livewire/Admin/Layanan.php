<?php

namespace App\Livewire\Admin;

use App\Helpers\ActivityLogger;
use App\Models\Layanan as LayananModel;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.admin')]
class Layanan extends Component
{
    use WithFileUploads;

    public bool $isModalOpen = false;
    public ?int $service_id = null;
    public string $nama_layanan = '';
    public string $kategori = 'Umum';
    public string $deskripsi = '';
    public string $url_eksternal = '';
    public int $urutan = 0;
    public bool $is_active = true;
    public $icon;
    public ?string $existingIcon = null;
    public string $search = '';

    public function openModal(): void
    {
        $this->resetInputFields();
        $this->urutan = ((int) LayananModel::max('urutan')) + 1;
        $this->isModalOpen = true;
    }

    public function closeModal(): void
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'nama_layanan' => ['required', 'string', 'min:3', 'max:150'],
            'kategori' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
            'url_eksternal' => ['required', 'url:http,https', 'max:2048'],
            'urutan' => ['required', 'integer', 'min:0', 'max:999'],
            'is_active' => ['boolean'],
            'icon' => ['nullable', 'image', 'max:2048'],
        ]);

        $service = $this->service_id ? LayananModel::findOrFail($this->service_id) : new LayananModel();
        $oldIcon = $service->icon_path;

        $service->fill([
            'nama_layanan' => $validated['nama_layanan'],
            'kategori' => $validated['kategori'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'url_eksternal' => $validated['url_eksternal'],
            'urutan' => $validated['urutan'],
            'is_active' => $validated['is_active'],
        ]);

        if ($this->icon) {
            $service->icon_path = $this->icon->store('layanan', 'public');
            if ($oldIcon) {
                Storage::disk('public')->delete($oldIcon);
            }
        }

        $method = $service->exists ? 'UPDATE' : 'CREATE';
        $service->save();
        ActivityLogger::log('Layanan', $method, 'success', auth()->id(), $service->nama_layanan);

        session()->flash('message', 'Layanan berhasil disimpan.');
        $this->closeModal();
    }

    public function edit(int $id): void
    {
        $service = LayananModel::findOrFail($id);
        $this->service_id = $service->id;
        $this->nama_layanan = $service->nama_layanan;
        $this->kategori = $service->kategori ?? 'Umum';
        $this->deskripsi = $service->deskripsi ?? '';
        $this->url_eksternal = $service->url_eksternal ?? '';
        $this->urutan = $service->urutan ?? 0;
        $this->is_active = (bool) $service->is_active;
        $this->existingIcon = $service->icon_path;
        $this->icon = null;
        $this->isModalOpen = true;
    }

    public function toggleActive(int $id): void
    {
        $service = LayananModel::findOrFail($id);
        $service->update(['is_active' => ! $service->is_active]);
        ActivityLogger::log('Layanan', 'UPDATE', 'success', auth()->id(), $service->nama_layanan.' status='.(int) $service->is_active);
    }

    public function delete(int $id): void
    {
        $service = LayananModel::findOrFail($id);
        $name = $service->nama_layanan;
        if ($service->icon_path) {
            Storage::disk('public')->delete($service->icon_path);
        }
        $service->delete();
        ActivityLogger::log('Layanan', 'DELETE', 'success', auth()->id(), $name);
        session()->flash('message', 'Layanan berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.admin.layanan', [
            'services' => LayananModel::query()
                ->when($this->search, fn ($query) => $query->where('nama_layanan', 'like', '%'.$this->search.'%'))
                ->orderBy('urutan')
                ->orderBy('nama_layanan')
                ->get(),
        ]);
    }

    private function resetInputFields(): void
    {
        $this->reset(['service_id', 'nama_layanan', 'deskripsi', 'url_eksternal', 'icon', 'existingIcon']);
        $this->kategori = 'Umum';
        $this->urutan = 0;
        $this->is_active = true;
        $this->resetValidation();
    }
}
