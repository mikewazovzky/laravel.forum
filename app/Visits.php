<?php

namespace App;

use Illuminate\Support\Facades\Redis;

class Visits 
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function reset()
    {
        Redis::del($this->cacheKey());
        return $this;
    }

    public function count()
    {
        return Redis::get($this->cacheKey()) ?? 0;
    }

    public function record()
    {
        Redis::incr($this->cacheKey());
        return $this;
    }

    protected function cacheKey()
    {
        // return "thread.{$this->model->id}.visits"; 
        $modelName = strtolower(
            (new \ReflectionClass(get_class($this->model)))->getShortName()
        );
        return "{$modelName}.{$this->model->id}.visits";    
    }
}