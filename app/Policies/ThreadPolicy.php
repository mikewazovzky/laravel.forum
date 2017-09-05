<?php

namespace App\Policies;

use App\User;
use App\Thread;
use Illuminate\Auth\Access\HandlesAuthorization;

class ThreadPolicy
{
    use HandlesAuthorization;

    // If before() method return true there is no additional checks,
    // all the other method are ignored
    // We moved this check to AuthServiceProvider to be applied to all Policies
    // public function before(User $user)
    // {
    //     if ($user->name === 'Alex') {
    //         return true;
    //     }
    // }

    /**
     * Determine whether the user can update the thread.
     *
     * @param  \App\User    $user
     * @param  \App\Thread  $thread
     * @return bool
     */
    public function update(User $user, Thread $thread)
    {
        return $thread->user_id == $user->id;
    }
}
