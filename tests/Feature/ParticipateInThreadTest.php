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
        $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertStatus(200);
        // Then the reply should be visible on thread page 
        $this->assertDatabaseHas('replies', [ 'body' => $reply->body ]);
        $this->assertEquals(1, $thread->fresh()->replies_count);
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
        $this->assertEquals(0, $reply->thread->fresh()->replies_count);
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
            ->assertStatus(200);
        
        $this->assertDatabaseHas('replies', [
            'id' => $reply->id,
            'body' => $updatedReply
        ]);
    }

    /** @test */
    public function reply_that_contains_spam_may_not_be_created()
    {
        $this->withExceptionHandling();
        // Given we have an authenticated user and a thread and a reply
        $this->signIn();         
        $thread = create('App\Thread');
        // When user posts a reply containing a spam
        $spam = 'Yahoo Customer Support';     
        $reply = make('App\Reply', ['body' => $spam]);     
        // $response = $this->post($thread->path() . '/replies', $reply->toArray());
        // Request response as json
        $response = $this->json('post', $thread->path() . '/replies', $reply->toArray());
        // Then reply may not be posted
        $response->assertStatus(422);
        // .. and reply should not be saved to database         
        $this->assertDatabaseMissing('replies', [ 'body' => $spam ]);
    }

    /** @test */
    public function users_may_not_post_replies_faster_then_once_a_minute()
    {
        $this->withExceptionHandling();
        $this->signIn();         
        $thread = create('App\Thread');
        $reply = make('App\Reply', ['body' => 'My reply']);
        $response = $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertStatus(200);
        $response = $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertStatus(429);            
    }
}
