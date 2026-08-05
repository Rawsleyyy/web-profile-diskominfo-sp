<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Pejabat;
use App\Models\LogActivity;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PejabatManager extends Component
{
    use WithFileUploads;

    public $isModalOpen = false;
    public $editingId = null;

    public $nama;
    public $jabatan;
    public $urutan = 0;
    public $tampil_utama = false;
    public $parent_id = null;
    public $foto;
    public $existingFoto = null;

    public function openModal()
    {
        $this->reset(['editingId', 'nama', 'jabatan', 'foto', 'existingFoto', 'parent_id']);
        $this->urutan = Pejabat::max('urutan') + 1;
        $this->tampil_utama = false;
        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function openEdit(int $id)
    {
        $pejabat = Pejabat::findOrFail($id);

        $this->editingId = $pejabat->id;
        $this->nama = $pejabat->nama;
        $this->jabatan = $pejabat->jabatan;
        $this->urutan = $pejabat->urutan;
        $this->tampil_utama = (bool) $pejabat->tampil_utama;
        $this->parent_id = $pejabat->parent_id;
        $this->existingFoto = $pejabat->foto;
        $this->foto = null;

        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->reset(['editingId', 'nama', 'jabatan', 'foto', 'existingFoto', 'parent_id']);
        $this->tampil_utama = false;
    }

    public function save()
    {
        $this->validate([
            'nama'         => 'required|min:3|max:255',
            'jabatan'      => 'required|min:3|max:255',
            'urutan'       => 'required|integer|min:0',
            'foto'         => 'nullable|image|max:2048',
            'tampil_utama' => 'boolean',
            'parent_id'    => 'nullable|exists:pejabats,id',
        ], [
            'nama.required'    => 'Nama pejabat wajib diisi',
            'jabatan.required' => 'Jabatan wajib diisi',
        ]);

        // Cegah orang jadi atasan dirinya sendiri
        if ($this->editingId && $this->parent_id == $this->editingId) {
            $this->addError('parent_id', 'Pejabat tidak bisa menjadi atasan dirinya sendiri.');
            return;
        }

        $data = [
            'nama'         => $this->nama,
            'jabatan'      => $this->jabatan,
            'urutan'       => $this->urutan,
            'tampil_utama' => $this->tampil_utama,
            'parent_id'    => $this->parent_id ?: null,
        ];

        if ($this->foto) {
            if ($this->editingId) {
                $old = Pejabat::find($this->editingId);
                if ($old && $old->foto) {
                    Storage::disk('public')->delete($old->foto);
                }
            }
            $data['foto'] = $this->foto->store('pejabat', 'public');
        }

        if ($this->editingId) {
            $pejabat = Pejabat::findOrFail($this->editingId);
            $pejabat->update($data);
            $this->logActivity('UPDATE', 'Pejabat: ' . $this->nama);
            session()->flash('message', 'Data pejabat berhasil diperbarui!');
        } else {
            Pejabat::create($data);
            $this->logActivity('CREATE', 'Pejabat: ' . $this->nama);
            session()->flash('message', 'Pejabat berhasil ditambahkan!');
        }

        $this->closeModal();
    }

    public function delete(int $id)
    {
        $pejabat = Pejabat::findOrFail($id);
        $nama = $pejabat->nama;

        if ($pejabat->foto) {
            Storage::disk('public')->delete($pejabat->foto);
        }
        // Bawahan otomatis jadi parent_id null (lihat migration: nullOnDelete)
        $pejabat->delete();

        $this->logActivity('DELETE', 'Pejabat: ' . $nama);
        session()->flash('message', 'Pejabat berhasil dihapus.');
    }

    private function logActivity(string $method, string $description): void
    {
        LogActivity::create([
            'user_id'     => auth()->id(),
            'subject'     => 'Pejabat',
            'method'      => $method,
            'ip_address'  => request()->ip(),
            'description' => $description,
            'status'      => 'success',
        ]);

        Log::channel('audit')->info(sprintf(
            'user_id=%s subject=Pejabat method=%s ip=%s status=success description=%s',
            auth()->id(),
            $method,
            request()->ip(),
            $description
        ));
    }

    public function render()
    {
        return view('livewire.admin.pejabat-manager', [
            'pejabatList'    => Pejabat::with('parent')->orderBy('urutan')->orderBy('id')->get(),
            // Untuk dropdown "Atasan": semua pejabat KECUALI yang lagi diedit sendiri
            'pejabatOptions' => Pejabat::orderBy('urutan')->orderBy('id')->get()
                ->reject(fn ($p) => $p->id === $this->editingId),
        ]);
    }
}
