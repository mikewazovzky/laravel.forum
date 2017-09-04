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

        $thread->addReply([
            'user_id' => auth()->id(),
            'body' => $request->body
        ]);

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

        return back();
    }

    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);

        $reply->update(request(['body']));
    }
}
