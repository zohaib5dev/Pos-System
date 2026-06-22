<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;

class Form extends Component
{
    // Mode
    public $mode = 'create';
    public $customerId = null;

    // Form Fields
    public $customer_code = '';
    public $name = '';
    public $email = '';
    public $phone = '';
    public $address = '';
    public $city = '';
    public $state = '';
    public $postal_code = '';
    public $country = '';
    public $tax_number = '';
    public $opening_balance = 0;
    public $current_balance = 0;
    public $credit_limit = 0;
    public $allow_credit = false;
    public $loyalty_points = 0;
    public $notes = '';
    public $is_active = true;

    protected function rules()
    {
        $rules = [
            'name' => 'required|min:2|max:255',
            'email' => 'nullable|email',
            'phone' => 'required',
            'address' => 'nullable|max:500',
            'city' => 'nullable|max:100',
            'state' => 'nullable|max:100',
            'postal_code' => 'nullable|max:20',
            'country' => 'nullable|max:100',
            'tax_number' => 'nullable|max:50',
            'opening_balance' => 'nullable|numeric|min:0',
            'current_balance' => 'nullable|numeric|min:0',
            'credit_limit' => 'nullable|numeric|min:0',
            'allow_credit' => 'boolean',
            'loyalty_points' => 'nullable|integer|min:0',
            'notes' => 'nullable|max:1000',
            'is_active' => 'boolean',
        ];

        if ($this->mode === 'edit' && $this->customerId) {
            $rules['customer_code'] = 'required|unique:customers,customer_code,' . $this->customerId;
            $rules['email'] = 'nullable|email|unique:customers,email,' . $this->customerId;
            $rules['phone'] = 'required|unique:customers,phone,' . $this->customerId;
        } else {
            $rules['customer_code'] = 'required|unique:customers,customer_code';
            $rules['email'] = 'nullable|email|unique:customers,email';
            $rules['phone'] = 'required|unique:customers,phone';
        }

        return $rules;
    }

    public function mount($id = null)
    {
        if ($id) {
            $this->mode = 'edit';
            $this->customerId = $id;
            $this->loadCustomer();
        } else {
            $this->mode = 'create';
            $this->generateCustomerCode();
        }
    }

    public function loadCustomer()
    {
        $customer = Customer::find($this->customerId);

        if ($customer) {
            $this->customer_code = $customer->customer_code;
            $this->name = $customer->name;
            $this->email = $customer->email;
            $this->phone = $customer->phone;
            $this->address = $customer->address;
            $this->city = $customer->city;
            $this->state = $customer->state;
            $this->postal_code = $customer->postal_code;
            $this->country = $customer->country;
            $this->tax_number = $customer->tax_number;
            $this->opening_balance = $customer->opening_balance;
            $this->current_balance = $customer->current_balance;
            $this->credit_limit = $customer->credit_limit;
            $this->allow_credit = $customer->allow_credit;
            $this->loyalty_points = $customer->loyalty_points;
            $this->notes = $customer->notes;
            $this->is_active = $customer->is_active;
        }
    }

    public function generateCustomerCode()
    {
        $this->customer_code = 'CUS-' . strtoupper(uniqid());
    }

    public function saveCustomer()
    {
        $this->validate();

        $data = [
            'customer_code' => $this->customer_code,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'tax_number' => $this->tax_number,
            'opening_balance' => $this->opening_balance,
            'current_balance' => $this->current_balance,
            'credit_limit' => $this->credit_limit,
            'allow_credit' => $this->allow_credit,
            'loyalty_points' => $this->loyalty_points,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
        ];

        try {
            if ($this->mode === 'edit') {
                $customer = Customer::find($this->customerId);

                if (!$customer) {
                    $this->dispatch('notify', [
                        'message' => 'Customer not found',
                        'type' => 'error'
                    ]);
                    return;
                }

                $oldData = collect($customer->toArray())->except(['updated_at'])->toArray();
                $customer->update($data);

                $this->dispatch('notify', [
                    'message' => 'Customer updated successfully',
                    'type' => 'success'
                ]);
            } else {
                $data['created_by'] = auth()->id();
                $customer = Customer::create($data);

                $this->dispatch('notify', [
                    'message' => 'Customer created successfully',
                    'type' => 'success'
                ]);
            }

            $this->dispatch('customerSaved');
            return $this->redirectRoute('customers.show', ['id' => $customer->id], navigate: true);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            Log::error('Customer save error: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return $this->redirectRoute('customers.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.customers.form')
            ->layout('layouts.app');
    }
}