<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Berita;
use App\Models\LogActivity;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BeritaManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $isModalOpen = false;
    public $editingId = null;

    // Field Form
    public $judul;
    public $konten;
    public $status_publish = false;
    public $thumbnail;
    public $existingThumbnail = null;

    protected $paginationTheme = 'tailwind';

    public function openModal()
    {
        $this->reset(['editingId', 'judul', 'konten', 'thumbnail', 'existingThumbnail']);
        $this->status_publish = false;
        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function openEdit(int $id)
    {
        $berita = Berita::findOrFail($id);

        $this->editingId = $berita->id;
        $this->judul = $berita->judul;
        $this->konten = $berita->konten;
        $this->status_publish = (bool) $berita->status_publish;
        $this->existingThumbnail = $berita->thumbnail;
        $this->thumbnail = null;

        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->reset(['editingId', 'judul', 'konten', 'thumbnail', 'existingThumbnail']);
        $this->status_publish = false;
    }

    public function save()
    {
        $this->validate([
            'judul'          => 'required|min:3|max:255',
            'konten'         => 'required|min:10',
            'thumbnail'      => 'nullable|image|max:2048',
            'status_publish' => 'boolean',
        ], [
            'judul.required'  => 'Judul berita wajib diisi',
            'konten.required' => 'Konten berita wajib diisi',
        ]);

        $data = [
            'judul'          => $this->judul,
            'konten'         => $this->konten,
            'status_publish' => $this->status_publish,
        ];

        if ($this->thumbnail) {
            // Hapus thumbnail lama jika sedang edit dan ganti gambar baru
            if ($this->editingId) {
                $old = Berita::find($this->editingId);
                if ($old && $old->thumbnail) {
                    Storage::disk('public')->delete($old->thumbnail);
                }
            }
            $data['thumbnail'] = $this->thumbnail->store('berita', 'public');
        }

        if ($this->editingId) {
            $berita = Berita::findOrFail($this->editingId);
            $berita->update($data);
            $this->logActivity('UPDATE', 'Berita: ' . $this->judul);
            session()->flash('message', 'Berita berhasil diperbarui!');
        } else {
            $data['author_id'] = auth()->id();
            Berita::create($data);
            $this->logActivity('CREATE', 'Berita: ' . $this->judul);
            session()->flash('message', 'Berita berhasil ditambahkan!');
        }

        $this->closeModal();
    }

    public function togglePublish(int $id)
    {
        $berita = Berita::findOrFail($id);
        $berita->status_publish = ! $berita->status_publish;
        $berita->save();

        $this->logActivity('UPDATE', ($berita->status_publish ? 'Publikasikan' : 'Sembunyikan') . ' Berita: ' . $berita->judul);
    }

    public function delete(int $id)
    {
        $berita = Berita::findOrFail($id);
        $judul = $berita->judul;

        if ($berita->thumbnail) {
            Storage::disk('public')->delete($berita->thumbnail);
        }
        $berita->delete();

        $this->logActivity('DELETE', 'Berita: ' . $judul);

        session()->flash('message', 'Berita berhasil dihapus.');
    }

    private function logActivity(string $method, string $description): void
    {
        LogActivity::create([
            'user_id'     => auth()->id(),
            'subject'     => 'Berita',
            'method'      => $method,
            'ip_address'  => request()->ip(),
            'description' => $description,
            'status'      => 'success',
        ]);

        Log::channel('audit')->info(sprintf(
            'user_id=%s subject=Berita method=%s ip=%s status=success description=%s',
            auth()->id(),
            $method,
            request()->ip(),
            $description
        ));
    }

    public function render()
    {
        return view('livewire.admin.berita-manager', [
            'beritas' => Berita::with('author')->latest()->paginate(10),
        ]);
    }
}
