<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Notifications\MyAccountDeleted;
use App\Notifications\UserCreated;
use App\Notifications\UserEdited;
use App\Notifications\WelcomeUser;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Password;

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
        event(new Registered($user));

        /* Notificación para admin */
        $user->notify(
            new WelcomeUser($user)
        );

        /* Notificación para admin */
        $admin = User::all()->filter(fn($u) => $u->isAdmin() );
        foreach($admin as $a){
            $a->notify(
            new UserCreated($user)
        );
        }

        return redirect()->route('inicio')->with('success', 'Bienvenido a Hikey, ' . $user->name . '!');
    }

    public function update(Request $request, User $user)
    {
        try{
            if(Gate::denies('updateAccountUser', $user))
            {
                abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
            }
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email',
            ]);

            // * si el usuario cambia el correo deberá verificarlo otra vez
            if ($request->email !== $user->email) {
                $user->email_verified_at = null;
            }

            $user->update($validated);

            /* notificación */
            $request->user()->notify(
                new UserEdited($user)
            );

            return redirect()->route('my-account.index', $user->id)->with('success', 'Información de usuario actualizada exitosamente!');
        }catch(\Exception $e){
            throw $e;
        }catch(\Exception $e){
            //si los datos no se guardan, se muestra mensaje de error
            return redirect()->route('my-account.index', $user->id)->with('error', 'No se actualizo la información del usuario.')->withInput();//withInput mantiene los datos del form
        }
    }

    public function destroy(User $user)
    {
        if(Gate::denies('deleteAccountUser', $user))
        {
            abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
        }

        /* notificación */
        $user->notify(
            new MyAccountDeleted($user)
        );

        $user->forceDelete();
        return redirect('/');
    }
}
