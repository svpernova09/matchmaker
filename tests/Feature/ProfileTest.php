<?php

namespace Tests\Feature;

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
	function authenticated_users_have_profiles()
	{
		$this->signIn($this->user)
			->get($this->user->profilePath)
			->assertSee($this->user->username)
			->assertSee($this->user->tagline)
			->assertSee($this->user->profile);
	}

	/** @test */
	function unauthenticated_users_can_not_view_profiles()
	{
		$this->withExceptionHandling()
			->get($this->user->profilePath)
			->assertRedirect('/login');
	}

	/** @test */
	function a_male_user_with_no_images_has_a_male_avatar()
	{
		$this->signIn();

		$user = create('App\User', ['gender' => 'male']);

		$this->get($user->profilePath)
			->assertSee('images/default_user_male.png');
	}

	/** @test */
	function a_female_user_with_no_images_has_a_female_avatar()
	{
		$this->signIn();

		$user = create('App\User', ['gender' => 'female']);

		$this->get($user->profilePath)
			->assertSee('images/default_user_female.png');
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
}
