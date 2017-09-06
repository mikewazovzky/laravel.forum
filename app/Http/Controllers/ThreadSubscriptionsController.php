<?php

namespace App\Http\Controllers;

use App\Thread;
use Illuminate\Http\Request;

class ThreadSubscriptionsController extends Controller
{
    /**
     * Create a new ThreadSubscriptionsController instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Persist a new tread subscription.
     *
     * @param  integer  $channelId
     * @param  Thread   $thread
     * @return \Illuminate\Database\Eloquent\Model | \Illuminate\Http\RedirectResponse
     */
    public function store($channelId, Thread $thread)
    {
        $thread->subscribe(auth()->user());
    }

    /**
     * Remove the specified tread subscription from storage.
     *
     * @param  integer      $channelId
     * @param  \App\Thread  $thread
     * @return void
     */
    public function destroy($channelId, Thread $thread)
    {
        $thread->unsubscribe(auth()->user());
    }
}
