<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(User $user): View
    {
        return view('profile.show', [
            'profile' => $user,
        ]);
    }
}
