<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreateThreadTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function an_authenticated_user_may_create_new_forum_thread()
    {
        // Given we have a signed in user
        $this->signIn();
        // When user post a new thread
        $thread = make('App\Thread');
        $this->post('/threads', $thread->toArray());
        // Then thread is posted to databse and 
        // .. is visble on threads.index page 
        $this->get('/threads')
            ->assertSee($thread->title)
            ->assertSee($thread->body);
        // var_dump($thread->path());
    }

    /** @test */
    public function unauthenticated_user_may_not_create_new_forum_thread()
    {
        $this->expectException('Illuminate\Auth\AuthenticationException');

        // Given we have no signed in user
        // When guest post a new thread
        $this->post('/threads', []);
        // Then exception is thrown 
    }    
}
