<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Trending;
use Illuminate\Support\Facades\Redis;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TrendingThreadsTest extends TestCase
{
    use DatabaseMigrations;   
    protected $trending;

    public function setUp()
    {
        parent::setUp();

        $this->trending = new Trending();

        $this->trending->clear();
    }  

    /** @test */
    public function it_increments_thread_score_every_time_it_is_read()
    {
        $this->assertCount(0, $this->trending->get());

        $thread = create('App\Thread');

        $this->call('GET', $thread->path());

        $this->assertCount(1, $trending = $this->trending->get());

        $this->assertEquals($thread->title, $trending[0]->title);
    }
}