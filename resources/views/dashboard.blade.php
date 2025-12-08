@extends('app')

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold">Dashboard</h1>
        <p class="mt-2">BIENVENIDO</p>
        @auth
            <div class="mt-6 grid grid-cols-1 gap-4" style="max-width:800px;">
                @if (auth()->user()->rol === 'admin')
                    <div>
                        <button id="open-users-modal" class="px-4 py-2 bg-blue-600 text-white rounded">VER USUARIOS</button>
                        <a href="{{ route('admin.users') }}" class="ml-3 px-3 py-2 bg-gray-200 rounded">Panel usuarios</a>
                        <a href="{{ route('admin.asignacion') }}" class="ml-3 px-3 py-2 bg-gray-200 rounded">Asignación órdenes</a>
                    </div>
                @endif

                @if (auth()->user()->rol === 'cliente')
                    <div>
                        <a href="{{ route('cliente.orden.create') }}" class="px-4 py-2 bg-green-600 text-white rounded">Crear orden</a>
                    </div>
                @endif

                @if (auth()->user()->rol === 'restaurante')
                    <div>
                        <a href="{{ route('productos.index') }}" class="px-4 py-2 bg-indigo-600 text-white rounded">Mis productos</a>
                        <a href="{{ route('restaurante.ordenes.pendientes') }}" class="ml-3 px-3 py-2 bg-gray-200 rounded">Órdenes pendientes</a>
                    </div>
                @endif

                <div>
                    <a href="{{ route('profile.edit') }}" class="px-3 py-2 bg-gray-200 rounded">Mi perfil</a>
                </div>
            </div>
        @endauth
    </div>
    @include('components.users_modal')

    {{-- Cargar script que maneja el modal y llamadas AJAX --}}
    <script src="{{ asset('js/users_admin.js') }}" defer></script>
@endsection
