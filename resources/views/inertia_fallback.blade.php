@extends('app')

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Página convertida: {{ $component }}</h1>

        <div class="bg-white p-4 rounded shadow">
            <h2 class="font-semibold">Props</h2>
            <pre class="text-sm overflow-auto bg-gray-100 p-3 rounded mt-2">{{ json_encode($props ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
        </div>

        <p class="text-gray-600 text-sm mt-4">Esta es una vista de transición generada automáticamente al eliminar Inertia/Vue. Reemplázala por una vista Blade específica cuando estés listo.</p>
    </div>
@endsection
