<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Response as InertiaResponse;

class NotificationController extends Controller
{
    //

    public function index(Request $request) : InertiaResponse
    {
        return inertia('Notification/NotificationIndex', [
            'notifications' => $request->user()->notifications()->paginate(10)
        ]);
    }

    public function destroy()
    {
        $user = Auth::user();
        $user->notifications()->delete();

        return redirect()->back()->with('success', 'Notificaciones eliminadas con éxito');
    }
}
