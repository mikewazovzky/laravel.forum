<?php

namespace App\Listeners;

use App\Events\ThreadReceivedNewReply;
use App\Notifications\YouWereMentioned;
use App\User;

class NotifyMentionedUser
{
    /**
     * Handle the event.
     *
     * @param  ThreadReceivedNewReply  $event
     * @return void
     */
    public function handle(ThreadReceivedNewReply $event)
    {
        User::whereIn('name', $event->reply->mentionedUsers())
            ->get()
            ->each(function($user) use ($event) {
                if($user) {
                    $user->notify(new YouWereMentioned($event->reply));
                }                
            });

        // collect($event->reply->mentionedUsers())
        //     ->map(function($name) {
        //         return User::whereName($name)->first();
        //     })
        //     ->filter()  // filter without arguments removes null values from collection
        //     ->each(function($user) use ($event) {
        //         if($user) {
        //             $user->notify(new YouWereMentioned($event->reply));
        //         }                
        //     });
    }
}
