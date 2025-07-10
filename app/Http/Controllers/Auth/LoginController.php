<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = \App\Models\User::where('email', $credentials['email'])->first();

        if (!$user) {
            $errors = ['email' => ['This email is not registered.']];
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'errors' => $errors], 422);
            }
            return back()->withErrors($errors)->onlyInput('email');
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            $errors = ['password' => ['The password you entered is incorrect.']];
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'errors' => $errors], 422);
            }
            return back()->withErrors($errors)->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'redirect' => route('tickets.index')]);
            }
            return redirect()->intended('tickets');
        }

        $errors = ['auth' => ['Login failed. Please try again.']];
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => false, 'errors' => $errors], 422);
        }
        return back()->withErrors($errors)->onlyInput('email');
    }
} 