<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Redis;
use App\Notifications\ThreadWasUpdated;
use Illuminate\Support\Facades\Notification;
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
        $this->thread =  create('App\Thread');
    }

    /** @test */
    public function it_has_a_path()
    {
        $this->assertEquals(
            "/threads/{$this->thread->channel->slug}/{$this->thread->slug}",             
            $this->thread->path()
        );
    }

    /** @test */
    public function it_has_user()
    {
        $this->assertInstanceOf('App\User', $this->thread->user);
    }

    /** @test */
    public function it_has_replies()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->thread->replies);
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

    /** @test */
    public function it_notifies_all_registered_subscribers_when_new_reply_is_added()
    {
        Notification::fake();
        // Given we have a thread and a user subscribed to the thread
        // When new reply is posted to the thread         
        $this->signIn()
            ->thread
            ->subscribe()
            ->addReply([
                'user_id' => create('App\User')->id, 
                'body' => 'Foobar'
            ]);        
        // The notification is creted for the user
        Notification::assertSentTo(auth()->user(), ThreadWasUpdated::class);
    }

    /** @test */
    public function it_belongs_to_a_channel()
    {
        $this->assertInstanceOf('App\Channel', $this->thread->channel);       
    }    

    /** @test */
    public function a_user_can_be_subscribed_to_a_thread()
    {
        // Given we have a thread and
        $thread = create('App\Thread');
        
        // ... authenticated user
        $this->signIn();
        
        // When the user subscribes to the thread
        // Tip: be careful to assign too much responsibilities to a user (god object)
        // $user->subscribeToThread();
        // $user->subscriptions()
        $thread->subscribe();
        
        // Then we should be able to fetch all threads that user is subscribed to
        $this->assertCount(1, $thread->subscriptions()->where(['user_id' => auth()->id()])->get());
    }

    /** @test */
    public function a_user_can_be_unsubscribed_to_a_thread()
    {
        // Given we have a thread and
        $thread = create('App\Thread');
        // ... authenticated user 
        $this->signIn();
        // ... subscribed to a thread
        $thread->subscribe();

        // When the user unsubscribes from the thread
        $thread->unsubscribe();
        // Then we should be able to fetch all threads that user is subscribed to
        $this->assertCount(0, $thread->subscriptions);
    }    

/** @test */
    public function it_knows_if_authenticated_user_is_subscribed_to_it()
    {       
        // Given we have a thread and
        $thread = create('App\Thread');
        // ... authenticated user 
        $this->signIn();
        // ... subscribed to a thread
        $this->assertFalse($thread->isSubscribedTo());
        $this->assertFalse($thread->isSubscribedTo);        
        $thread->subscribe();
        // Then we can check if subscription exists
        $this->assertTrue($thread->isSubscribedTo());
        $this->assertTrue($thread->isSubscribedTo);        
    }    

    /** @test */
    public function it_can_check_if_authenticated_user_has_read_all_replies()
    {
        // Given we have a user and a thread with replies
        $this->signIn();
        $thread = create('App\Thread');
        $this->assertTrue($thread->hasUpdatesFor());

        tap(auth()->user(), function($user) use ($thread) {
            $this->assertTrue($thread->hasUpdatesFor($user));          
            // When the user visits the thread
            // $this->get($thread->path());

            // .. simulate that user visited the thread            
            // cache()->forever(
            //         $user->visitedThreadCacheKey($thread), 
            //         \Carbon\Carbon::now()
            // );
            $user->readThread($thread);

            // Then check hasUpdaesFor shoudl be change from thrue to false
            $this->assertFalse($thread->hasUpdatesFor($user));            
        });
    }    

    // /** @test */
    // public function it_records_each_visit()
    // {
    //     $thread = make('App\Thread', ['id' => 1]);
    //     $thread->visits()->reset();
    //     $this->assertSame(0, $thread->visits()->count());
        
    //     $thread->visits()->record();
    //     $this->assertEquals(1, $thread->visits()->count());
        
    //     $thread->visits()->record();
    //     $this->assertEquals(2, $thread->visits()->count());        
    // }

}
