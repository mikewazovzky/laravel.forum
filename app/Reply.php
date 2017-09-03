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

    protected static function boot()
    {
        parent::boot();
        // To turn off eager loading
        // App\Thread::withoutGlobalScope('favorites')->all() 
        // App\Thread::withoutGlobalScopes()->all()
        static::addGlobalScope('favorites', function($builder) {
            return $builder->with('favorites');
        });
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
