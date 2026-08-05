<?php

namespace App\Livewire\Admin;

use App\Models\LogActivity as LogActivityModel;
use Livewire\Component;
use Livewire\WithPagination;

class LogActivity extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $logs = LogActivityModel::with('user')
            ->when($this->search, fn ($query) => $query->where(function ($q) {
                $q->where('subject', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%')
                    ->orWhere('ip_address', 'like', '%'.$this->search.'%');
            }))
            ->when($this->statusFilter, fn ($query) => $query->where('status', $this->statusFilter))
            ->latest('created_at')
            ->paginate(15);

        return view('livewire.admin.log-activity', compact('logs'));
    }
}
