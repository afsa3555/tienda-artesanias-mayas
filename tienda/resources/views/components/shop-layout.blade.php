@props(['title' => 'Inicio'])
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} · Artesanías Mayas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-stone-50 text-stone-800">
    <header class="border-b border-stone-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4">
            <a href="/" class="text-xl font-bold text-amber-800">Artesanías Mayas</a>
            <nav class="flex gap-4 text-sm">
                <a href="/" class="hover:text-amber-700">Catálogo</a>
                <a href="/recorrido" class="hover:text-amber-700">Recorrido VR</a>
                <a href="/carrito" class="hover:text-amber-700">Carrito (<livewire:cart-counter />)</a>
</nav>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-8">
        {{ $slot }}
    </main>

    <footer class="mt-12 border-t border-stone-200 py-6 text-center text-sm text-stone-500">
        Proyecto Integrador · Universidad Politécnica de Quintana Roo
    </footer>

    @livewireScripts
</body>
</html>