<?php

namespace App\Livewire\Product;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Unit;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Form extends Component
{
    use WithFileUploads;

    // Mode
    public $mode = 'create'; // create, edit
    public $productId = null;

    // Form Fields
    public $name = '';
    public $slug = '';
    public $sku = '';
    public $barcode = '';
    public $description = '';
    public $category_id = '';
    public $brand_id = '';
    public $unit_id = '';
    public $purchase_price = 0;
    public $selling_price = 0;
    public $wholesale_price = 0;
    public $tax_rate = 0;
    public $tax_type = 'exclusive';
    public $stock_quantity = 0;
    public $low_stock_threshold = 5;
    public $allow_out_of_stock = false;
    public $is_active = true;
    public $is_featured = false;
    public $existingImage = null;
    public $tempImage = null;

    protected function rules()
    {
        $rules = [
            'name' => 'required|min:3|max:255',
            'description' => 'nullable|max:1000',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_id' => 'nullable|exists:units,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'tax_type' => 'required|in:exclusive,inclusive',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'allow_out_of_stock' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'tempImage' => 'nullable|image|max:2048',
        ];

        // Add unique rules with ignore for edit mode
        if ($this->mode === 'edit' && $this->productId) {
            $rules['slug'] = 'nullable|unique:products,slug,' . $this->productId;
            $rules['sku'] = 'required|unique:products,sku,' . $this->productId;
            $rules['barcode'] = 'nullable|unique:products,barcode,' . $this->productId;
        } else {
            $rules['slug'] = 'nullable|unique:products,slug';
            $rules['sku'] = 'required|unique:products,sku';
            $rules['barcode'] = 'nullable|unique:products,barcode';
        }

        return $rules;
    }

    public function mount($id = null)
    {
        if ($id) {
            $this->mode = 'edit';
            $this->productId = $id;
            $this->loadProduct();
        } else {
            $this->generateSku();
        }
    }

    public function loadProduct()
    {
        $product = Product::find($this->productId);

        if ($product) {
            $this->name = $product->name;
            $this->slug = $product->slug;
            $this->sku = $product->sku;
            $this->barcode = $product->barcode;
            $this->description = $product->description;
            $this->category_id = $product->category_id;
            $this->brand_id = $product->brand_id;
            $this->unit_id = $product->unit_id;
            $this->purchase_price = $product->purchase_price;
            $this->selling_price = $product->selling_price;
            $this->wholesale_price = $product->wholesale_price;
            $this->tax_rate = $product->tax_rate;
            $this->tax_type = $product->tax_type;
            $this->stock_quantity = $product->stock_quantity;
            $this->low_stock_threshold = $product->low_stock_threshold;
            $this->allow_out_of_stock = $product->allow_out_of_stock;
            $this->is_active = $product->is_active;
            $this->is_featured = $product->is_featured;
            $this->existingImage = $product->main_image;
        }
    }

    public function updatedName()
    {
        if ($this->mode === 'create' && empty($this->slug)) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function generateSku()
    {
        $this->sku = 'PRD-' . strtoupper(Str::random(6));
    }

    public function generateSlug()
    {
        if ($this->name) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function updatedTempImage()
    {
        $this->validate([
            'tempImage' => 'image|max:2048',
        ]);
    }

    public function removeTempImage()
    {
        $this->tempImage = null;
    }

    public function deleteImage()
    {
        if ($this->existingImage) {
            $destinationPath = public_path('assets/img/products');
            if (file_exists($destinationPath . '/' . $this->existingImage)) {
                unlink($destinationPath . '/' . $this->existingImage);
            }

            if ($this->productId) {
                $product = Product::find($this->productId);
                if ($product) {
                    $product->image = null;
                    $product->save();
                }
            }

            $this->existingImage = null;

            $this->dispatch('notify', [
                'message' => 'Image deleted successfully',
                'type' => 'success'
            ]);
        }
    }

    public function saveProduct()
    {
        $this->validate();

        // Generate slug if empty
        if (empty($this->slug) && !empty($this->name)) {
            $this->slug = Str::slug($this->name);
        }
$imagePath = $this->existingImage;
if ($this->tempImage) {
    // Delete old image if exists
    if ($imagePath && Storage::disk('public')->exists($imagePath)) {
        Storage::disk('public')->delete($imagePath);
    }
    
    // Store new image
    $imagePath = $this->tempImage->store('products', 'public');
}

    Log::info('Image path before save: ' . ($imagePath ?? 'null'));

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'description' => $this->description,
            'category_id' => $this->category_id ?: null,
            'brand_id' => $this->brand_id ?: null,
            'unit_id' => $this->unit_id ?: null,
            'purchase_price' => $this->purchase_price,
            'selling_price' => $this->selling_price,
            'wholesale_price' => $this->wholesale_price,
            'tax_rate' => $this->tax_rate,
            'tax_type' => $this->tax_type,
            'stock_quantity' => $this->stock_quantity,
            'low_stock_threshold' => $this->low_stock_threshold,
            'allow_out_of_stock' => $this->allow_out_of_stock,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'main_image' => $imagePath,
        ];

        try {
            if ($this->mode === 'edit') {
                $product = Product::find($this->productId);

                if (!$product) {
                    $this->dispatch('notify', [
                        'message' => 'Product not found',
                        'type' => 'error'
                    ]);
                    return;
                }

                $oldData = collect($product->toArray())->except(['updated_at'])->toArray();
                $product->update($data);

                // Log activity
                logActivity('updated', $product, $oldData, $data);

                $this->dispatch('notify', [
                    'message' => 'Product updated successfully',
                    'type' => 'success'
                ]);
            } else {
                $data['created_by'] = auth()->id();
                $product = Product::create($data);

                // Log activity
                logActivity('created', $product, [], $data);

                $this->dispatch('notify', [
                    'message' => 'Product created successfully',
                    'type' => 'success'
                ]);
            }

            $this->dispatch('productSaved');

            // Redirect to product details
            return $this->redirectRoute('products.show', ['id' => $product->id], navigate: true);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            Log::error('Product save error: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return $this->redirectRoute('products.index', navigate: true);
    }

    public function getProductProperty()
    {
        if ($this->productId) {
            return Product::find($this->productId);
        }
        return null;
    }

    public function generateSlugFromName($name) {}

    public function render()
    {
        return view('livewire.products.form', [
            'product' => $this->product,
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
            'brands' => Brand::where('is_active', true)->orderBy('name')->get(),
            'units' => Unit::where('is_active', true)->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
