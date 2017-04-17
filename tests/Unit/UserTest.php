<?php

namespace Tests\Unit;

use Tests\TestCase;

class UserTest extends TestCase
{
	/** @test */
	function a_user_has_a_string_path_to_their_profiles_url()
	{
		$user = create('App\User');

		$this->assertEquals("/@{$user->username}", $user->profilePath);
	}
}
