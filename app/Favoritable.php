<?php

namespace App;

trait Favoritable
{
    /**
     * Boot the trait.
     */
    public static function bootFavoritable()
    {
        static::deleting(function($model) {
            $model->favorites->each->delete();
        });
    }

    /**
     * A reply can be favorited.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favorited');
    }

    /**
     * Favorite the current reply.
     *
     * @param User | null   $user
     * @return Model
     */
    public function favorite($user = null)
    {
        $attributes = [ 'user_id' => $user ? $user->id : auth()->id() ];
        
        if(! $this->favorites()->where($attributes)->exists() ) {
            return $this->favorites()->create($attributes);
        }            
        
        return null;
    }

    /**
     * Unfavorite the current reply.
     *
     * @param User | null   $user
     * @return mixed
     */
    public function unfavorite($user = null)
    {
        $attributes = [ 'user_id' => $user ? $user->id : auth()->id() ];
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

    /**
     * Determine if the current reply has been favorited.
     *
     * @return boolean
     */
    public function isFavorited($user = null)
    {
        $user = $user ?: auth()->user();

        if (!$user) return false;

        return !! $this->favorites->where('user_id', $user->id)->count();
    }

    /**
     * Fetch the favorited status as a property.
     *
     * @return bool
     */
    public function getIsFavoritedAttribute()
    {
        return $this->isFavorited();
    }

    /**
     * Get the number of favorites for the reply as a property.
     *
     * @return integer
     */
    public function getFavoritesCountAttribute()
    {
        return $this->favorites->count();
    }
}
