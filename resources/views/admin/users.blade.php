@extends('app')

@section('content')
<div class="container mx-auto p-6">
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Gestión de usuarios</h1>

        @if(session('status'))
            <div class="p-3 mb-4 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
        @endif

        <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 border">ID</th>
                    <th class="p-2 border">Nombre</th>
                    <th class="p-2 border">Email</th>
                    <th class="p-2 border">Rol</th>
                    <th class="p-2 border">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr class="odd:bg-white even:bg-gray-50">
                    <td class="p-2 border">{{ $user->id }}</td>
                    <td class="p-2 border">{{ $user->name }}</td>
                    <td class="p-2 border">{{ $user->email }}</td>
                    <td class="p-2 border">{{ $user->rol }}</td>
                    <td class="p-2 border">
                        <form action="{{ route('admin.users.updateRole', $user) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            <select name="rol" class="border p-1 rounded">
                                <option value="cliente" {{ $user->rol === 'cliente' ? 'selected' : '' }}>cliente</option>
                                <option value="restaurante" {{ $user->rol === 'restaurante' ? 'selected' : '' }}>restaurante</option>
                                <option value="repartidor" {{ $user->rol === 'repartidor' ? 'selected' : '' }}>repartidor</option>
                                <option value="admin" {{ $user->rol === 'admin' ? 'selected' : '' }}>admin</option>
                            </select>
                            <button type="submit" class="px-2 py-1 bg-blue-600 text-white rounded">Actualizar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection
