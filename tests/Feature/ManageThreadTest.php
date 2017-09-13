<?php

namespace Tests\Feature;

use App\Activity;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ManageThreadTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function guest_may_not_create_thread()
    {
        $this->withExceptionHandling();

        $this->get('/threads/create')
            ->assertRedirect(route('login'));

        $this->post(route('threads'), [])
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function new_user_must_confirm_email_address_before_creating_thread()
    {
        $user = factory('App\User')->states('unconfirmed')->create();
        
        $this->signIn($user);

        $thread = make('App\Thread');

        $this->post(route('threads'), $thread->toArray())
            ->assertRedirect(route('threads'))
            ->assertSessionHas('flash');
    }

    /** @test */
    public function authenticated_user_may_create_forum_thread()
    {
        // Given we have a signed in user
        $this->signIn();
        // When user post a new thread
        $thread = make('App\Thread');
        $response = $this->post(route('threads'), $thread->toArray());
        // dd($response->headers->get('Location')); // check redirect uri
        // Then thread is posted to databse and 
        // .. is visble on threads.index page 
        $this->get($response->headers->get('Location'))
            ->assertSee($thread->title)
            ->assertSee($thread->body);
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

    /** @test */
    public function unauthorized_users_may_not_delete_threads()
    {
        $this->withExceptionHandling();

        $thread = create('App\Thread');
        $reply =create('App\Reply', ['thread_id' => $thread->id]);

        $this->delete($thread->path())->assertRedirect('/login');

        $this->assertDatabaseHas('threads', [ 'id' => $thread->id ]);
        $this->assertDatabaseHas('replies', [ 'id' => $reply->id ]);  

        // Even signed in user may not delete other user's threads 
        $this->signIn();
        $this->delete($thread->path())->assertStatus(403);

    }

    /** @test */
    public function authorized_users_may_delete_threads()
    {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id() ]);
        $reply =create('App\Reply', ['thread_id' => $thread->id]);

        $this->json('DELETE', $thread->path())
            ->assertStatus(204);

        $this->assertDatabaseMissing('threads', [ 'id' => $thread->id ]);
        $this->assertDatabaseMissing('replies', [ 'id' => $reply->id ]);  

        // $this->assertDatabaseMissing('activities', [ 
        //     'subject_id' => $thread->id,
        //     'subject_type' => get_class($thread) 
        // ]);   

        // $this->assertDatabaseMissing('activities', [ 
        //     'subject_id' => $reply->id, 
        //     'subject_type' => get_class($reply)             
        // ]);     

        $this->assertEquals(0, Activity::all()->count());                   
    }

    protected function publishThread($attributes = [])
    {
        $this->withExceptionHandling()->signIn();

        $thread = make('App\Thread', $attributes);

        return $this->post(route('threads'), $thread->toArray());
    }
}
