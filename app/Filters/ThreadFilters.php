<?php

namespace App\Filters;

use App\User;
use Illuminate\Http\Request;

class ThreadFilters extends Filters
{
    /**
     * Registered filters to be applied.
     *
     * @var array
     */
    protected $filters = ['by', 'popular', 'unanswered'];

    /**
     * Filter the query by a given username.
     *
     * @param string $username
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function by($username)
    {
        $user = User::where('name', $username)->firstOrFail();

        return $this->builder->where('user_id', $user->id);
    }

    /**
     * Filter the query according to most popular threads.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function popular()
    {
        // Clear all OrderBy parameters of the query before applying a new one
        // $this->builder->getQuery()->orders = [];

        return $this->builder->orderBy('replies_count', 'desc');
    }

    /**
     * Filter the query according to those that are unanswered.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function unanswered()
    {
        return $this->builder->where('replies_count', 0);
    }
}