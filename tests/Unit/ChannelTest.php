<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ChannelTest extends TestCase
{
    use DatabaseMigrations;     

    /** @test */
    public function it_has_threads()
    {
        $channel = create('App\Channel');
        $thread = create('App\Thread', [ 'channel_id' => $channel->id ]);

        $this->assertInstanceOf('App\Thread', $channel->threads()->first());
        $this->assertTrue($channel->threads->contains($thread));
    }
}