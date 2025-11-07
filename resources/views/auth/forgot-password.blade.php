@extends('app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold">Recuperar contraseña</h1>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div>
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required />
        </div>
        <div class="mt-4">
            <button type="submit">Enviar enlace de recuperación</button>
        </div>
    </form>
</div>
@endsection
