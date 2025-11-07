@extends('app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold">Productos del restaurante</h1>

    <ul>
        @foreach($productos as $producto)
            <li>{{ $producto->nombre }} — {{ $producto->precio }}</li>
        @endforeach
    </ul>

    <div class="mt-4">
        <a href="{{ route('dashboard') }}">Volver</a>
    </div>
</div>
@endsection
