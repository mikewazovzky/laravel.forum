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

    /** @test */
    public function it_can_fetch_all_users_starting_with_given_string()
    {
        // Given we have a serch string
        $name = 'Mik';
        // .. and users with the name starting with this string
        $mike1 = create('App\User', ['name' => 'Mikhael']);
        $mike2 = create('App\User', ['name' => 'Mike Wazovzky']);        
        // .. and a user with the name NOT starting with this name
        $alex = create('App\User', ['name' => 'Alex']);
        // When we fetch users with a string as a query parameter 'name'
        $results = $this->json('GET', '/api/users', ['name' => $name])->json();
        // Then we get array of two users, which
        $this->assertCount(2, $results);
        // .. includes users whoes name match the search string and 
        $this->assertTrue(in_array($mike1->name, $results));
        $this->assertTrue(in_array($mike2->name, $results));       
        // .. does not include the one which does not match 
        $this->assertFalse(in_array($alex->name, $results));   
    }
}
