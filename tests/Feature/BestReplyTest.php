<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class BestReplyTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_thread_creator_may_mark_any_reply_as_best_reply()
    {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $replies = create('App\Reply', ['thread_id' => $thread->id], 2);

        $this->assertFalse($replies[1]->fresh()->isBest());

        $this->postJson(route('best-reply.store', $replies[1]));

        $this->assertTrue($replies[1]->fresh()->isBest());
    }

    /** @test */
    public function only_thread_creator_may_mark_any_reply_as_best_reply()
    {
        $this->withExceptionHandling();

        $this->signIn();

        $thread = create('App\Thread');

        $replies = create('App\Reply', ['thread_id' => $thread->id], 2);

        $this->postJson(route('best-reply.store', $replies[1]))->assertStatus(403);

        $this->assertFalse($replies[1]->fresh()->isBest());
    }
}
