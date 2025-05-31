<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user || !$user->role) {
            abort(403, 'User role not defined.');
        }

        $role = strtolower($user->role);
        if ($role === 'client') {
            return view('dashboard', ['component' => 'client-dashboard']);
        } elseif ($role === 'seller') {
            return view('dashboard', ['component' => 'seller-dashboard']);
        }

        abort(403, 'Invalid role.');
    }
}