<?php

namespace App\Filters;

use App\User;
use Illuminate\Http\Request;

class ThreadFilters extends Filters
{
    protected $filters = ['by', 'popular', 'unanswered'];

    /**
     * Filter the query by a given $username
     *
     * @param string $username
     * @return \Illuminate\Database\Query\Builder
     */
    public function by($username)
    {
        $user = User::where('name', $username)->firstOrFail();
        return $this->builder->where('user_id', $user->id);
    }

    /**
     * Filter the query according to most popular threads
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function popular()
    {
        return $this->builder->orderBy('replies_count', 'desc');
    }

    /**
     * Filter threads with no replies
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function unanswered()
    {
        return $this->builder->where('replies_count', 0);
    }
}