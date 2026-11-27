<x-shop-layout title="Video promocional">
    <div class="mx-auto max-w-4xl">
        <h1 class="text-3xl font-bold">Conoce Artesanías Mayas</h1>
        <p class="mt-1 text-stone-600">Piezas hechas a mano, con visor 3D, realidad aumentada y recorrido virtual.</p>
        <video controls preload="metadata" class="mt-6 w-full rounded-lg border border-stone-200 bg-black">
            <source src="{{ asset('media/promocional.mp4') }}" type="video/mp4">
            Tu navegador no admite video.
        </video>
    </div>
</x-shop-layout>