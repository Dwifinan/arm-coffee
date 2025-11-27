<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $user = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

       if( Auth::attempt($user)){
        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
       }
       return back();

    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

}
