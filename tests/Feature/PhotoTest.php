<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhotoTest extends TestCase
{
    /** @test */
    function unauthenticated_users_cannot_upload_images()
    {
        $this->setExpectedException('Illuminate\Auth\AuthenticationException');

        $this->json('POST', '/photos', []);
    }

	/** @test */
	function authenticated_users_can_upload_a_photo()
	{
        $this->signIn($user = create('App\User'));

        Storage::fake('images');

        $response = $this->json('POST', 'photos', [
            'photo' => UploadedFile::fake()->image('avatar.png')
        ]);

        $this->assertEquals(1, $user->photos->count());

        Storage::assertExists(
            'photos/' . $user->photos[0]->photo_path
        );
	}
}
