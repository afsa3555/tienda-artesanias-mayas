<x-shop-layout title="Pedidos">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold">Panel de pedidos</h1>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button class="text-sm text-stone-600 underline">Cerrar sesión</button>
        </form>
    </div>

    <p class="mt-1 text-sm text-stone-500">{{ $orders->count() }} pedido(s)</p>

    <div class="mt-6 space-y-4">
        @forelse ($orders as $order)
            <div class="rounded-lg border bg-white p-4">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <p class="font-semibold">Pedido #{{ $order->id }} · {{ $order->customer_name }}</p>
                        <p class="text-sm text-stone-500">{{ $order->customer_email }} · {{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <p class="text-lg font-bold">${{ number_format($order->total, 2) }} MXN</p>
                </div>

                <ul class="mt-3 text-sm text-stone-700">
                    @foreach ($order->items as $item)
                        <li>{{ $item->product->name ?? 'Producto eliminado' }} × {{ $item->quantity }}
                            (${{ number_format($item->unit_price, 2) }} c/u)</li>
                    @endforeach
                </ul>

                <form method="POST" action="{{ route('admin.estado', $order) }}" class="mt-3 flex items-center gap-2">
                    @csrf
                    <select name="status" class="rounded border-stone-300 text-sm">
                        @foreach (['pendiente', 'pagado', 'enviado', 'cancelado'] as $s)
                            <option value="{{ $s }}" @selected($order->status === $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                    <button class="rounded bg-stone-800 px-3 py-1 text-sm text-white">Actualizar</button>
                </form>
            </div>
        @empty
            <p class="rounded border border-dashed p-8 text-center text-stone-500">Aún no hay pedidos.</p>
        @endforelse
    </div>
</x-shop-layout>