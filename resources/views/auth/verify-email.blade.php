@extends('app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold">Verifica tu email</h1>

    @if(session('status'))
        <div class="mt-4 text-sm text-gray-600">{{ session('status') }}</div>
    @endif

    <p class="mt-4">Por favor, revisa tu bandeja de entrada y haz clic en el enlace de verificación.</p>

    <form method="POST" action="{{ route('verification.send') }}" class="mt-4">
        @csrf
        <button type="submit">Reenviar correo de verificación</button>
    </form>
</div>
@endsection
