<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use Favoritable;
    use RecordsActivity;

    protected $fillable = ['user_id', 'body'];
    
    // Specified relations will eager load everytime we fetch a reply
    protected $with = ['owner'];

    // Specifies list of custom attributes that will be appended to Model
    // when casted toArray or to JSON object
    protected $appends = ['favoritesCount', 'isFavorited'];

    protected static function boot()
    {
        parent::boot();
        // To turn off eager loading
        // App\Thread::withoutGlobalScope('favorites')->all() 
        // App\Thread::withoutGlobalScopes()->all()
        static::addGlobalScope('favorites', function($builder) {
            return $builder->with('favorites');
        });

        static::created(function($reply) {
            $reply->thread->increment('replies_count');
        });   

        static::deleted(function($reply) {
            $reply->thread->decrement('replies_count');
        });   

    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function path()
    {
        return $this->thread->path() . '#reply-' . $this->id;
    }
}
