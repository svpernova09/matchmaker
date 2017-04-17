<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SignUpTest extends TestCase
{
	/** @test */
	function a_user_can_must_have_a_gender()
	{
		$this->withExceptionHandling()
			->post('/register', make('App\User', ['gender' => null])->toArray())
			->assertSessionHasErrors('gender');
	}

	/** @test */
	function a_user_can_be_a_male()
	{
		$this->withExceptionHandling()
			->post('/register', make('App\User', ['gender' => 'male'])->toArray())
			->assertRedirect('/');
	}

	/** @test */
	function a_user_can_be_a_female()
	{
		$this->withExceptionHandling()
			->post('/register', make('App\User', ['gender' => 'female'])->toArray())
			->assertRedirect('/');
	}

	/** @test */
	function a_user_cannot_be_something_other_than_male_or_female()
	{
		$this->withExceptionHandling()
			->post('/register', make('App\User', ['gender' => 'unicorn'])->toArray())
			->assertSessionHasErrors('gender');
	}
}
