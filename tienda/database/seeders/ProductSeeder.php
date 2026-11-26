<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $mascaras = Category::create(['name' => 'Máscaras', 'slug' => 'mascaras']);
        $ceramica = Category::create(['name' => 'Cerámica', 'slug' => 'ceramica']);
        $textiles = Category::create(['name' => 'Textiles', 'slug' => 'textiles']);

        Product::create([
            'category_id' => $mascaras->id,
            'name' => 'Máscara ceremonial',
            'slug' => 'mascara-ceremonial',
            'description' => 'Máscara de inspiración maya tallada y pintada a mano.',
            'price' => 850.00,
            'stock' => 5,
            'model_path' => 'models/mascara.glb',
        ]);

        Product::create([
            'category_id' => $ceramica->id,
            'name' => 'Vasija de barro',
            'slug' => 'vasija-de-barro',
            'description' => 'Vasija de barro con grabados tradicionales.',
            'price' => 620.00,
            'stock' => 8,
            'model_path' => 'models/vasija.glb',
        ]);

        Product::create([
            'category_id' => $textiles->id,
            'name' => 'Hamaca tejida',
            'slug' => 'hamaca-tejida',
            'description' => 'Hamaca tejida a mano con hilo de colores.',
            'price' => 1200.00,
            'stock' => 3,
            'model_path' => 'models/hamaca.glb',
        ]);
    }
}