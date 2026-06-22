<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Customer;

class Show extends Component
{
    public $customerId;
    public $customer;

    public function mount($id)
    {
        $this->customerId = $id;
        $this->loadCustomer();
    }

    public function loadCustomer()
    {
        $this->customer = Customer::with(['orders' => function ($q) {
            $q->latest()->limit(10);
        }, 'creator'])
            ->withCount('orders')
            ->find($this->customerId);

        if (!$this->customer) {
            session()->flash('error', 'Customer not found');
            return $this->redirectRoute('customers.index', navigate: true);
        }
    }

    public function goBack()
    {
        return $this->redirectRoute('customers.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.customers.show', [
            'customer' => $this->customer,
        ])->layout('layouts.app');
    }
}