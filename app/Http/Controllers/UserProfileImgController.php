<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserProfileImgController extends Controller
{
    public function store(Request $request, User $user)
    {
        try{
            if(Gate::denies('updateAccountUser', $user))
            {
                abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
            }
            $request->validate([
                'profile-photo' => 'required|image|mimes:png,jpg,jpeg,webp|max:2048'
            ]);

            /* si el usuario tiene otra foto de perfil la eliminara */
            if($user->profile_photo){
                Storage::disk('public')->delete($user->profile_photo);
            }

            $path = $request->file('profile-photo')->store('profile-photos', 'public');

            /* añade la img de foto de usuario ala tabla users */
            $user->update([
                    'profile_photo' => $path
                ]);
            return redirect()->back()->with('success', 'Foto de perfil actualizada');

        } catch (ValidationException $e) {
                throw $e;
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Error al subir imagen.');
            }
    }
}
