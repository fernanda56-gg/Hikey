<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;

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
}
