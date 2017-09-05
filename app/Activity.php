<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    /**
     * Don't auto-apply mass assignment protection.
     *
     * @var array
     */
    protected $guarded = [];


    public function subject()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Fetch an activity feed for the given user.
     *
     * @param  User     $user
     * @param  integer  $take
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function feed($user, $take = 25)
    {
        return  static::where(['user_id' => $user->id])
            ->latest()
            ->with('subject')
            ->take($take)
            ->get()
            ->groupBy(function($activity) {
                return $activity->created_at->format('Y-m-d');
            }
        );    
    }
}


