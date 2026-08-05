<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use App\Models\Award;
use Illuminate\Support\Facades\Storage;

#[Layout('components.layouts.admin')]
class Awards extends Component
{
    use WithFileUploads;

    // ... sisanya tetap sama
    use WithFileUploads;

    public $isModalOpen = false;
    public $editingId = null;

    public $nama_penghargaan;
    public $tahun;
    public $gambar;
    public $existingGambar;
    public $deskripsi;

    public function render()
    {
    return view('livewire.admin.awards', [
        'awards' => Award::latest()->get()
    ]);
    }

    public function openModal()
    {
        $this->resetFields();
        $this->isModalOpen = true;
    }

    public function openEdit($id)
    {
        $this->resetFields();
        $award = Award::findOrFail($id);

        $this->editingId = $award->id;
        // Mengambil data dari database (Mendukung nama kolom title / year / image / description)
        $this->nama_penghargaan = $award->title ?? $award->nama_penghargaan;
        $this->tahun = $award->year ?? $award->tahun;
        $this->deskripsi = $award->description ?? $award->deskripsi;
        $this->existingGambar = $award->image ?? $award->gambar;

        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
    }

    public function resetFields()
    {
        $this->editingId = null;
        $this->nama_penghargaan = '';
        $this->tahun = date('Y');
        $this->gambar = null;
        $this->existingGambar = null;
        $this->deskripsi = '';
    }

    public function save()
    {
        $rules = [
            'nama_penghargaan' => 'required|string|max:255',
            'tahun'            => 'required|numeric',
            'deskripsi'        => 'nullable|string',
        ];

        if ($this->editingId) {
            $rules['gambar'] = 'nullable|image|max:2048';
        } else {
            $rules['gambar'] = 'required|image|max:2048';
        }

        $this->validate($rules);

        if ($this->editingId) {
            $award = Award::findOrFail($this->editingId);
            $imagePath = $award->image ?? $award->gambar;

            if ($this->gambar) {
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $this->gambar->store('awards', 'public');
            }

            // Disimpan ke kolom DB: title, year, image, description
            $award->update([
                'title'       => $this->nama_penghargaan,
                'year'        => $this->tahun,
                'image'       => $imagePath,
                'description' => $this->deskripsi,
            ]);

            session()->flash('message', 'Penghargaan berhasil diperbarui!');
        } else {
            $imagePath = $this->gambar->store('awards', 'public');

            // Disimpan ke kolom DB: title, year, image, description
            Award::create([
                'title'       => $this->nama_penghargaan,
                'year'        => $this->tahun,
                'image'       => $imagePath,
                'description' => $this->deskripsi,
            ]);

            session()->flash('message', 'Penghargaan berhasil ditambahkan!');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        $award = Award::find($id);
        if ($award) {
            $imagePath = $award->image ?? $award->gambar;
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $award->delete();
            session()->flash('message', 'Penghargaan berhasil dihapus!');
        }
    }
}
