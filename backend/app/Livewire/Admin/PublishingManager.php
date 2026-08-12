<?php

namespace App\Livewire\Admin;

use App\Helpers\ActivityLogger;
use App\Models\SitePublication;
use App\Services\PreviewTokenService;
use App\Services\SiteConfigBuilder;
use App\Services\SiteConfigImporter;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.admin')]
class PublishingManager extends Component
{
    use WithFileUploads;
    public string $label = '';
    public string $previewUrl = '';
    public $importFile = null;

    public function generatePreview(): void
    {
        $token=PreviewTokenService::make(auth()->id(),30);
        $frontend=rtrim(config('app.frontend_url', env('FRONTEND_URL','http://localhost:5174')),'/');
        $this->previewUrl=$frontend.'/?preview_token='.urlencode($token);
        $this->dispatch('open-preview', url:$this->previewUrl);
    }

    public function publish(SiteConfigBuilder $builder): void
    {
        $version=((int)SitePublication::max('version'))+1;
        SitePublication::create(['version'=>$version,'label'=>$this->label ?: 'Publikasi v'.$version,'payload'=>$builder->build(),'published_by'=>auth()->id()]);
        ActivityLogger::log('Website Publication','PUBLISH','success',auth()->id(),'v'.$version);
        $this->label=''; session()->flash('publish-message','Website berhasil dipublikasikan sebagai versi '.$version.'.');
    }

    public function restore(int $id): void
    {
        $old=SitePublication::findOrFail($id); $version=((int)SitePublication::max('version'))+1;
        SitePublication::create(['version'=>$version,'label'=>'Restore dari v'.$old->version,'payload'=>$old->payload,'published_by'=>auth()->id()]);
        ActivityLogger::log('Website Publication','RESTORE','success',auth()->id(),'v'.$old->version.' -> v'.$version);
        session()->flash('publish-message','Versi '.$old->version.' dipulihkan sebagai publikasi baru v'.$version.'.');
    }

    public function exportConfig(SiteConfigBuilder $builder)
    {
        $json=json_encode(['schema'=>'institution-cms-v3','exported_at'=>now()->toIso8601String(),'config'=>$builder->build()],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
        return response()->streamDownload(fn()=>print($json),'institution-config-'.now()->format('Ymd-His').'.json',['Content-Type'=>'application/json']);
    }

    public function importConfig(SiteConfigImporter $importer): void
    {
        abort_unless(auth()->user()?->hasPermission('config.import'), 403);
        $this->validate(['importFile'=>['required','file','max:2048','mimes:json,txt']]);
        $data=json_decode(file_get_contents($this->importFile->getRealPath()),true);
        $payload=$data['config'] ?? $data;
        if(!is_array($payload)||!isset($payload['settings'],$payload['modules'])) { $this->addError('importFile','Format konfigurasi tidak dikenali.'); return; }
        $importer->apply($payload); $this->reset('importFile');
        ActivityLogger::log('Configuration','IMPORT','success',auth()->id(),'Import JSON');
        session()->flash('publish-message','Konfigurasi berhasil diimpor ke draft. Preview lalu Publish untuk menerapkannya ke publik.');
    }

    public function render()
    {
        return view('livewire.admin.publishing-manager',['publications'=>SitePublication::with('publisher')->latest('version')->limit(20)->get()]);
    }
}
