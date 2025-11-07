<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
// Inertia removed: using Blade views instead

// 1. 🎯 IMPORTAR la clase UserFactory que implementa el Factory Method
use App\Services\UserFactory; 

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validación estándar de Laravel Breeze
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // -----------------------------------------------------------
        // INICIO DE LA IMPLEMENTACIÓN DEL PATRÓN FACTORY METHOD
        // -----------------------------------------------------------
        
        // Llamamos al método estático de la fábrica en lugar de User::create()
        // Asignamos el rol 'cliente' por defecto para el registro público
        $user = UserFactory::crearUsuario('cliente', [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // Nota: No necesitamos el campo 'rol' aquí, la fábrica se encarga de asignarlo.
        ]);

        // -----------------------------------------------------------
        // FIN DE LA IMPLEMENTACIÓN DEL PATRÓN FACTORY METHOD
        // -----------------------------------------------------------

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}