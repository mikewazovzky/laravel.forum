<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserTest extends TestCase
{
    use DatabaseMigrations;     

    /** @test */
    public function it_can_fetch_the_most_recent_reply()
    {
        $user = create('App\User');
        $reply = create('App\Reply', ['user_id' => $user->id]);
        // $this->assertEquals($reply, $user->lastReply);
        $this->assertEquals($reply->id, $user->lastReply->id);
    }
}   