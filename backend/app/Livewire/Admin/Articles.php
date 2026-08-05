<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads; // Trait untuk upload file
use App\Models\Article; // Import Model Article
use App\Helpers\ActivityLogger;
use Illuminate\Support\Facades\Storage;

class Articles extends Component
{
    use WithFileUploads; // Aktifkan fitur upload

    public $isModalOpen = false;

    // Field Form
    public $title;
    public $published_at;
    public $author;
    public $category = 'Berita Utama';
    public $content;
    public $image; // Variable untuk menampung gambar

    public function openModal()
    {
        $this->reset(['title', 'author', 'content', 'image']);
        $this->published_at = date('Y-m-d');
        $this->category = 'Berita Utama';
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    public function save()
    {
        $this->validate([
            'title'        => 'required|min:3',
            'published_at' => 'required|date',
            'author'       => 'required|min:3',
            'category'     => 'required',
            'content'      => 'required|min:10',
            'image'        => 'nullable|image|max:2048', // Validasi gambar
        ]);

        // Simpan gambar jika ada yang diupload
        $imagePath = null;
        if ($this->image) {
            $imagePath = $this->image->store('articles', 'public');
        }

        // 1. Simpan Data ke Database
        Article::create([
            'title'        => $this->title,
            'published_at' => $this->published_at,
            'author'       => $this->author,
            'category'     => $this->category,
            'content'      => $this->content,
            'image'        => $imagePath,
        ]);

        $this->logActivity('CREATE', 'Artikel: ' . $this->title);

        $this->closeModal();
        session()->flash('message', 'Artikel berhasil ditambahkan!');
    }

    public function delete(int $id): void
    {
        $article = Article::findOrFail($id);
        $title = $article->title;

        if ($article->image) {
            Storage::disk('public')->delete($article->image);
        }

        $article->delete();

        $this->logActivity('DELETE', 'Artikel: ' . $title);

        session()->flash('message', 'Artikel berhasil dihapus.');
    }

    private function logActivity(string $method, string $description): void
    {
        ActivityLogger::log(
            subject: 'Artikel',
            method: $method,
            status: 'success',
            userId: auth()->id(),
            description: $description,
        );
    }

    public function render()
    {
        // 2. Ambil semua data artikel terbaru dari database
        return view('livewire.admin.articles', [
            'articles' => Article::latest()->get()
        ])->layout('layouts.admin', ['title' => 'Kelola Artikel']);
    }
}
