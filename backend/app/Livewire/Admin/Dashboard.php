<?php

namespace App\Livewire\Admin;

use App\Models\Article;
use App\Models\Berita;
use App\Models\Dokumen;
use App\Models\Layanan;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'totalBerita' => Berita::count(),
            'totalLayanan' => Layanan::count(),
            'totalDokumen' => Dokumen::count(),
            'totalArtikel' => Article::count(),
        ]);
    }
}
