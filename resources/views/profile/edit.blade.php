@extends('app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold">Editar perfil</h1>

    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PATCH')
        <div>
            <label for="name">Nombre</label>
            <input id="name" type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required />
        </div>
        <div class="mt-4">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required />
        </div>
        <div class="mt-4">
            <button type="submit">Guardar</button>
        </div>
    </form>
</div>
@endsection
