<?php

namespace App\Livewire\Brand;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class Form extends Component
{
    use WithFileUploads;

    // Mode
    public $mode = 'create'; // create, edit
    public $brandId = null;

    // Form Fields
    public $name = '';
    public $slug = '';
    public $description = '';
    public $logo = null;
    public $existing_logo = null;
    public $is_active = true;

    // Temporary upload for preview
    public $logoPreview = null;

    // Dynamic validation rules
    protected function rules()
    {
        $rules = [
            'name' => 'required|min:2|max:255',
            'description' => 'nullable|max:1000',
            'logo' => 'nullable|image|max:1024', // Max 1MB
            'is_active' => 'boolean',
        ];

        // Add unique rule for slug with ignore for edit mode
        if ($this->mode === 'edit' && $this->brandId) {
            $rules['slug'] = 'required|max:255|unique:brands,slug,' . $this->brandId;
        } else {
            $rules['slug'] = 'required|max:255|unique:brands,slug';
        }

        return $rules;
    }

    public function mount($id = null)
    {
        if ($id) {
            $this->mode = 'edit';
            $this->brandId = $id;
            $this->loadBrand();
        }
    }

    public function loadBrand()
    {
        $brand = Brand::find($this->brandId);

        if ($brand) {
            $this->name = $brand->name;
            $this->slug = $brand->slug;
            $this->description = $brand->description;
            $this->existing_logo = $brand->logo;
            $this->is_active = $brand->is_active;
        }
    }

    public function updatedName()
    {
        if ($this->mode === 'create') {
            $this->slug = Str::slug($this->name);
        }
    }

    public function updatedLogo()
    {
        $this->validate([
            'logo' => 'image|max:1024',
        ]);

        $this->logoPreview = $this->logo->temporaryUrl();
    }

    public function removeLogo()
    {
        $this->logo = null;
        $this->logoPreview = null;

        if ($this->mode === 'edit' && $this->existing_logo) {
            // Mark for deletion on save
            $this->existing_logo = null;
        }
    }

    public function saveBrand()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ];

        try {
            if ($this->mode === 'edit') {
                $brand = Brand::find($this->brandId);

                if (!$brand) {
                    $this->dispatch('notify', [
                        'message' => 'Brand not found',
                        'type' => 'error'
                    ]);
                    return;
                }

                $oldData = collect($brand->toArray())->except(['updated_at'])->toArray();

                // Handle logo upload
                if ($this->logo) {
                    // Delete old logo if exists
                    if ($brand->logo) {
                        Storage::disk('public')->delete($brand->logo);
                    }
                    $path = $this->logo->store('brands', 'public');
                    $data['logo'] = $path;
                } elseif ($this->existing_logo === null && $brand->logo) {
                    // Logo was removed
                    Storage::disk('public')->delete($brand->logo);
                    $data['logo'] = null;
                }

                $brand->update($data);

                // Log activity
                logActivity('updated', $brand, $oldData, $data);

                $this->dispatch('notify', [
                    'message' => 'Brand updated successfully',
                    'type' => 'success'
                ]);
            } else {
                // Create new brand
                if ($this->logo) {
                    $path = $this->logo->store('brands', 'public');
                    $data['logo'] = $path;
                }

                $brand = Brand::create($data);

                // Log activity
                logActivity('created', $brand, [], $data);

                $this->dispatch('notify', [
                    'message' => 'Brand created successfully',
                    'type' => 'success'
                ]);
            }

            $this->dispatch('brandSaved');

            return $this->redirectRoute('brands.index', navigate: true);
            
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            Log::error('Brand save error: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return $this->redirectRoute('brands.index', navigate: true);
    }

    public function getBrandProperty()
    {
        if ($this->brandId) {
            return Brand::find($this->brandId);
        }
        return null;
    }

    public function render()
    {
        return view('livewire.brands.form', [
            'brand' => $this->brand,
        ])->layout('layouts.app');
    }
}