<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserAvatarController extends Controller
{
    public function store()
    {
        $this->validate(request(), [
            'avatar' => ['required', 'image']
        ]);
        
        auth()->user()->update([
            'avatar_path' => request()->file('avatar')->store('avatars', 'public')
            // 'avatar_path' => request()->file('avatar')->storeAs('avatars', 'avatar.jpg', 'public')
        ]);      

        return back();  
    }
}
