<?php

namespace App\Http\Controllers;

use App\Trending;
// use Illuminate\Support\Facades\Redis;
use App\Filters\ThreadFilters;
use App\Channel;
use App\Thread;
use Illuminate\Http\Request;

class ThreadsController extends Controller
{
    /**
     * Create a new ThreadsController instance.
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Channel      $channel
     * @param ThreadFilters $filters
     * @return \Illuminate\Database\Eloquent\Collection | \Illuminate\Http\Response
     */
    public function index(Channel $channel, ThreadFilters $filters, Trending $trending)
    {
        $threads = $this->getThreads($channel, $filters);

        if (request()->wantsJson()) {
            return $threads;
        }

        // $trending = collect(Redis::zrevrange('trending_threads', 0, $take = 4))
        //     ->map(function($thread) {
        //         return json_decode($thread);
        //     });

        // $trending = array_map('json_decode', Redis::zrevrange('trending_threads', 0, -1));

        return view('threads.index', [ 
            'threads' => $threads,
            'trending' => $trending->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('threads.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // if (! auth()->user()->confirmed) {
        //     return redirect('/threads')->with('flash', 'Must confirm email address.');
        // }

        $this->validate($request, [
            'channel_id' => 'required|exists:channels,id',
            'title' => 'required|spamfree',
            'body' => 'required|spamfree',
        ]);

        $thread = Thread::create([
            'user_id' => auth()->id(),
            'channel_id' => $request->channel_id,
            'title' => $request->title,
            'body' => $request->body,
        ]);

        return redirect($thread->path())
            ->with('flash', 'Your thread has been published!');
    }

    /**
     * Display the specified resource.
     *
     * @param  integer      $channel
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function show($channelId, Thread $thread, Trending $trending)
    {
        if (auth()->check()) {
            auth()->user()->readThread($thread);            
        }   

        $trending->push($thread);   

        $thread->increment('visits');    
        
        return view('threads.show', [ 
            'thread' => $thread->append('isSubscribedTo'),
        ]);
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  integer      $channelId
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy($channelId, Thread $thread)
    {
        $this->authorize('update', $thread);

        $thread->delete();

        if (request()->wantsJson()) {
            return response([], 204);
        }
        
        return redirect('/threads')
            ->with('flash', 'Your thread has been deleted!');
    }

    /**
     * Fetch all relevant threads.
     *
     * @param Channel       $channel
     * @param ThreadFilters $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getThreads($channel, $filters)
    {
        $threads = Thread::filter($filters)->latest();
        
        if ($channel->exists) {
            $threads = $threads->where('channel_id', $channel->id);
        }

        return $threads->paginate(20);
    }
}