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
}