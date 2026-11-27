<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Tu carrito</h1>

    @if ($lines->isEmpty())
        <p class="text-gray-600">Tu carrito está vacío.</p>
        <a href="{{ route('catalogo') }}" class="text-amber-800 underline">Ir al catálogo</a>
    @else
        <div class="bg-white rounded-lg border divide-y">
            @foreach ($lines as $line)
                <div class="flex items-center justify-between p-4" wire:key="line-{{ $line['product']->id }}">
                    <div>
                        <p class="font-semibold">{{ $line['product']->name }}</p>
                        <p class="text-sm text-gray-500">${{ number_format($line['product']->price, 2) }} MXN c/u</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button wire:click="decrement({{ $line['product']->id }})" class="px-2 border rounded">−</button>
                        <span>{{ $line['qty'] }}</span>
                        <button wire:click="increment({{ $line['product']->id }})" class="px-2 border rounded">+</button>
                        <span class="w-28 text-right font-semibold">${{ number_format($line['subtotal'], 2) }}</span>
                        <button wire:click="remove({{ $line['product']->id }})" class="text-red-600 text-sm">Quitar</button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex justify-between items-center mt-6">
            <p class="text-2xl font-bold">Total: ${{ number_format($total, 2) }} MXN</p>
            <a href="{{ route('checkout') }}" class="bg-amber-800 text-white px-6 py-2 rounded">Continuar al pago</a>
        </div>
    @endif
</div>