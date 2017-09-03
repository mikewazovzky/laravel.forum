<?php

namespace App\Http\Controllers;

use App\Activity;
use App\User;
use Illuminate\Http\Request;

class ProfilesController extends Controller
{
    public function show(User $user)
    {
        return view('profiles.show', [
            'profileUser' => $user,
            'activities' => Activity::feed($user),
        ]);
    }

    // This functionality moved to Activity::feed method
    // public function getActivities(User $user)
    // {
    //     return  $user->activities()
    //         ->latest()
    //         ->with('subject')
    //         ->take(25)
    //         ->get()
    //         ->groupBy(function($activity) {
    //             return $activity->created_at->format('Y-m-d');
    //         }
    //     );
    // }
}
