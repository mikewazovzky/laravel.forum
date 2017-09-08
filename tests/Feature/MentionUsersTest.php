<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class MentionUsersTest extends TestCase
{
    use DatabaseMigrations;     

    /** @test */
    public function mentioned_users_in_a_reply_are_notified()
    {
        // Given we have an athenticated users <alex>
        // .. and another user <mike> 
        // .. and a thread        
        $this->signIn(
            $alex = create('App\User', ['name' => 'alex'])
        );
        $mike = create('App\User', ['name' => 'mike']);
        $thread = create('App\Thread');

        // When <alex> posts a reply and mentions <mike> in a reply body
        $reply = make('App\Reply', [
            'body' => 'Hi @mike! How are you doing? Greeting from @mary.'
        ]);
        $this->json('post', $thread->path() . '/replies', $reply->toArray());
        
        // Then <mike> should be notified
        $this->assertCount(1, $mike->notifications);
    }
}
