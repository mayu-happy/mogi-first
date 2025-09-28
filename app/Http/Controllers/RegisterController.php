<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showForm()
    {
        return view('auth.register');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:20'],
            'email'                 => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'              => ['required', 'confirmed', 'min:8'],
        ], []);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        auth()->login($user);

        return redirect()->route('mypage.profile');
    }
}
