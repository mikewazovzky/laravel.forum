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

    /** @test */
    public function a_user_can_filter_threads_by_popularity()
    {
        // Given we have three threads
        // With 3, 0 and 2 replies        
        $threadWithThreeReplies = create('App\Thread');
        create('App\Reply', ['thread_id' => $threadWithThreeReplies->id ], 3);

        $threadWithNoReplies =$this->thread;

        $threadWithTwoReplies = create('App\Thread');
        create('App\Reply', ['thread_id' => $threadWithTwoReplies->id], 2);

        // When we filter thread by popularity
        $response = $this->getJson('/threads?popular=1')->json();
        // dd($response);

        // They should come in proper order
        $this->assertEquals([3, 2, 0], array_column($response, 'replies_count'));   
    }    

    /** @test */
    public function a_user_can_filter_unanswered_threads()
    {
        // Given we have two threads
        // with and without replies        
        $threadWithReplies = create('App\Thread');
        create('App\Reply', ['thread_id' => $threadWithReplies->id ], 3);

        $threadWithNoReplies =$this->thread;

        // When we filter unanswered threads
        $response = $this->getJson('/threads?unanswered=1')->json();
        // There should be only one present
        $this->assertCount(1, $response);
    }    

    /** @test */
    public function a_user_can_get_all_replies_for_a_thread($value='')
    {
        $thread = create('App\Thread');
        $reply = create('App\Reply', ['thread_id' => $thread->id], 40);

        $response = $this->getJson($thread->path() . '/replies')->json();

        $this->assertCount(20, $response['data']); // paginated to chunks of 20
        $this->assertEquals(40, $response['total']); // total number of replies
    }
}
