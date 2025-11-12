@extends('app')

@section('content')
<div class="container mx-auto p-6 max-w-md">
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold">Iniciar sesión</h1>

        @if(session('status'))
            <div class="mt-4 text-sm text-gray-600">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="mt-6">
            @csrf
            <div class="mb-3">
                <label for="email" class="block text-sm font-medium">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full border p-2 rounded" />
            </div>
            <div class="mb-3">
                <label for="password" class="block text-sm font-medium">Contraseña</label>
                <input id="password" type="password" name="password" required autocomplete="current-password" class="w-full border p-2 rounded" />
            </div>
            <div class="flex items-center justify-between mb-3">
                <label class="text-sm">
                    <input type="checkbox" name="remember"> Recordarme
                </label>
                <a href="{{ route('password.request') }}" class="text-sm text-blue-600">¿Olvidaste tu contraseña?</a>
            </div>
            <div>
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded">Entrar</button>
            </div>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ route('register') }}" class="text-sm text-gray-700">¿No tienes cuenta? Regístrate</a>
        </div>
    </div>
</div>
@endsection
