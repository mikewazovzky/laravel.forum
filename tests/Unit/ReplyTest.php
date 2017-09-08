<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReplyTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_has_owner()
    {
        $reply = factory('App\Reply')->create();
        $this->assertInstanceOf('App\User', $reply->owner);
        $this->assertTrue($reply->owner->id == $reply->user_id);
    }

    /** @test */
    public function it_knows_if_it_was_posted_less_then_a_minite_ago()
    {
        $reply = create('App\Reply');
        $this->assertTrue($reply->wasJustPublished());
        $reply->created_at = Carbon::now()->subMinutes(3);
        $this->assertFalse($reply->wasJustPublished());
    }

    /** @test */
    public function it_can_detect_all_metioned_users()
    {
        $reply = create('App\Reply', ['body' => 'Hello @mike, greetings from @mary!']);
        $this->assertEquals(['mike', 'mary'], $reply->mentionedUsers());
        
    }
}
