<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ReadThreadsTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->thread = factory('App\Thread')->create();
    }

    /** @test */
    public function a_user_can_browse_all_threads()
    {
        $this->get('/threads')
            ->assertStatus(200)
            ->assertSee($this->thread->title)
            ->assertSee($this->thread->body);
    }

    /** @test */
    public function a_user_can_read_a_single_thread()
    {
        $this->get($this->thread->path())
            ->assertStatus(200)
            ->assertSee($this->thread->title)
            ->assertSee($this->thread->body);
    }

    /** @test */
    public function a_user_can_read_replies_associated_with_the_thread()
    {
        // Given we have a thread with reply(-ies)
        $reply = factory('App\Reply')
            ->create([ 'thread_id' => $this->thread->id ]);
        // When we hit a thread page
        // Then we should see a reply body
        $this->get($this->thread->path())
            ->assertSee($reply->body);
    }

    /** @test */
    public function a_user_can_filter_threads_by_a_channel()
    {
        $channel = create('App\Channel');
        $threadInChannel = create('App\Thread', [ 'channel_id' => $channel->id ]);
        $threadNotInChannel = create('App\Thread');

        $this->get('/threads/' . $channel->slug)
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannel->title);   
    }

    /** @test */
    public function a_user_can_filter_threads_by_any_username()
    {
        $this->signIn(create('App\User', [ 'name' => 'MikeWazovzky']));
        $threadByMike = create('App\Thread', [ 'user_id' => auth()->id() ]);
        $threadNotByMike = create('App\Thread');

        // $url = '/threads' . '?by=' . auth()->user()->name;
        // dd($url);
        $url = '/threads?by=MikeWazovzky';        

        $this->get($url)
            ->assertSee($threadByMike->title)
            ->assertDontSee($threadNotByMike->title);   
    }
}
