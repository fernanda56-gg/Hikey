<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegisterController extends Controller
{
    //
    public function index(){
        return inertia('Login/RegisterPage');
    }

    public function show(){
        return inertia('Home/HomePage');
    }
}
