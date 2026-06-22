<?php

namespace App\Livewire\ActivityLog;

use Livewire\Component;
use App\Models\ActivityLog;

class Show extends Component
{
    public $id;
    public $log = null;

    public function mount($id)
    {
        $this->id = $id;
        if (!ActivityLog::find($id)) {
            session()->flash('error', 'Activity log not found');
            return redirect()->route('activity-logs.index');
        }

        $this->loadLog();
    }

    public function loadLog()
    {
        $this->log = ActivityLog::find($this->id);
    }

    public function render()
    {
        return view('livewire.activity-logs.show', [
            'id' => $this->id,
            'log' => $this->log,
        ])->layout('layouts.app');
    }
}
