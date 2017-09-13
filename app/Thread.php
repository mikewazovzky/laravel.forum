<?php

namespace App;

use App\Events\ThreadReceivedNewReply;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use RecordsActivity;
    
    /**
     * Auto-apply mass assignment protection.
     *
     * @var array
     */
    protected $fillable = [ 'user_id', 'channel_id', 'title', 'body', 'slug'];


    /**
     * The relationships to always eager-load.
     *
     * @var array
     */
    protected $with = ['user', 'channel'];


    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Global Query Scope
        // static::addGlobalScope('replyCount', function($builder) {
        //     return $builder->withCount('replies');
        // });
        // replies_count field added to database
        
        // Model events: delete associated replies when model:deleting event is fired off
        // 
        static::deleting(function($thread) {
            // $thread->replies()->delete();

            // The code above doesn't fire deleting event on reply model 
            // to delete asspciated reply activity, 
            // because reply models are not fetched,  
            // just a database query is creted to delete replies

            // Option 1. 
            // Fetch replies (models) and delete them
            // $thread->replies->each(function($reply) {
            //     $reply->delete();
            // });

            // Option 2. 
            // Higher order messaging on Laravel collection, use 'each' pseudo prop  
            $thread->replies->each->delete();
        });
    }
   
    /**
     * Get the route key name for Laravel.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';              
    }

    /**
     * Get a string path for the thread.
     *
     * @return string
     */
    public function path()
    {
        // return '/threads/' . $this->channel->slug . '/' . $this->id;
        // return "/threads/{$this->channel->slug}/{$this->id}";
        return "/threads/{$this->channel->slug}/{$this->slug}";
    }

    /**
     * A thread belongs to a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A thread is assigned a channel.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * A thread may have many replies.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    /**
     * Add a reply to the thread.
     *
     * @param  array $reply
     * @return Model
     */
    public function addReply($reply)
    {
        $reply =  $this->replies()->create($reply);

        // Prepare notifications for all subscribers
        // Refactoring stages/options

        // foreach($this->subscriptions as $subscription) {
        //     if($subscription->user_id != $reply->user_id) {
        //         $subscription->user->notify(new ThreadWasUpdated($this, $reply));
        //     }           
        // }

        // $this->subscriptions
        //     ->filter(function($subscription) use ($reply) {
        //         return $subscription->user_id != $reply->user_id;
        //     })
        //     ->each(function($subscription) use($reply) {
        //         $subscription->user->notify(new ThreadWasUpdated($this, $reply));
        //     });

        // $this->subscriptions
        //     ->filter(function($subscription) use ($reply) {
        //         return $subscription->user_id != $reply->user_id;
        //     })
        //     ->each->notify($reply);

        // $this->subscriptions
        //     ->where('user_id',  '!=',  $reply->user_id)
        //     ->each
        //     ->notify($reply);

        // $this->notifySubscribers($reply);

        event(new ThreadReceivedNewReply($reply));

        return $reply;
    }

    public function notifySubscribers($reply)
    {
        $this->subscriptions
            ->where('user_id',  '!=',  $reply->user_id)
            ->each
            ->notify($reply);
    }

    /**
     * Apply all relevant thread filters.
     *
     * @param  Builder       $query
     * @param  ThreadFilters $filters
     * @return Builder
     */
    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }

    /**
     * Subscribe a user to the current thread.
     *
     * @param  User|null $userId
     * @return $this
     */
    public function subscribe($user = null)
    {
        $this->subscriptions()->create([
            'user_id' => $user ? $user->id : auth()->id()
        ]);

        return $this;
    }

    /**
     * Unsubscribe a user from the current thread.
     *
     * @param User|null $userId
     */
    public function unsubscribe($user = null)
    {
        $this->subscriptions()
            ->where([ 'user_id' => $user ? $user->id : auth()->id() ])
            ->delete();
    }    

    /**
     * A thread can have many subscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    /**
     * Check if a user is subscribed to the thread 
     *
     * @param User|null
     * @return bool
     */
    public function isSubscribedTo($user = null)
    {
        $userId = $user ? $user->id : ( auth()->check() ? auth()->id() : null);
        return $this->subscriptions()->where('user_id', $userId)->exists();
    }

    /**
     * Attribute specifies if a user is subscribed to the thread 
     *
     * @param User|null
     * @return bool
     */
    public function getIsSubscribedToAttribute()
    {
        return $this->isSubscribedTo();
    }    

    public function hasUpdatesFor($user = null)
    {
        $user = $user ?: auth()->user();

        // Look in the cash for the proper key
        // compare that element (carbon instance) with $thread->updated_at 
        // $key = auth()->user()->visitedThreadCacheKey($this);
        return $this->updated_at > cache(
            $key = auth()->user()->visitedThreadCacheKey($this)
        );
    }

    public function setSlugAttribute($value)
    {
        $original = $slug = str_slug($value);
        $count = 2;

        while(static::whereSlug($slug)->exists()) {
            $slug = "{$original}-" . $count++;
        }

        // if (static::whereSlug($slug = str_slug($value))->exists()) {
        //     $slug = $this->incrementSlug($slug);
        // }

        $this->attributes['slug'] = $slug;
    }

    // protected function incrementSlug($slug, $count = 2)
    // {
    //     // Option 1 (original, does not cover edgecases)
    //     // $max = static::whereTitle($this->title)->latest('id')->value('slug');
    //     // if (is_numeric($max[-1])) {
    //     //     return preg_replace_callback('/(\d+)$/', function ($matches) {
    //     //         return $matches[1] + 1;
    //     //     }, $max);
    //     // }
    //     // return "{$slug}-2";

    //     // Option 2. transfered to setSlugAttribute()
    //     // multiple database queries possible,
    //     // scenario: created->deleted->created thread with the same title, 
    //     //   link to original thread will now refer to the new thread  
    //     $original = $slug;
    //     while(static::whereSlug($slug)->exists()) {
    //         $slug = "{$original}-" . $count++;
    //     }
    //     return $slug;
    // }
}
