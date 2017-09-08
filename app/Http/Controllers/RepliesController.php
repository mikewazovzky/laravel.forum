<?php

namespace App\Http\Controllers;

use App\Thread;
use App\Reply;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RepliesController extends Controller
{
    /**
     * Create a new RepliesController instance.
     */
    public function __construct()
    {
        // $this->middleware('auth')->except(['index']);
        $this->middleware('auth', ['except' => 'index']);
    }

    /**
     * Fetch all relevant replies.
     *
     * @param int    $channelId
     * @param Thread $thread
     */
    public function index($channelId, Thread $thread)
    {
       return $thread->replies()->paginate(20); 
    }

    /**
     * Persist a new reply.
     *
     * @param  integer  $channelId
     * @param  Thread   $thread
     * @param  Request  $form
     * @return \Illuminate\Database\Eloquent\Model | \Illuminate\Http\RedirectResponse
     */
    public function store($channelId, Thread $thread)
    {
        
        if (Gate::denies('create', new Reply)) {
            return response('Your are postng too frequently. Pls. take a break.', 429);
        }

        try {
            // $this->authorize('create', new Reply());
            $this->validate(request(), ['body' => 'required|spamfree']);
         
            $reply = $thread->addReply([
                'user_id' => auth()->id(),
                'body' => request('body')
            ]);     
        } catch (\Exception $e) {
            // 422 - unprocessable entity
            return response('Sorry! Your reply could not be saved at this time.', 422);
        }

        // if ($request->expectsJson()) {
        //     return $reply->load('owner');
        // }
        // return back()->with('flash', 'Your reply has been posted!');

        // We only store replies via ajax request now
        return $reply->load('owner');
    }

    /**
     * Update an existing reply.
     *
     * @param Reply $reply
     */
    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);

        try {
            $this->validate(request(), ['body' => 'required|spamfree']);

            $reply->update(request(['body']));
        } catch (\Exception $e) {
            // 422 - unprocessable entity
            return response('Sorry! Your reply could not be saved at this time.', 422);
        }

        // if(request()->expectsJson()) {
        //     return response(['status' => 'Reply updated']);
        // }
        // return back();

        // We only update replies via ajax request now
        return response(['status' => 'Reply updated']);
    }

    /**
     * Delete the given reply.
     *
     * @param  Reply $reply
     * @return \Illuminate\Database\Eloquent\Model | \Illuminate\Http\RedirectResponse
     */
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

    // SpamFree detection transfered to validation rule
    // protected function validateReply()
    // {
    //     $this->validate(request(), ['body' => 'required|spamfree']);
    //     // (new Spam())->detect(request('body'));
    //     // resolve(Spam::class)->detect(request('body'));
    // }
}
