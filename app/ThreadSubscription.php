<?php

namespace App;

use App\Notifications\ThreadWasUpdated;
use Illuminate\Database\Eloquent\Model;

class ThreadSubscription extends Model
{
    /**
     * Don\t auto-apply mass assignment protection.
     *
     * @var array
     */
    protected $guarded = [];
    /**
     * A thread subscription belongs to a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A thread subscription belongs to a thread.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function thrad()
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Creates notification for the subscribed user.
     */
    public function notify($reply)
    {
        $this->user->notify(new ThreadWasUpdated($this->thread, $reply));
    }
}
