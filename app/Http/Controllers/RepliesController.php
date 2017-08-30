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
    public function store(Thread $thread, Request $request)
    {
        // validation

        $thread->addReply([
            'user_id' => auth()->id(),
            'body' => $request->body
        ]);

        return back();
    }
}
