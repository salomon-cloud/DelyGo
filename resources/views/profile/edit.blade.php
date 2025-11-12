@extends('app')

@section('content')
<div class="container mx-auto p-6 max-w-md">
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold">Editar perfil</h1>

        <form method="POST" action="{{ route('profile.update') }}" class="mt-4">
            @csrf
            @method('PATCH')
            <div class="mb-3">
                <label for="name" class="block text-sm font-medium">Nombre</label>
                <input id="name" type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required class="w-full border p-2 rounded" />
            </div>
            <div class="mb-3">
                <label for="email" class="block text-sm font-medium">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required class="w-full border p-2 rounded" />
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection
