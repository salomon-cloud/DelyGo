@extends('app')

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold">{{ config('app.name', 'Laravel') }}</h1>
        <p class="mt-4">Bienvenido.</p>

        <ul class="mt-4 list-disc pl-6 text-sm text-gray-700">
            <li>Inicia sesión en /login</li>
            <li>Panel: /dashboard (requiere autenticación)</li>
        </ul>
    </div>
@endsection
