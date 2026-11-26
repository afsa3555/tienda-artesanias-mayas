<div>
    <h1 class="text-3xl font-bold text-stone-900">Catálogo de artesanías</h1>
    <p class="mt-1 text-stone-600">Piezas hechas a mano inspiradas en la cultura maya.</p>

    <div class="mt-6 flex flex-wrap items-end gap-4 rounded-lg border border-stone-200 bg-white p-4">
        <div>
            <label class="block text-sm font-medium text-stone-700">Categoría</label>
            <select wire:model.live="category" class="mt-1 rounded border-stone-300 text-sm">
                <option value="">Todas</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-stone-700">Precio mínimo</label>
            <input type="number" min="0" wire:model.live.debounce.500ms="minPrice"
                   class="mt-1 w-28 rounded border-stone-300 text-sm" placeholder="0">
        </div>

        <div>
            <label class="block text-sm font-medium text-stone-700">Precio máximo</label>
            <input type="number" min="0" wire:model.live.debounce.500ms="maxPrice"
                   class="mt-1 w-28 rounded border-stone-300 text-sm" placeholder="2000">
        </div>

        <button wire:click="clearFilters" type="button"
                class="rounded border border-stone-300 px-3 py-2 text-sm hover:bg-stone-100">
            Limpiar filtros
        </button>
    </div>

    <p class="mt-4 text-sm text-stone-500">{{ $products->count() }} producto(s) encontrado(s)</p>

    <div class="mt-4 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($products as $product)
            <article class="overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm">
                <div class="flex aspect-square items-center justify-center bg-stone-200 text-stone-500">
                    Modelo 3D próximamente
                </div>
                <div class="p-4">
                    <p class="text-xs uppercase tracking-wide text-amber-700">{{ $product->category->name }}</p>
                    <h2 class="mt-1 text-lg font-semibold">{{ $product->name }}</h2>
                    <p class="mt-1 text-sm text-stone-600">{{ $product->description }}</p>
                    <p class="mt-3 text-xl font-bold">${{ number_format($product->price, 2) }} MXN</p>
                </div>
            </article>
        @empty
            <p class="col-span-full rounded border border-dashed border-stone-300 p-8 text-center text-stone-500">
                No hay productos con esos filtros.
            </p>
        @endforelse
    </div>
</div>