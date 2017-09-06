<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SubscribeToThreadsTest extends TestCase
{
    use DatabaseMigrations;     

    /** @test */
    public function an_authenticated_user_can_subscribe_to_a_thread()
    {
        // Given we have a user and a thread and the user subscibes to the thread
        $this->signIn();
        $thread = create('App\Thread');
        // When user hits subscribe endpoint        
        $this->post($thread->path() . '/subscriptions'); 
        // Then the user shoul be subscribed to a thread
        $this->assertCount(1, $thread->subscriptions);
    }

    /** @test */
    public function an_authenticated_user_can_unsubscribe_from_a_thread()
    {
        // Given we have a user and a thread and the user is subscibes to the thread
        $this->signIn();
        $thread = create('App\Thread');
        $thread->subscribe();
        $this->assertCount(1, $thread->subscriptions);

        // When user unsubscribes
        $this->delete($thread->path() . '/subscriptions');        
        // Then it's not subscribed
        $this->assertCount(0, $thread->fresh()->subscriptions);
    }    
}