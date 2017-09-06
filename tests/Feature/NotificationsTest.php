<?php

namespace Tests\Feature;

use Illuminate\Notifications\DatabaseNotification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class NotificationsTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->signIn();
    }

    /** @test */
    public function a_notification_is_prepaired_when_a_subscribed_thread_receives_a_new_reply_by_other_user()
    {
        // Given we have a user and a thread and the user is subscibed to the thread
        $thread = create('App\Thread')->subscribe();
        $this->assertCount(0, auth()->user()->notifications);        
        // When new reply is posted to the thread by this user
        $thread->addReply([ 
            'user_id' => auth()->id(),
            'body' =>  'Some reply body here.'
        ]);
        // Then no notification is created
        $this->assertCount(0, auth()->user()->fresh()->notifications);
        // When new reply is posted to the thread by a different user
        $thread->addReply([ 
            'user_id' => create('App\User')->id,
            'body' =>  'Some reply body here.'
        ]);
        // Then notification is prepaired for the user
        $this->assertCount(1, auth()->user()->fresh()->notifications);
    }

    /** @test */
    public function a_user_can_fetch_its_unread_notifications()
    {
        // Given we have a authenticated user and notification
        create(DatabaseNotification::class);
        $user = auth()->user();
        // When the user hits gets notifications route
        $response = $this->getJson('/profiles/' . auth()->user()->name . '/notifications')->json();
        // Then the user gets his collection of unread notification
        $this->assertCount(1, $response);
    }

    /** @test */
    public function a_user_can_mark_a_notification_as_read()
    {
        // Given we have a authenticated user and notification
        create(DatabaseNotification::class);
        tap($user = auth()->user(), function ($user) {
            // Check that we have a notification 
            $this->assertCount(1, $user->unreadNotifications);
            // When user clears the notification
            $notificationId = $user->notifications->first()->id;
            $this->delete("/profiles/{$user->name}/notifications/{$notificationId}");
            // Then the user notifications are cleared
            $this->assertCount(0, $user->fresh()->unreadNotifications);
        });

    }
}
