<?php

namespace App;

use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class CustomRegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        if ($user->role === 'seller') {
            return new RedirectResponse(route('seller.dashboard'));
        }

        // Default redirect for other roles (e.g., client)
        return new RedirectResponse(route('dashboard'));
    }
}