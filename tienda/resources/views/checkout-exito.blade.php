<x-shop-layout title="Pago exitoso">
    <div class="mx-auto max-w-2xl text-center">
        <h1 class="text-3xl font-bold text-green-700">¡Pago aprobado!</h1>
        <p class="mt-3">Pedido #{{ $order->id }} · Total ${{ number_format($order->total, 2) }} MXN</p>
        <p class="mt-1 text-stone-600">Gracias, {{ $order->customer_name }}.</p>
        <a href="/" class="mt-6 inline-block text-amber-800 underline">Volver al catálogo</a>
    </div>
</x-shop-layout>