<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    //Para generar el form del login del usuario
    public function create(){
        return inertia('Auth/LoginPage');
    }

    //Para almacenar y validar la info del form
    public function store () {

    }

    //Destruye la sesión del usuario
    public function destroy() {

    }
}
