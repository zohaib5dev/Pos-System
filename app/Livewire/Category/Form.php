<?php

namespace App\Livewire\Category;

use Livewire\Component;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class Form extends Component
{
    // Mode
    public $mode = 'create'; // create, edit
    public $categoryId = null;

    // Form Fields
    public $name = '';
    public $slug = '';
    public $description = '';
    public $is_active = true;
    public $sort_order = 0;

    protected function rules()
    {
        $rules = [
            'name' => 'required|min:2|max:255',
            'description' => 'nullable|max:1000',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
        ];

        // Add unique rule for slug with ignore for edit mode
        if ($this->mode === 'edit' && $this->categoryId) {
            $rules['slug'] = 'required|max:255|unique:categories,slug,' . $this->categoryId;
        } else {
            $rules['slug'] = 'required|max:255|unique:categories,slug';
        }

        return $rules;
    }

    public function mount($id = null)
    {
        if ($id) {
            $this->mode = 'edit';
            $this->categoryId = $id;
            $this->loadCategory();
        }
    }

    public function loadCategory()
    {
        $category = Category::find($this->categoryId);

        if ($category) {
            $this->name = $category->name;
            $this->slug = $category->slug;
            $this->description = $category->description;
            $this->is_active = $category->is_active;
            $this->sort_order = $category->sort_order;
        }
    }

    public function updatedName()
    {
        // Only auto-generate slug for new categories
        if ($this->mode === 'create') {
            $this->slug = Str::slug($this->name);
        }
    }

    public function generateSlug()
    {
        if ($this->name) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function saveCategory()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
        ];

        try {
            if ($this->mode === 'edit') {
                $category = Category::find($this->categoryId);

                if (!$category) {
                    $this->dispatch('notify', [
                        'message' => 'Category not found',
                        'type' => 'error'
                    ]);
                    return;
                }

                $oldData = collect($category->toArray())->except(['updated_at'])->toArray();
                $category->update($data);

                // Log activity
                logActivity('updated', $category, $oldData, $data);

                $this->dispatch('notify', [
                    'message' => 'Category updated successfully',
                    'type' => 'success'
                ]);
            } else {
                // Create new category
                $data['created_by'] = auth()->id();
                $category = Category::create($data);

                // Log activity
                logActivity('created', $category, [], $data);

                $this->dispatch('notify', [
                    'message' => 'Category created successfully',
                    'type' => 'success'
                ]);
            }

            $this->dispatch('categorySaved');

            // redirectRoute to index
            return $this->redirectRoute('categories.index', navigate: true);
            
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            Log::error('Category save error: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return $this->redirectRoute('categories.index', navigate: true);
    }

    public function getCategoryProperty()
    {
        if ($this->categoryId) {
            return Category::find($this->categoryId);
        }
        return null;
    }

    public function generateSlugFromName($name) 
    {
    }

    public function render()
    {
        return view('livewire.categories.form', [
            'category' => $this->category,
        ])->layout('layouts.app');
    }
}