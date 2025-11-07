@extends('app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold">Restablecer contraseña</h1>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token ?? '' }}">
        <div>
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required />
        </div>
        <div class="mt-4">
            <label for="password">Nueva contraseña</label>
            <input id="password" type="password" name="password" required />
        </div>
        <div class="mt-4">
            <label for="password_confirmation">Confirmar contraseña</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required />
        </div>
        <div class="mt-4">
            <button type="submit">Restablecer contraseña</button>
        </div>
    </form>
</div>
@endsection
