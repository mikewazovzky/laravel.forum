<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SubscribeToThreadsTest extends TestCase
{
    use DatabaseMigrations;     

    /** @test */
    public function an_authenticated_user_can_subscribe_to_threads()
    {
        // Given we have a user and a thread and the user subscibes to the thread
        $this->signIn();
        $thread = create('App\Thread');
        $this->post($thread->path() . '/subscriptions');        
        // When new reply is posted to the thread
        $thread->addReply([ 
            'user_id' => auth()->id(),
            'body' =>  'Some reply body here.'
        ]);
        // Then notification is prepaired for the user
        // $this->assertCount(1, auth()->user()->notifications);
    }
}