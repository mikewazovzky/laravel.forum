<?php

namespace App;

use Illuminate\Support\Facades\Redis;

class Trending 
{
    public function get()
    {
        return $trending = array_map('json_decode', Redis::zrevrange($this->cacheKey(), 0, -1));
    }

    public function push(Thread $thread)
    {
        Redis::zincrby($this->cacheKey(), 1, json_encode([
            'title' => $thread->title,
            'path' => $thread->path(),
        ])); 
    }

    public function clear()
    {
        Redis::del($this->cacheKey());
    }

    protected function cacheKey()
    {
        $key = (app()->environment() === 'testing') ? 'trending_threads_testing' : 'trending_threads';
        return $key;
    }
}