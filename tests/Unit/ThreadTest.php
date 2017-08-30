<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ThreadTest extends TestCase
{
    use DatabaseMigrations;
    protected $thread;

    public function setUp()
    {
        parent::setUp();
        $this->thread =  factory('App\Thread')->create();
    }

    /** @test */
    public function it_has_replies()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->thread->replies);
    }

    /** @test */
    public function it_has_user()
    {
        $this->assertInstanceOf('App\User', $this->thread->user);
    }

    /** @test */
    public function it_can_add_a_reply()
    {
        $replyData = [ 
            'user_id' => 1, 
            'body' => 'Foobar'
        ];

        $this->assertCount(0, $this->thread->replies);
        $this->thread->addReply($replyData);

        $this->assertCount(1, $this->thread->fresh()->replies);
    }
}
