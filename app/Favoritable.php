<?php

namespace App;

trait Favoritable
{
    public static function bootFavoritable()
    {
        static::deleting(function($model) {
            $model->favorites->each->delete();
        });
    }

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

    public function unfavorite($user)
    {
        $attributes = [ 'user_id' => $user->id ];
        // The code below does not trigger Model::deleting events,
        // it's just SQL query
        // return $this->favorites()->where($attributes)->delete();
        
        // Modified version
        // Option 1. Fetch a collection of model objects
        return $this->favorites()->where($attributes)->get()->each(function($model) {
            $model->delete();
        });       

        // Option 2. Fetch a collection of model objects and use higher order messaging
        // return $this->favorites()->where($attributes)->get()->each->delete();
    }

    public function isFavorited($user = null)
    {
        $user = $user ?: auth()->user();

        if (!$user) return false;

        return !! $this->favorites->where('user_id', $user->id)->count();
    }

    public function getIsFavoritedAttribute()
    {
        return $this->isFavorited();
    }

    public function getFavoritesCountAttribute()
    {
        return $this->favorites->count();
    }
}
