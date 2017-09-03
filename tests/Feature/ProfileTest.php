<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProfileTest extends TestCase
{
    use DatabaseMigrations;     

    /** @test */
    public function a_user_has_a_profile()
    {
        $user = create('App\User');

        $this->get('/profiles/' . $user->name)
            ->assertSee($user->name);
    }

    /** @test */
    public function profiles_display_all_threads_created_by_associated_user()
    {
        $this->signIn();
        
        $threadNotByUser = create('App\Thread');        
        $threadByUser = create('App\Thread', ['user_id' => auth()->id()]);
        // need to change user for create_thread activity as its value
        // is set to auth()->user() even if thread->user_id is different
        auth()->user()->activities()->first()->update(['user_id' => 999]);

        $this->get('/profiles/' . auth()->user()->name)
            ->assertSee($threadByUser->title)
            ->assertSee($threadByUser->body)
            ->assertDontSee($threadNotByUser->title);
    }    
}