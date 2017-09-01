<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    protected $fillable = [ 'user_id', 'channel_id', 'title', 'body'];

    protected static function boot()
    {
        parent::boot();

        // Global Query Scope
        static::addGlobalScope('replyCount', function($builder) {
            return $builder->withCount('replies');
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
        $this->replies()->create($reply);
    }

    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }

}
