<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Redis;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TrendingThreadsTest extends TestCase
{
    use DatabaseMigrations;   

    public function setUp()
    {
        parent::setUp();
        Redis::del('trending_threads');
    }  

    /** @test */
    public function it_increments_thread_score_every_time_it_is_read()
    {
        $this->assertCount(0, $trending = Redis::zrevrange('trending_threads', 0, -1));

        $thread = create('App\Thread');

        $this->call('GET', $thread->path());

        $this->assertCount(1, $trending = Redis::zrevrange('trending_threads', 0, -1));

        $this->assertEquals($thread->title, json_decode($trending[0])->title);

        // dd($trending);
    }
}