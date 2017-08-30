<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{

    public function path()
    {
        return '/threads/' . $this->id;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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

}
