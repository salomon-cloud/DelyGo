<nav class="p-4 bg-white shadow">
    <div class="container">
        <a href="{{ url('/') }}" class="mr-4">Home</a>
        @auth
            <a href="{{ route('dashboard') }}" class="mr-2">Dashboard</a>
            <a href="{{ route('profile.edit') }}" class="mr-2">Perfil</a>
            <form method="POST" action="{{ route('logout') }}" style="display:inline">@csrf<button type="submit">Salir</button></form>
        @else
            <a href="{{ route('login') }}" class="mr-2">Entrar</a>
            <a href="{{ route('register') }}">Registro</a>
        @endauth
    </div>
</nav>
