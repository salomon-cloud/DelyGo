@extends('app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold">Confirmar contraseña</h1>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf
        <div>
            <label for="password">Contraseña</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" />
        </div>
        <div class="mt-4">
            <button type="submit">Confirmar</button>
        </div>
    </form>
</div>
@endsection
