@extends('app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold">Iniciar sesión</h1>

    @if(session('status'))
        <div class="mt-4 text-sm text-gray-600">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-6">
        @csrf
        <div>
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus />
        </div>
        <div class="mt-4">
            <label for="password">Contraseña</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" />
        </div>
        <div class="mt-4">
            <label>
                <input type="checkbox" name="remember"> Recordarme
            </label>
        </div>
        <div class="mt-4">
            <button type="submit">Entrar</button>
        </div>
    </form>

    <div class="mt-4">
        <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
    </div>

    <div class="mt-2">
        <a href="{{ route('register') }}">¿No tienes cuenta? Regístrate</a>
    </div>
</div>
@endsection
