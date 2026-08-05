<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use App\Models\Dokumen;
use App\Models\LogActivity;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

#[Layout('components.layouts.admin')]
class DokumenPublikManager extends Component
{
    // ... sisanya tetap sama, tidak perlu diubah
    use WithFileUploads;

    public $isModalOpen = false;
    public $editingId = null;

    public $judul;
    public $kategori = 'Rilis Data';
    public $file;
    public $existingFileName = null;

    // Kategori Khusus Dokumen & Data Publik
    public $kategoriOptions = [
        'Rilis Data',
        'LKJIP',
        'Statistik',
    ];

    public function openModal()
    {
        $this->reset(['editingId', 'judul', 'file', 'existingFileName']);
        $this->kategori = 'Rilis Data';
        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function openEdit(int $id)
    {
        $dokumen = Dokumen::findOrFail($id);

        $this->editingId = $dokumen->id;
        $this->judul = $dokumen->judul;
        $this->kategori = $dokumen->kategori;
        $this->existingFileName = basename($dokumen->file_path);
        $this->file = null;

        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->reset(['editingId', 'judul', 'file', 'existingFileName']);
        $this->kategori = 'Rilis Data';
    }

    public function save()
    {
        $this->validate([
            'judul'    => 'required|min:3|max:255',
            'kategori' => 'required|in:' . implode(',', $this->kategoriOptions),
            'file'     => ($this->editingId ? 'nullable' : 'required') . '|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
        ], [
            'judul.required'    => 'Judul dokumen wajib diisi',
            'kategori.required' => 'Kategori wajib dipilih',
            'file.required'     => 'File dokumen wajib diunggah',
            'file.mimes'        => 'Format file harus PDF, Word, Excel, atau PowerPoint',
            'file.max'          => 'Ukuran file maksimal 10MB',
        ]);

        $data = [
            'judul'    => $this->judul,
            'kategori' => $this->kategori,
        ];

        if ($this->file) {
            if ($this->editingId) {
                $old = Dokumen::find($this->editingId);
                if ($old && $old->file_path) {
                    Storage::disk('public')->delete($old->file_path);
                }
            }
            $data['file_path'] = $this->file->store('dokumen', 'public');
            $data['format'] = strtoupper($this->file->getClientOriginalExtension());
            $data['ukuran_kb'] = (int) round($this->file->getSize() / 1024);
        }

        if ($this->editingId) {
            $dokumen = Dokumen::findOrFail($this->editingId);
            $dokumen->update($data);
            $this->logActivity('UPDATE', 'Dokumen Publik: ' . $this->judul);
            session()->flash('message', 'Dokumen publik berhasil diperbarui!');
        } else {
            Dokumen::create($data);
            $this->logActivity('CREATE', 'Dokumen Publik: ' . $this->judul);
            session()->flash('message', 'Dokumen publik berhasil ditambahkan!');
        }

        $this->closeModal();
    }

    public function delete(int $id)
    {
        $dokumen = Dokumen::findOrFail($id);
        $judul = $dokumen->judul;

        if ($dokumen->file_path) {
            Storage::disk('public')->delete($dokumen->file_path);
        }
        $dokumen->delete();

        $this->logActivity('DELETE', 'Dokumen Publik: ' . $judul);
        session()->flash('message', 'Dokumen publik berhasil dihapus.');
    }

    private function logActivity(string $method, string $description): void
    {
        LogActivity::create([
            'user_id'     => auth()->id(),
            'subject'     => 'Dokumen Publik',
            'method'      => $method,
            'ip_address'  => request()->ip(),
            'description' => $description,
            'status'      => 'success',
        ]);

        Log::channel('audit')->info(sprintf(
            'user_id=%s subject=DokumenPublik method=%s ip=%s status=success description=%s',
            auth()->id(),
            $method,
            request()->ip(),
            $description
        ));
    }

    public function render()
    {
        return view('livewire.admin.dokumen-publik-manager', [
            'dokumenList' => Dokumen::whereIn('kategori', $this->kategoriOptions)
                ->latest()
                ->get(),
        ]);
    }
}
