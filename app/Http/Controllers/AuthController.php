<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    //Para generar el form del login del usuario
    public function create(){
        return inertia('Auth/LoginPage');
    }

    //Para almacenar y validar la info del form
    public function store (Request $request) {
        if(!Auth::attempt($request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]), true)){
            throw ValidationException::withMessages([
                'email' => 'Datos incorrectos, inténtalo de nuevo.'
            ]);
        }
        $request->session()->regenerate();

        return redirect()->intended('/home');
    }

    //Destruye la sesión del usuario
    public function destroy() {

    }
}
