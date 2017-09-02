<?php

namespace App;

trait Favoritable
{
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favorited');
    }

    public function favorite($user)
    {
        $attributes = [ 'user_id' => $user->id ];
        
        if(! $this->favorites()->where($attributes)->exists() ) {
            return $this->favorites()->create($attributes);
        }            
        
        return null;
    }

    public function isFavorited($user = null)
    {
        $user = $user ?: auth()->user();

        if (!$user) return false;

        return !! $this->favorites->where('user_id', $user->id)->count();
    }

    public function getFavoritesCountAttribute()
    {
        return $this->favorites->count();
    }
}
