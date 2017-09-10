<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AddAvatarTest extends TestCase
{
    use DatabaseMigrations;     

    /** @test */
    public function only_members_can_test_avatars()
    {
        $this->withExceptionHandling();
        $this->json('POST', '/api/users/1/avatar', [])
            ->assertStatus(401);
    }

    /** @test */
    public function a_valid_avatar_must_be_provided()
    {
        $this->withExceptionHandling()->signIn();

        $url = '/api/users/' .  auth()->id() . '/avatar';
        $this->json('POST', $url, [
            'avatar' => 'not-an-image'
        ])->assertStatus(422);
    }    

    /** @test */
    public function a_user_may_add_an_avatar_to_their_profile()
    {
        // fake Storage and UploadedFile
        Storage::fake('public');

        $this->signIn();

        $this->json('POST', '/api/users/' .  auth()->id() . '/avatar', [
            'avatar' => $file = UploadedFile::fake()->image('avatar.jpg')
        ]);

        $this->assertEquals('avatars/' . $file->hashName(), auth()->user()->avatar_path);

        Storage::disk('public')->assertExists('avatars/' . $file->hashName());
    }    

}