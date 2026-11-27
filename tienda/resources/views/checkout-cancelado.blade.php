<x-shop-layout title="Pago no completado">
    <div class="mx-auto max-w-2xl text-center">
        <h1 class="text-3xl font-bold text-red-700">Pago no completado</h1>
        <p class="mt-3">El pedido #{{ $order->id }} no se pagó (tarjeta rechazada o pago cancelado). Tu carrito sigue intacto.</p>
        <a href="{{ route('carrito') }}" class="mt-6 inline-block rounded bg-amber-800 px-6 py-2 text-white">Volver al carrito e intentar de nuevo</a>
    </div>
</x-shop-layout>