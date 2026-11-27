<x-shop-layout title="Acceso administrador">
    <div class="mx-auto max-w-sm">
        <h1 class="text-2xl font-bold">Acceso administrador</h1>
        <form method="POST" action="{{ route('admin.login.store') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium">Correo</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="mt-1 w-full rounded border-stone-300">
                @error('email')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Contraseña</label>
                <input type="password" name="password" required class="mt-1 w-full rounded border-stone-300">
            </div>
            <button type="submit" class="w-full rounded bg-amber-800 py-2 text-white hover:bg-amber-700">Entrar</button>
        </form>
    </div>
</x-shop-layout>