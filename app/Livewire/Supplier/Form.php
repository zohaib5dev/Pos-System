<?php

namespace App\Livewire\Supplier;

use Livewire\Component;
use App\Models\Supplier;
use Illuminate\Support\Facades\Log;

class Form extends Component
{
    // Mode
    public $mode = 'create';
    public $supplierId = null;

    // Form Fields
    public $name = '';
    public $company_name = '';
    public $email = '';
    public $phone = '';
    public $alternative_phone = '';
    public $address = '';
    public $city = '';
    public $state = '';
    public $postal_code = '';
    public $country = '';
    public $tax_number = '';
    public $payment_terms = '';
    public $notes = '';
    public $is_active = true;

    protected function rules()
    {
        $rules = [
            'name' => 'required|min:2|max:255',
            'company_name' => 'nullable|max:255',
            'email' => 'nullable|email',
            'phone' => 'required',
            'alternative_phone' => 'nullable',
            'address' => 'nullable|max:500',
            'city' => 'nullable|max:100',
            'state' => 'nullable|max:100',
            'postal_code' => 'nullable|max:20',
            'country' => 'nullable|max:100',
            'tax_number' => 'nullable|max:50',
            'payment_terms' => 'nullable|max:50',
            'notes' => 'nullable|max:1000',
            'is_active' => 'boolean',
        ];

        // Add unique rules with ignore for edit mode
        if ($this->mode === 'edit' && $this->supplierId) {
            $rules['email'] = 'nullable|email|unique:suppliers,email,' . $this->supplierId;
            $rules['phone'] = 'required|unique:suppliers,phone,' . $this->supplierId;
        } else {
            $rules['email'] = 'nullable|email|unique:suppliers,email';
            $rules['phone'] = 'required|unique:suppliers,phone';
        }

        return $rules;
    }

    public function mount($id = null)
    {
        if ($id) {
            $this->mode = 'edit';
            $this->supplierId = $id;
            $this->loadSupplier();
        }
    }

    public function loadSupplier()
    {
        $supplier = Supplier::find($this->supplierId);

        if ($supplier) {
            $this->name = $supplier->name;
            $this->company_name = $supplier->company_name;
            $this->email = $supplier->email;
            $this->phone = $supplier->phone;
            $this->alternative_phone = $supplier->alternative_phone;
            $this->address = $supplier->address;
            $this->city = $supplier->city;
            $this->state = $supplier->state;
            $this->postal_code = $supplier->postal_code;
            $this->country = $supplier->country;
            $this->tax_number = $supplier->tax_number;
            $this->payment_terms = $supplier->payment_terms;
            $this->notes = $supplier->notes;
            $this->is_active = $supplier->is_active;
        }
    }

    public function saveSupplier()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'company_name' => $this->company_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'alternative_phone' => $this->alternative_phone,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'tax_number' => $this->tax_number,
            'payment_terms' => $this->payment_terms,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
        ];

        try {
            if ($this->mode === 'edit') {
                $supplier = Supplier::find($this->supplierId);

                if (!$supplier) {
                    $this->dispatch('notify', [
                        'message' => 'Supplier not found',
                        'type' => 'error'
                    ]);
                    return;
                }

                $oldData = collect($supplier->toArray())->except(['updated_at'])->toArray();
                $supplier->update($data);

                // Log activity
                logActivity('updated', $supplier, $oldData, $data);

                $this->dispatch('notify', [
                    'message' => 'Supplier updated successfully',
                    'type' => 'success'
                ]);
            } else {
                $data['created_by'] = auth()->id();
                $supplier = Supplier::create($data);

                // Log activity
                logActivity('created', $supplier, [], $data);

                $this->dispatch('notify', [
                    'message' => 'Supplier created successfully',
                    'type' => 'success'
                ]);
            }

            $this->dispatch('supplierSaved');

            // Redirect to supplier details
            return $this->redirectRoute('suppliers.show', ['id' => $supplier->id], navigate: true);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            Log::error('Supplier save error: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return $this->redirectRoute('suppliers.index', navigate: true);
    }

    public function getSupplierProperty()
    {
        if ($this->supplierId) {
            return Supplier::find($this->supplierId);
        }
        return null;
    }

    public function render()
    {
        return view('livewire.suppliers.form', [
            'supplier' => $this->supplier,
        ])->layout('layouts.app');
    }
}