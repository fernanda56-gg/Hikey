<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ManageAccountsController extends Controller
{
    //
    public function index ()
    {
        return inertia('ManageAccountUsers/IndexPage');
    }
}
