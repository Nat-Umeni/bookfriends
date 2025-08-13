<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{


    public function store()
    {
        $user = User::create(request()->validate([
            'name' => 'required|string|min:1',
            'email' => 'required|email|unique:users',
            'password' => ['required', 'confirmed', 'min:6'],
        ]));

        Auth::login($user);

        return redirect()->route('home');
    }
}
