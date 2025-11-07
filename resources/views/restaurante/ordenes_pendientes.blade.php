@extends('app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold">Órdenes pendientes</h1>

    <ul>
        @foreach($ordenes as $orden)
            <li>#{{ $orden->id }} — {{ $orden->estado }} — Cliente: {{ $orden->cliente->name ?? 'N/A' }}</li>
        @endforeach
    </ul>
</div>
@endsection
