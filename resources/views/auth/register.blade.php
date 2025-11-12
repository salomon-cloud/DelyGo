@extends('app')

@section('content')
<div class="container mx-auto p-6 max-w-md">
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold">Registro</h1>

        @if ($errors->any())
            <div class="mb-4 text-red-600">
                <strong>Se encontraron errores:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('status'))
            <div class="mb-4 text-green-600">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="mt-6">
            @csrf
            <div class="mb-3">
                <label for="name" class="block text-sm font-medium">Nombre</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="w-full border p-2 rounded" />
            </div>
            <div class="mb-3">
                <label for="email" class="block text-sm font-medium">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required class="w-full border p-2 rounded" />
            </div>
            <div class="mb-3">
                <label for="password" class="block text-sm font-medium">Contraseña</label>
                <input id="password" type="password" name="password" required autocomplete="new-password" class="w-full border p-2 rounded" />
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="block text-sm font-medium">Confirmar contraseña</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required class="w-full border p-2 rounded" />
            </div>
            <div>
                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded">Registrarse</button>
            </div>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ route('login') }}" class="text-sm text-gray-700">¿Ya tienes cuenta? Entra</a>
        </div>
    </div>
</div>
@endsection