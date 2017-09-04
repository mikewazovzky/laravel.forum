<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class FavoritesTest extends TestCase
{
    use DatabaseMigrations;     

    /** @test */
    public function guest_may_not_favorite_a_reply()
    {           
        $this->withExceptionHandling()
            ->post("/replies/1/favorites", [])
            ->assertRedirect('/login');
    }

    /** @test */
    public function an_authenticated_user_may_favorite_a_reply()
    {           
        $this->signIn();
        // Given we have a reply
        $reply = create('App\Reply');
        // When we post to "favorite" endpoint
        $response = $this->post("/replies/{$reply->id}/favorites");
        // Then it shoul be recorded in data base
        $this->assertDatabaseHas('favorites', [
            'favorited_type' => get_class($reply),
            'favorited_id' => $reply->id
        ]);

        $this->assertCount(1, $reply->favorites);
    }

    /** @test */
    public function an_authenticated_user_may_favorite_a_reply_only_once()
    {           
        $this->signIn();
        // Given we have a reply
        $reply = create('App\Reply');
        // When we post to "favorite" endpoint a few times
        try {
            $response = $this->post("/replies/{$reply->id}/favorites");
            $response = $this->post("/replies/{$reply->id}/favorites");            
        } catch(\Exception $e) {
            $this->fail('Error! Can not insert same record twice.');
        }
        // Then it shoul be recorded in data base only once
        $this->assertCount(1, $reply->favorites);
    }    

    /** @test */
    public function guest_may_not_unfavorite_a_reply()
    {           
        $this->withExceptionHandling()
            ->delete("/replies/1/favorites", [])
            ->assertRedirect('/login');
    }

    /** @test */
    public function an_authenticated_user_may_unfavorite_a_reply()
    {           
        // Given we have a reply favorited by the user
        $this->signIn();
        $reply = create('App\Reply');
        // $response = $this->post("/replies/{$reply->id}/favorites");
        $reply->favorite(auth()->user());

        // When we post to "unfavorite" endpoint
        $response = $this->delete("/replies/{$reply->id}/favorites")
            ->assertStatus(302);

        // Then it shoul be deleted from database
        $this->assertCount(0, $reply->favorites);
    }
}