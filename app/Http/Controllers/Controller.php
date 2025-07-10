<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    protected function authorizeRole(string|array $roles)
    {
        $user = Auth::user();
        if (is_string($roles)) {
            $roles = [$roles];
        }
        if (!$user || !in_array($user->role, $roles)) {
            abort(403, 'Unauthorized.');
        }
    }
}