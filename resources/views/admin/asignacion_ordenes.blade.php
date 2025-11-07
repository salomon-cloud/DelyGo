@extends('app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold">Asignación de órdenes</h1>

    <h2 class="mt-4">Órdenes</h2>
    <ul>
        @foreach($ordenes as $orden)
            <li>#{{ $orden->id }} — {{ $orden->estado }} — Restaurante: {{ $orden->restaurante->nombre ?? 'N/A' }}</li>
        @endforeach
    </ul>

    <h2 class="mt-4">Repartidores</h2>
    <ul>
        @foreach($repartidores as $r)
            <li>{{ $r->name }} (ID: {{ $r->id }})</li>
        @endforeach
    </ul>
</div>
@endsection
