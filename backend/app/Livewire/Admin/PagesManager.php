<?php

namespace App\Livewire\Admin;

use App\Helpers\ActivityLogger;
use App\Models\CustomPage;
use App\Models\MediaAsset;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.admin')]
class PagesManager extends Component
{
    use WithFileUploads;

    public ?int $editingId = null;
    public string $title = '';
    public string $slug = '';
    public string $excerpt = '';
    public string $content = '';
    public $banner = null;
    public ?string $existingBanner = null;
    public string|int $banner_media_id = '';
    public bool $is_published = false;
    public ?string $published_at = null;
    public string $seo_title = '';
    public string $seo_description = '';
    public $seo_og = null;
    public ?string $existingSeoOg = null;
    public string|int $seo_og_media_id = '';
    public bool $robots_index = true;
    public bool $showForm = false;

    public function openCreate(): void { $this->resetForm(); $this->showForm = true; }

    public function edit(int $id): void
    {
        $page = CustomPage::findOrFail($id);
        foreach (['title','slug','excerpt','content','seo_title','seo_description'] as $field) $this->{$field} = (string) ($page->{$field} ?? '');
        $this->editingId=$page->id; $this->existingBanner=$page->banner_path; $this->existingSeoOg=$page->seo_og_path;
        $this->is_published=(bool)$page->is_published; $this->robots_index=(bool)$page->robots_index;
        $this->published_at=optional($page->published_at)->format('Y-m-d\TH:i'); $this->banner=null; $this->seo_og=null; $this->showForm=true;
    }

    public function updatedTitle(): void { if (! $this->editingId || blank($this->slug)) $this->slug = Str::slug($this->title); }

    public function save(): void
    {
        $this->slug = Str::slug($this->slug ?: $this->title);
        $validated = $this->validate([
            'title'=>['required','string','max:255'],'slug'=>['required','string','max:255','regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',Rule::unique('custom_pages','slug')->ignore($this->editingId)],
            'excerpt'=>['nullable','string','max:1000'],'content'=>['required','string','min:3'],'banner'=>['nullable','image','max:4096'],'banner_media_id'=>['nullable','exists:media_assets,id'],
            'is_published'=>['boolean'],'published_at'=>['nullable','date'],'seo_title'=>['nullable','string','max:255'],'seo_description'=>['nullable','string','max:500'],
            'seo_og'=>['nullable','image','max:4096'],'seo_og_media_id'=>['nullable','exists:media_assets,id'],'robots_index'=>['boolean'],
        ]);

        if ($validated['is_published'] && ! auth()->user()?->hasPermission('content.publish')) {
            $this->addError('is_published', 'Role Anda dapat mengedit halaman, tetapi tidak memiliki permission untuk publish.');
            return;
        }

        $page = $this->editingId ? CustomPage::findOrFail($this->editingId) : new CustomPage();
        $bannerPath=$page->banner_path; $seoOgPath=$page->seo_og_path;
        if ($this->banner) { $this->deleteIfOwned($bannerPath); $bannerPath=$this->banner->store('custom-pages','public'); }
        elseif ($validated['banner_media_id'] ?? null) { $bannerPath=MediaAsset::find($validated['banner_media_id'])?->path ?: $bannerPath; }
        if ($this->seo_og) { $this->deleteIfOwned($seoOgPath); $seoOgPath=$this->seo_og->store('custom-pages/seo','public'); }
        elseif ($validated['seo_og_media_id'] ?? null) { $seoOgPath=MediaAsset::find($validated['seo_og_media_id'])?->path ?: $seoOgPath; }

        $page->fill([
            'title'=>$validated['title'],'slug'=>$validated['slug'],'excerpt'=>$validated['excerpt'] ?: null,'content'=>$validated['content'],'banner_path'=>$bannerPath,
            'is_published'=>$validated['is_published'],'published_at'=>$validated['is_published'] ? ($validated['published_at'] ?: now()) : null,
            'seo_title'=>$validated['seo_title'] ?: null,'seo_description'=>$validated['seo_description'] ?: null,'seo_og_path'=>$seoOgPath,'robots_index'=>$validated['robots_index'],'updated_by'=>auth()->id(),
        ]);
        if (! $page->exists) $page->created_by=auth()->id();
        $method=$page->exists?'UPDATE':'CREATE'; $page->save();
        ActivityLogger::log('Custom Page',$method,'success',auth()->id(),$page->title);
        session()->flash('page-message','Halaman berhasil disimpan. Status: '.($page->is_published?'Published':'Draft').'.'); $this->resetForm();
    }

    public function togglePublish(int $id): void
    {
        abort_unless(auth()->user()?->hasPermission('content.publish'), 403);
        $page=CustomPage::findOrFail($id); $page->is_published=!$page->is_published; $page->published_at=$page->is_published?($page->published_at ?: now()):null; $page->updated_by=auth()->id(); $page->save();
        ActivityLogger::log('Custom Page','UPDATE','success',auth()->id(),$page->title.' publish='.($page->is_published?'yes':'no'));
    }

    public function delete(int $id): void
    {
        $page=CustomPage::findOrFail($id); $title=$page->title; $this->deleteIfOwned($page->banner_path); $this->deleteIfOwned($page->seo_og_path); $page->delete();
        ActivityLogger::log('Custom Page','DELETE','success',auth()->id(),$title); session()->flash('page-message','Halaman berhasil dihapus.');
    }

    public function cancel(): void { $this->resetForm(); }
    private function deleteIfOwned(?string $path): void { if($path && !str_starts_with($path,'media-library/')) Storage::disk('public')->delete($path); }
    private function resetForm(): void { $this->reset(['editingId','title','slug','excerpt','content','banner','existingBanner','banner_media_id','is_published','published_at','seo_title','seo_description','seo_og','existingSeoOg','seo_og_media_id','showForm']); $this->robots_index=true; $this->resetValidation(); }

    public function render()
    {
        return view('livewire.admin.pages-manager', [
            'pages'=>CustomPage::query()->latest()->get(),
            'mediaImages'=>MediaAsset::query()->where('mime_type','like','image/%')->latest()->limit(100)->get(),
        ]);
    }
}
