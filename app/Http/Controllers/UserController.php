<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

use Illuminate\Http\Request;

class UserController extends Controller
{
    //Form para crear un nuevo usuario
    public function create (){
        return inertia('UserAccount/CreatePage');
    }

    //Guarda la info de nuevo usuario
    public function store(Request $request){
        $user = User::make($request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(12)
                    ->letters() //checa que tenga letras
                    ->mixedCase() //al menos 1 mayúscula y 1 minúscula
                    ->numbers() //al menos 1 numero
                    ->symbols() //al menos un símbolo
                    ->uncompromised(),],
        ]));
        $user->save();
        $user->assignRole('user'); //se asigna rol de usuario por defecto
        Auth::login($user);

        return redirect()->route('inicio')->with('success', 'Bienvenido a Hikey, ' . $user->name . '!');
    }
}
