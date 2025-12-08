@extends('app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold">Productos del restaurante</h1>

    @if(session('success'))
        <div class="p-3 mb-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif

    <div class="mb-6 p-4 bg-white rounded shadow">
        <h2 class="font-semibold mb-2">Agregar producto</h2>
        <form method="POST" action="{{ route('productos.store') }}">
            @csrf
            <div class="grid grid-cols-3 gap-3">
                <input name="nombre" placeholder="Nombre" class="border p-2 rounded" required />
                <input name="precio" type="number" step="0.01" placeholder="Precio" class="border p-2 rounded" required />
                <input name="descripcion" placeholder="Descripción" class="border p-2 rounded" />
            </div>
            <div class="mt-3">
                <button class="px-3 py-1 bg-green-600 text-white rounded">Crear producto</button>
            </div>
        </form>
    </div>

    <ul>
        @foreach($productos as $producto)
            <li class="p-2 bg-white rounded mb-2 shadow">{{ $producto->nombre }} — {{ $producto->precio }}</li>
        @endforeach
    </ul>

    <div class="mt-4">
        <a href="{{ route('dashboard') }}">Volver</a>
    </div>
</div>
@endsection
