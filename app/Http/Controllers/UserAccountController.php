<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        if(Gate::denies('viewAny', User::class))
        {
            abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
        }

        $userAccounts = User::with('roles')->get();

        $userAccounts->each(function ($userAccount) use ($user) {
            $userAccount->update_c = $user->can('update', $userAccount);
            $userAccount->delete_c = $user->can('delete', $userAccount);
        });

        return inertia('ManageAccountUsers/IndexPage', [
            'userAccounts' => $userAccounts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if(Gate::denies('create', User::class))
        {
            abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
        }

        return inertia('ManageAccountUsers/CreatePage', [
            'roles' => Role::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
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
            'roles' => 'required|array|max:1', //asegura que el rol es array
            'roles.*' => 'integer|exists:roles,id', //checa que el rol exista en la bd
            ]));
            $user->assignRole($request->input('roles'));
            $user->save();
            return redirect()->route('manage-account.index')->with('success', 'Usuario creado exitosamente.');
        }catch(\Exception $e){
            throw $e;
        }catch(\Exception $e){
            //si los datos no se guardan, se muestra mensaje de error
            return redirect()->back()->with('error', 'No se agrego al usuario.')->withInput();//withInput mantiene los datos del form
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request,)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        if(Gate::denies('update', $user))
        {
            abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
        }

        return inertia('ManageAccountUsers/EditPage', [
            'userAccount' => $user,
            'roles' => Role::all(),
            'userRoles' => $user->roles->pluck('id')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        try{
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'roles' => 'required|array|max:1', //asegura que el rol es array
            'roles.*' => 'integer|exists:roles,id', //checa que el rol exista en la bd
            ]);

            $user->update($validated);
            $user->syncRoles($validated['roles']);


            return redirect()->route('manage-account.index')->with('success', 'Usuario actualizado exitosamente.');
        }catch(\Exception $e){
            throw $e;
        }catch(\Exception $e){
            //si los datos no se guardan, se muestra mensaje de error
            return redirect()->back()->with('error', 'No se actualizo el usuario.')->withInput();//withInput mantiene los datos del form
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if(Gate::denies('delete', $user))
        {
            abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
        }

        $user->delete();
        return redirect()->route('manage-account.index')->with('success', 'Usuario eliminado exitosamente.');
    }
}
