<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use App\Support\Cart; 
use Livewire\Component;

class Catalog extends Component
{
    public string $category = '';
    public ?string $minPrice = null;
    public ?string $maxPrice = null;

    public function clearFilters(): void
    {
        $this->reset(['category', 'minPrice', 'maxPrice']);
    }
    public function addToCart(int $id): void
    {
        Cart::add($id);
        $this->dispatch('cart-updated');
    }

    public function render()
    {
        $products = Product::with('category')
            ->when($this->category !== '', fn ($q) => $q->where('category_id', $this->category))
            ->when(is_numeric($this->minPrice), fn ($q) => $q->where('price', '>=', $this->minPrice))
            ->when(is_numeric($this->maxPrice), fn ($q) => $q->where('price', '<=', $this->maxPrice))
            ->orderBy('name')
            ->get();

        return view('livewire.catalog', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }
}