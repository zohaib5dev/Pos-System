<?php

namespace App\Livewire\Supplier;

use Livewire\Component;
use App\Models\Supplier;

class Show extends Component
{
    public $supplierId;
    public $supplier;

    public function mount($id)
    {
        $this->supplierId = $id;
        $this->loadSupplier();
    }

    public function loadSupplier()
    {
        $this->supplier = Supplier::with(['purchases' => function ($q) {
            $q->latest()->limit(10);
        }, 'creator'])
            ->withCount('purchases')
            ->find($this->supplierId);

        if (!$this->supplier) {
            $this->dispatch('notify', [
                'message' => 'Supplier not found',
                'type' => 'error'
            ]);
            return $this->redirectRoute('suppliers.index', navigate: true);
        }
    }

    public function goBack()
    {
        return $this->redirectRoute('suppliers.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.suppliers.show', [
            'supplier' => $this->supplier,
        ])->layout('layouts.app');
    }
}