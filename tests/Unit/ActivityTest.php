<?php

namespace Tests\Feature;

use Carbon\Carbon;
use App\Activity;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ActivityTest extends TestCase
{
    use DatabaseMigrations;     

    /** @test */
    public function it_records_activity_when_a_thread_is_created()
    {
        // When a user creates a thread
        $this->signIn();
        $thread = create('App\Thread', [ 'user_id' => auth()->id() ]);
        // Then an activity is creted in a database
        $this->assertDatabaseHas('activities', [
            'type' => 'created_thread',
            'user_id' => auth()->id(),
            'subject_id' => $thread->id,
            'subject_type' => get_class($thread)
        ]);

        $activity = Activity::first();
        $this->assertEquals($activity->subject->id, $thread->id);
    }

    /** @test */
    public function it_records_activity_when_a_reply_is_created()
    {
        // Given we have authorized user
        $this->signIn();
        // When a user creates a reply
        $reply = create('App\Reply');
        // Then an activity is creted in a database
        $this->assertDatabaseHas('activities', [
            'type' => 'created_reply',
            'subject_id' => $reply->id,
            'subject_type' => get_class($reply)
        ]);

        $this->assertCount(2, Activity::all());
    }

    /** @test */
    public function it_fetches_a_feed_for_any_user($value='')
        {
            $this->signIn();
            
            $thread = create('App\Thread', ['user_id' => auth()->id()], 2);
            auth()->user()->activities()->first()->update(['created_at' => Carbon::now()->subWeek()]);

            $feed = Activity::feed(auth()->user());

            $this->assertTrue($feed->keys()->contains(
                Carbon::now()->format('Y-m-d')
            ));

            $this->assertTrue($feed->keys()->contains(
                Carbon::now()->subWeek()->format('Y-m-d')
            ));
        }    
}