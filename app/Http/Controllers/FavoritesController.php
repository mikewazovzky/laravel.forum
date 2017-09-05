<?php

namespace App\Http\Controllers;

use App\Reply;

class FavoritesController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a new favorite in the database.
     *
     * @param  Reply $reply
     */
    public function store(Reply $reply)
    {
        // Favorite::create([
        //     'user_id' => auth()->id(),
        //     'favorited_type' => get_class($reply),
        //     'favorited_id' => $reply->id,
        // ]);

        // $reply->favorites()->create(['user_id' => auth()->id()]);        
        
        $reply->favorite(auth()->user());

        if (request()->expectsJson()) {
            return response(['status' => 'Reply favorited']);
        }

        return back();
    }

    /**
     * Delete the favorite.
     *
     * @param Reply $reply
     */
    public function destroy(Reply $reply)
    {
        $reply->unfavorite(auth()->user());

        if(request()->expectsJson()) {
            return response(['status' => 'Reply unfavorited']);
        }

        return back();
    }
}
