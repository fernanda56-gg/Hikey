<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\UpdatePassword;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class MyAccountController extends Controller
{

    public function index(User $user)
    {
        if(Gate::denies('viewAccountUser', $user))
        {
            abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
        }
        return inertia('MyAccount/IndexPage', [
            'user' => $user,
        ]);
    }

    public function update(Request $request, User $user)
    {
        if(Gate::denies('updatePassword', $user))
        {
            abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
        }

        /* se valida la contraseña */
        $request->validate([
            'current_password' => 'required|current_password', //? el sistema verifica que la contraseña si no coincide marcara error y no dejara modificarla
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
        ]);


        /* se actualiza la contraseña */
        try {
            $user->update([
                'password' => Hash::make($request->input('password')),
            ]);

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $user->notify(new UpdatePassword($user));

            return redirect()->route('login');

    } catch (\Exception $e) {
        return back()->withErrors('error', 'No se pudo actualizar la contraseña intentalo de nuevo');
    }
    }
}
