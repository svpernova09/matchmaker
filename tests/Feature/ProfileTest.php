<?php

namespace Tests\Feature;

use App\Photo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
	protected $user;

	function setUp()
	{
		parent::setUp();

		$this->user = create('App\User');
	}

	/** @test */
	function unauthenticated_users_can_not_view_profiles()
	{
		$this->withExceptionHandling()
			->get($this->user->profilePath)
			->assertRedirect('/login');
	}

	/** @test */
	function authenticated_users_have_profiles()
	{
		$this->signIn($this->user)
			->get($this->user->profilePath)
			->assertSee($this->user->username)
			->assertSee($this->user->tagline)
			->assertSee($this->user->profile);
	}

	/** @test */
	function a_male_user_with_no_photo_has_a_male_avatar()
	{
		$this->signIn();

		$user = create('App\User', ['gender' => 'male']);

		$this->get($user->profilePath)
			->assertSee('images/default_user_male.png');
	}

	/** @test */
	function a_female_user_with_no_photo_has_a_female_avatar()
	{
		$this->signIn();

		$user = create('App\User', ['gender' => 'female']);

		$this->get($user->profilePath)
			->assertSee('images/default_user_female.png');
	}

	/** @test */
	function a_user_with_a_photo_has_it_as_an_avatar()
	{
		$this->signIn($user = create('App\User'));

		Photo::create([
			'user_id' => $user->id,
			'path' => 'uploaded_photo.png'
		]);

		$this->get($user->profilePath)
			->assertSee('images/thumbnails/uploaded_photo.png');
	}

	/** @test */
	function the_photo_in_the_first_position_is_the_avatar()
	{
		$this->signIn($user = create('App\User'));

		Photo::create([
			'user_id' => $user->id,
			'path' => 'uploaded_photo1.png'
		]);

		Photo::create([
			'user_id' => $user->id,
			'path' => 'uploaded_photo2.png'
		]);

		$this->assertEquals('images/thumbnails/uploaded_photo1.png', $user->fresh()->getAvatarAttribute());
	}

	/** @test */
	function users_can_update_their_profile()
	{
		$oldProfile = [
			'gender'  => 'male',
			'tagline' => 'This is my old tagline...',
			'profile' => 'I\'ll fill this out later...'
		];

		$updatedProfile = [
			'gender'  => 'female',
			'tagline' => 'This is my new tagline!',
			'profile' => 'Blah blah blah, I hate filling these things out!'
		];

		$user = create('App\User', $oldProfile);

		$this->signIn($user);

		$this->post('profile/edit', $updatedProfile);

		$this->get($user->profilePath)
			->assertSee("images/default_user_{$updatedProfile['gender']}.png")
			->assertSee($updatedProfile['tagline'])
			->assertSee($updatedProfile['profile']);
	}

	/** @test */
	function a_user_can_must_have_a_gender()
	{
		$this->signIn()
			->withExceptionHandling()
			->post('profile/edit', ['gender' => null])
			->assertSessionHasErrors('gender');
	}

	/** @test */
	function a_user_can_be_a_male()
	{
		$user = create('App\User');

		$this->signIn($user)
			->withExceptionHandling()
			->post('profile/edit', ['gender' => 'male'])
			->assertRedirect($user->profilePath);
	}

	/** @test */
	function a_user_can_be_a_female()
	{
		$user = create('App\User');

		$this->signIn($user)
			->withExceptionHandling()
			->post('profile/edit', ['gender' => 'female'])
			->assertRedirect($user->profilePath);
	}

	/** @test */
	function a_user_cannot_be_something_other_than_male_or_female()
	{
		$this->signIn()
			->withExceptionHandling()
			->post('profile/edit', ['gender' => 'unicorn'])
			->assertSessionHasErrors('gender');
	}

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

        Storage::fake('local');

        $response = $this->json('POST', 'photos', [
            'photo' => UploadedFile::fake()->image('avatar.png')
        ]);

        $this->assertEquals(1, $user->photos->count());

        Storage::assertExists(
            'photos/' . $user->photos[0]->photo_path
        );

        Storage::assertExists(
            'thumbnails/' . $user->photos[0]->photo_path
        );
	}

	/** @test */
	function users_can_delete_their_photos()
	{
        Storage::fake('local');
        $this->signIn($user = create('App\User'));
        $this->json('POST', 'photos', ['photo' => UploadedFile::fake()->image('avatar.png')]);

        $photo = $user->photos[0];
        $photoName = $photo->path;
        $this->json('DELETE', "photos/{$photo->id}");

        $this->assertCount(0, $user->fresh()->photos);
        Storage::assertMissing('photos/' . $photoName);
        Storage::assertMissing('thumbnails/' . $photoName);
	}

	/** @test */
	function users_cannot_delete_other_users_photos()
	{
        Storage::fake('local');
        $this->signIn($user = create('App\User'));
        $this->json('POST', 'photos', ['photo' => UploadedFile::fake()->image('avatar.png')]);
        $photo = $user->photos[0];
        $photoName = $photo->path;

        $this->signIn();
        $this->json('DELETE', "photos/{$photo->id}");

        $this->assertCount(1, $user->fresh()->photos);
        Storage::assertExists('photos/' . $photoName);
        Storage::assertExists('thumbnails/' . $photoName);
	}

	/** @test */
	function users_can_rearrange_the_position_of_their_photos()
	{
        $this->signIn($user = create('App\User'));
		$photo1 = Photo::create(['user_id' => $user->id,'path' => 'uploaded_photo.png']);
		$photo2 = Photo::create(['user_id' => $user->id,'path' => 'uploaded_photo.png']);
		$this->assertEquals(1, $photo1->fresh()->position);
		$this->assertEquals(2, $photo2->fresh()->position);
		
        $this->json('POST', "/photos/{$photo2->id}", ['position'=>1]);

		$this->assertEquals(1, $photo2->fresh()->position);
		$this->assertEquals(2, $photo1->fresh()->position);
	}

	/** @test */
	function users_cant_rearrange_the_position_of_other_users_photos()
	{
		$photo1 = Photo::create(['user_id' => '1000','path' => 'uploaded_photo.png']);

        $this->signIn($user = create('App\User', ['id' => '1001']));
        $response = $this->json('POST', "/photos/{$photo1->id}", ['position'=>1]);

        $response->assertSessionHasErrors('position');
	}

	/** @test */
	function profiles_display_user_images()
	{
		$this->signIn($user = create('App\User'));

		Photo::create(['user_id' => $user->id,'path' => 'uploaded_photo.png']);
		Photo::create(['user_id' => $user->id,'path' => 'uploaded_photo1.png']);
		Photo::create(['user_id' => $user->id,'path' => 'uploaded_photo2.png']);

		$this->get($user->profilePath)
			->assertSee('images/thumbnails/uploaded_photo.png')
			->assertSee('images/thumbnails/uploaded_photo1.png')
			->assertSee('images/thumbnails/uploaded_photo2.png');
	}
}
