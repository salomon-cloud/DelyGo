@extends('app')

@section('content')
<div class="container">
    <h1>Gestión de usuarios</h1>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->rol }}</td>
                    <td>
                        <form action="{{ route('admin.users.updateRole', $user) }}" method="POST">
                            @csrf
                            <select name="rol">
                                <option value="cliente" {{ $user->rol === 'cliente' ? 'selected' : '' }}>cliente</option>
                                <option value="restaurante" {{ $user->rol === 'restaurante' ? 'selected' : '' }}>restaurante</option>
                                <option value="repartidor" {{ $user->rol === 'repartidor' ? 'selected' : '' }}>repartidor</option>
                                <option value="admin" {{ $user->rol === 'admin' ? 'selected' : '' }}>admin</option>
                            </select>
                            <button type="submit">Actualizar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
