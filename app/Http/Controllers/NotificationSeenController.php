<?php

namespace App\Http\Controllers;

/* /* use Illuminate\Http\Request; */
use Illuminate\Support\Facades\Auth;

class NotificationSeenController extends Controller
{
    //
    public function __invoke($id)
{
    $notification = Auth::user()->notifications()->findOrFail($id); //busca el id del usuario y lo relaciona con el id de la notificación
    //notifications() viene del use Notifiable

    $notification->markAsRead();
    return back()->with('success', 'Notificación marcada como vista.');
}
}
