<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Skm;
use App\Models\Layanan;

class SkmManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $search = '';
    public $filterLayanan = '';

    // Detail (lihat jawaban lengkap 1 responden)
    public $isDetailOpen = false;
    public $selected = null;

    // Daftar pertanyaan, dipakai untuk menampilkan teks pertanyaan di modal detail.
    // Harus tetap sinkron dengan pertanyaan pada SKMSurveyForm.jsx (frontend).
    public array $pertanyaan = [
        1 => 'Kesesuaian persyaratan pelayanan',
        2 => 'Kemudahan prosedur pelayanan',
        3 => 'Kecepatan waktu pelayanan',
        4 => 'Kewajaran biaya/tarif pelayanan',
        5 => 'Kesesuaian produk layanan',
        6 => 'Kompetensi/kemampuan petugas',
        7 => 'Kesopanan dan keramahan petugas',
        8 => 'Penanganan pengaduan pengguna layanan',
        9 => 'Kualitas sarana dan prasarana',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterLayanan()
    {
        $this->resetPage();
    }

    public function detail($id)
    {
        $this->selected = Skm::with('jenisLayanan')->findOrFail($id);
        $this->isDetailOpen = true;
    }

    public function closeDetail()
    {
        $this->isDetailOpen = false;
        $this->selected = null;
    }

    public function delete($id)
    {
        Skm::destroy($id);

        if ($this->selected && $this->selected->id == $id) {
            $this->closeDetail();
        }

        session()->flash('message', 'Data responden berhasil dihapus!');
    }

    public function render()
    {
        $query = Skm::with('jenisLayanan')->latest();

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                  ->orWhere('no_whatsapp', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterLayanan !== '') {
            $query->where('jenis_layanan_id', $this->filterLayanan);
        }

        return view('livewire.admin.skm-manager', [
            'responden' => $query->paginate(10),
            'daftarLayanan' => Layanan::orderBy('nama_layanan')->get(),
        ])->layout('components.layouts.admin', ['title' => 'Kelola Data SKM']);
    }
}
