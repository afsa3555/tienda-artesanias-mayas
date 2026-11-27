<?php

namespace App\Livewire;

use App\Support\Cart as CartStore;
use Livewire\Component;

class Cart extends Component
{
    public function increment(int $id): void
    {
        CartStore::set($id, (CartStore::items()[$id] ?? 0) + 1);
        $this->dispatch('cart-updated');
    }

    public function decrement(int $id): void
    {
        CartStore::set($id, (CartStore::items()[$id] ?? 0) - 1);
        $this->dispatch('cart-updated');
    }

    public function remove(int $id): void
    {
        CartStore::remove($id);
        $this->dispatch('cart-updated');
    }

    public function render()
    {
        return view('livewire.cart', [
            'lines' => CartStore::lines(),
            'total' => CartStore::total(),
        ]);
    }
}