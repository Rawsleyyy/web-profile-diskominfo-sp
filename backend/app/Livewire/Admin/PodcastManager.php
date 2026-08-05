<?php

namespace App\Livewire\Admin;

use App\Models\Podcast;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.admin')]
class PodcastManager extends Component
{
    use WithFileUploads;

    public $podcastId;
    public $judul = '';
    public $episode = '';
    public $deskripsi = '';
    public $type = 'file'; // hanya dipakai di form, untuk menentukan input mana yang tampil
    public $audio;         // temporary uploaded file
    public $audio_link = '';
    public $existingUrlAudio;
    public $thumbnail;     // temporary uploaded file
    public $existingThumbnail;
    public $is_active = true;

    public $showModal = false;
    public $isEditMode = false;

    protected function rules()
    {
        return [
            'judul' => 'required|string|max:255',
            'episode' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
            'audio' => $this->type === 'file' && !$this->existingUrlAudio
                ? 'required|file|mimes:mp3,wav,ogg|max:51200'
                : 'nullable|file|mimes:mp3,wav,ogg|max:51200',
            'audio_link' => $this->type === 'link' ? 'required|url' : 'nullable|url',
            'thumbnail' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ];
    }

    public function render()
    {
        return view('livewire.admin.podcast-manager', [
            'podcasts' => Podcast::latest()->get(),
        ]);
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $podcast = Podcast::findOrFail($id);

        $this->podcastId = $podcast->id;
        $this->judul = $podcast->judul;
        $this->episode = $podcast->episode;
        $this->deskripsi = $podcast->deskripsi;
        $this->existingThumbnail = $podcast->thumbnail;
        $this->is_active = $podcast->is_active;

        if ($podcast->isLink()) {
            $this->type = 'link';
            $this->audio_link = $podcast->url_audio;
            $this->existingUrlAudio = null;
        } else {
            $this->type = 'file';
            $this->existingUrlAudio = $podcast->url_audio;
            $this->audio_link = '';
        }

        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'judul' => $this->judul,
            'episode' => $this->episode,
            'deskripsi' => $this->deskripsi,
            'is_active' => $this->is_active,
        ];

        if ($this->type === 'link') {
            $data['url_audio'] = $this->audio_link;
        } else {
            if ($this->audio) {
                $data['url_audio'] = $this->audio->store('podcasts/audio', 'public');
            } elseif ($this->existingUrlAudio) {
                $data['url_audio'] = $this->existingUrlAudio;
            }
        }

        if ($this->thumbnail) {
            $data['thumbnail'] = $this->thumbnail->store('podcasts/thumbnails', 'public');
        } elseif ($this->existingThumbnail) {
            $data['thumbnail'] = $this->existingThumbnail;
        }

        if ($this->isEditMode) {
            Podcast::findOrFail($this->podcastId)->update($data);
            session()->flash('message', 'Podcast berhasil diperbarui!');
        } else {
            Podcast::create($data);
            session()->flash('message', 'Podcast berhasil ditambahkan!');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        Podcast::findOrFail($id)->delete();
        session()->flash('message', 'Podcast berhasil dihapus!');
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->showModal = false;
    }

    private function resetForm()
    {
        $this->reset([
            'podcastId', 'judul', 'episode', 'deskripsi', 'audio',
            'audio_link', 'thumbnail', 'existingUrlAudio',
            'existingThumbnail', 'is_active',
        ]);
        $this->type = 'file';
        $this->is_active = true;
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
