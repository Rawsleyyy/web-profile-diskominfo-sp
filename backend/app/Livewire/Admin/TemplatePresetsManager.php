<?php

namespace App\Livewire\Admin;

use App\Helpers\ActivityLogger;
use App\Services\InstitutionPresetService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class TemplatePresetsManager extends Component
{
    public function apply(string $key, InstitutionPresetService $service): void
    {
        $preset=InstitutionPresetService::presets()[$key] ?? null; if(!$preset)return;
        $service->apply($key); ActivityLogger::log('Institution Preset','APPLY','success',auth()->id(),$preset['label']);
        session()->flash('preset-message','Preset '.$preset['label'].' diterapkan ke draft. Konten database tidak dihapus. Preview sebelum Publish.');
    }
    public function render(){return view('livewire.admin.template-presets-manager',['presets'=>InstitutionPresetService::presets()]);}
}
