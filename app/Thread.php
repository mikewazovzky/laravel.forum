<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use RecordsActivity;
    
    protected $fillable = [ 'user_id', 'channel_id', 'title', 'body'];
    protected $with = ['user', 'channel'];

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
   
    public function path()
    {
        // return '/threads/' . $this->channel->slug . '/' . $this->id;
        return "/threads/{$this->channel->slug}/{$this->id}";
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }
    /**
     * Function description
     *
     * @param type name
     * @return type 
     */
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function addReply($reply)
    {
        return $this->replies()->create($reply);
    }

    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }

}
