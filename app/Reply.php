<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use Favoritable;
    use RecordsActivity;

    /**
     * Auto-apply mass assignment protection.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'body'];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['owner'];

    /**
     * The accessors to append to the model's array form.
     * Specifies list of custom attributes that will be appended when
     * model is casted toArray or to JSON object
     *
     * @var array
     */
    protected $appends = ['favoritesCount', 'isFavorited', 'isBest'];

    /**
     * Boot the reply instance.
     */
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

    /**
     * A reply has an owner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * A reply belongs to a thread.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Determine the path to the reply.
     *
     * @return string
     */
    public function path()
    {
        return $this->thread->path() . '#reply-' . $this->id;
    }

    public function wasJustPublished($requiredDelay = 60)
    {
        return $this->created_at->gt(Carbon::now()->subSeconds($requiredDelay));
    }

    /**
     *
     * @return array of strings
     */
    public function mentionedUsers()
    {
        // preg_match_all('/\@([^\s\.\,\!\?]+)/', $this->body, $matches);
        preg_match_all('/\@([\w\-]+)/', $this->body, $matches);

        return $matches[1];
    }

    public function setBodyAttribute($body)
    {
        $this->attributes['body'] = preg_replace('/\@([\w\-]+)/', '<a href="/profiles/$1">$0</a>', $body);
    }

    public function isBest()
    {
        return $this->id == $this->thread->best_reply_id;
    }

    public function getIsBestAttribute()
    {
        return $this->isBest();
    }
}
