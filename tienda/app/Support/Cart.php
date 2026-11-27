<?php

namespace App\Support;

use App\Models\Product;

class Cart
{
    public static function items(): array
    {
        return session('cart', []);
    }

    public static function add(int $id, int $qty = 1): void
    {
        $product = Product::findOrFail($id);
        self::set($id, (self::items()[$id] ?? 0) + $qty);
    }

    public static function set(int $id, int $qty): void
    {
        $product = Product::findOrFail($id);
        $qty = min($qty, $product->stock);
        $cart = self::items();
        if ($qty <= 0) {
            unset($cart[$id]);
        } else {
            $cart[$id] = $qty;
        }
        session(['cart' => $cart]);
    }

    public static function remove(int $id): void
    {
        $cart = self::items();
        unset($cart[$id]);
        session(['cart' => $cart]);
    }

    public static function clear(): void
    {
        session()->forget('cart');
    }

    public static function count(): int
    {
        return array_sum(self::items());
    }

    public static function lines()
    {
        $products = Product::whereIn('id', array_keys(self::items()))->get()->keyBy('id');

        return collect(self::items())->map(fn ($qty, $id) => [
            'product'  => $products[$id],
            'qty'      => $qty,
            'subtotal' => $products[$id]->price * $qty,
        ])->filter(fn ($l) => $l['product'])->values();
    }

    public static function total(): float
    {
        return (float) self::lines()->sum('subtotal');
    }
}