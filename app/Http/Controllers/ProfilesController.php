<?php

namespace App\Http\Controllers;

use App\Activity;
use App\User;

class ProfilesController extends Controller
{
    /**
     * Show the user's profile.
     *
     * @param  User $user
     * @return \Response
     */  
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
