<?php

namespace App\Livewire;

use App\Models\VisitorLog;
use Livewire\Component;

class VisitorCounter extends Component
{
    public function render()
    {
        return view('livewire.visitor-counter', [
            'total' => VisitorLog::count(),
        ]);
    }
}
