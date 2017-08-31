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
        $response = $this->post('/threads', $thread->toArray());
        // dd($response->headers->get('Location')); // check redirect uri
        // Then thread is posted to databse and 
        // .. is visble on threads.index page 
        $this->get($response->headers->get('Location'))
            ->assertSee($thread->title)
            ->assertSee($thread->body);
        // var_dump($thread->path());
    }

    /** @test */
    public function guest_may_not_create_new_forum_thread()
    {
        $this->withExceptionHandling();

        $this->get('/threads/create')
            ->assertRedirect('/login');

        $this->post('/threads', [])
            ->assertRedirect('/login');
    }

    /** @test */
    public function it_requires_a_valid_channel()
    {
        factory('App\Channel', 2)->create();
        $this->publishThread([ 'channel_id' => null])
            ->assertSessionHasErrors('channel_id');

        $this->publishThread([ 'channel_id' => 777])
            ->assertSessionHasErrors('channel_id');            
    }

    /** @test */
    public function it_requires_a_title()
    {
        $this->publishThread([ 'title' => null])
            ->assertSessionHasErrors('title');
    }

    /** @test */
    public function it_requires_a_body()
    {
        $this->publishThread([ 'body' => null])
            ->assertSessionHasErrors('body');
    }

    protected function publishThread($attributes = [])
    {
        $this->withExceptionHandling()->signIn();

        $thread = make('App\Thread', $attributes);

        return $this->post('/threads', $thread->toArray());
    }
}
