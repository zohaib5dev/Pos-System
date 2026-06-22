<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;

class Show extends Component
{
    public $productId;
    public $product;

    public function mount($id)
    {
        $this->productId = $id;
        $this->loadProduct();
    }

    public function loadProduct()
    {
        $this->product = Product::with(['category', 'brand', 'unit', 'creator'])
            ->withCount('orderItems')
            ->find($this->productId);

        if (!$this->product) {
            session()->flash('error', 'Product not found');
            return $this->redirectRoute('products.index', navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.products.show', [
            'product' => $this->product,
        ])->layout('layouts.app');
    }
}