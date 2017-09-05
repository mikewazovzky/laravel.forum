<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ParticipateInThreadTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function an_authenticated_user_can_participate_in_a_thread()
    {
        // Given we have an authenticated user and ...
        $this->signIn();         
        // ... existing thread
        $thread = create('App\Thread');
        // When user posts a reply: post to '/threads/id/replies'
        $reply = factory('App\Reply')->make();
        $this->post($thread->path() . '/replies', $reply->toArray());
        // Then the reply should be visible on thread page 
        $this->assertDatabaseHas('replies', [ 'body' => $reply->body ]);
    }

    /** @test */
    public function unauthenticated_users_may_not_add_replies_to_a_thread()
    {
        $this->withExceptionHandling();

        $this->post('/threads/some-channel/1/replies', [])
            ->assertRedirect('/login');
    }

    /** @test */
    public function a_reply_requires_a_body()
    {
        $this->withExceptionHandling()->signIn();

        $thread = create('App\Thread');
        $reply = make('App\Reply', [ 'body' => null ]);
        
        $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertSessionHasErrors('body');
    }

    /** @test */ 
    public function unauthorized_users_can_not_delete_replies()
    {
        $this->withExceptionHandling();

        $reply = create('App\Reply');

        $this->delete("/replies/{$reply->id}")
            ->assertRedirect('/login');

        $this->signIn()
            ->delete("/replies/{$reply->id}")
            ->assertStatus(403);
    }

    /** @test */ 
    public function authorized_users_can_delete_replies()
    {
        $this->signIn();

        $reply = create('App\Reply', ['user_id' => auth()->id()]);

        $this->delete("/replies/{$reply->id}")->assertStatus(302);
        
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
    }

    /** @test */ 
    public function unauthorized_users_can_not_update_replies()
    {
        $this->withExceptionHandling();

        $reply = create('App\Reply');
        $updatedReply = 'Updated reply body';

        $this->patch("/replies/{$reply->id}", ['body' => $updatedReply] )
            ->assertRedirect('/login');

        $this->signIn()
            ->patch("/replies/{$reply->id}", ['body' => $updatedReply] )
            ->assertStatus(403);

        $this->assertDatabaseMissing('replies', [
            'id' => $reply->id,
            'body' => $updatedReply
        ]);

    }
    /** @test */ 
    public function authorized_users_can_update_replies()
    {
        $this->signIn();

        $reply = create('App\Reply', ['user_id' => auth()->id()]);
        $updatedReply = 'Updated reply body';

        $this->patch("/replies/{$reply->id}", ['body' => $updatedReply] )
            ->assertStatus(302);
        
        $this->assertDatabaseHas('replies', [
            'id' => $reply->id,
            'body' => $updatedReply
        ]);
    }
}
