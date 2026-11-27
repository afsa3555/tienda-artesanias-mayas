<x-shop-layout title="Pago">
    <div class="mx-auto max-w-2xl">
        <h1 class="text-3xl font-bold">Finalizar compra</h1>

        <div class="mt-6 divide-y rounded-lg border bg-white">
            @foreach ($lines as $line)
                <div class="flex justify-between p-4">
                    <span>{{ $line['product']->name }} × {{ $line['qty'] }}</span>
                    <span>${{ number_format($line['subtotal'], 2) }}</span>
                </div>
            @endforeach
            <div class="flex justify-between p-4 text-lg font-bold">
                <span>Total</span>
                <span>${{ number_format($total, 2) }} MXN</span>
            </div>
        </div>

        <form method="POST" action="{{ route('checkout.store') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium">Nombre completo</label>
                <input type="text" name="customer_name" value="{{ old('customer_name') }}" required
                       class="mt-1 w-full rounded border-stone-300">
                @error('customer_name')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Correo electrónico</label>
                <input type="email" name="customer_email" value="{{ old('customer_email') }}" required
                       class="mt-1 w-full rounded border-stone-300">
                @error('customer_email')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="w-full rounded bg-amber-800 py-3 text-white hover:bg-amber-700">
                Pagar con tarjeta
            </button>
            <p class="text-xs text-stone-500"></p>
        </form>
    </div>
</x-shop-layout>