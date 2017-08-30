<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ParticipateInForumTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function an_authenticated_user_can_participate_in_a_thread()
    {
        // Given we have an authenticated user and ...
        $this->be($user = factory('App\User')->create());         
        // ... existing thread
        $thread = factory('App\Thread')->create();

        // When user posts a areply: post to '/threads/id/posts'
        $reply = factory('App\Reply')->make();
        $this->post($thread->path() . '/replies', $reply->toArray());

        // Then the reply should be visible on thread page 
        $this->get($thread->path())
            ->assertSee($reply->body);
    }

    /** @test */
    public function unauthenticated_users_may_not_add_replies_to_a_thread()
    {
        $this->expectException('Illuminate\Auth\AuthenticationException');

        $this->post('/threads/1/replies', []);
    }
}
