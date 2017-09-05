<?php

namespace App\Http\Controllers;

use App\Thread;
use App\Reply;
use Illuminate\Http\Request;

class RepliesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function store($channelId, Thread $thread, Request $request)
    {
        $this->validate($request, [
            'body' => 'required'
        ]);

        $reply = $thread->addReply([
            'user_id' => auth()->id(),
            'body' => $request->body
        ]);

        if ($request->expectsJson()) {
            return $reply->load('owner');
        }

        return back()
            ->with('flash', 'Your reply has been posted!');
    }

    public function destroy(Reply $reply)
    {
        // if ($reply->user_id != auth()->id()) {
        //     return response([], 403);
        // }

        $this->authorize('update', $reply);

        $reply->delete();

        if(request()->expectsJson()) {
            return response(['status' => 'Reply deleted']);
        }

        return back();
    }

    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);

        $reply->update(request(['body']));

        if(request()->expectsJson()) {
            return response(['status' => 'Reply updated']);
        }

        return back();
    }
}
