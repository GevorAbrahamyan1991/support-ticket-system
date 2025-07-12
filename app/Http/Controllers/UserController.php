<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->isAgent()) {
            abort(403, 'Unauthorized.');
        }
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }
} 