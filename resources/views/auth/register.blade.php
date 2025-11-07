@extends('app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold">Registro</h1>

    <form method="POST" action="{{ route('register') }}" class="mt-6">
        @csrf
        <div>
            <label for="name">Nombre</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus />
        </div>
        <div class="mt-4">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required />
        </div>
        <div class="mt-4">
            <label for="password">Contraseña</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" />
        </div>
        <div class="mt-4">
            <label for="password_confirmation">Confirmar contraseña</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required />
        </div>
        <div class="mt-4">
            <button type="submit">Registrarse</button>
        </div>
    </form>

    <div class="mt-4">
        <a href="{{ route('login') }}">¿Ya tienes cuenta? Entra</a>
    </div>
</div>
@endsection
