<?php

namespace Tests;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Utilties\DisableExceptionHandler;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseTransactions;

    protected $oldExceptionHandler;

    protected function setUp()
    {
        parent::setUp();
        
        $this->disableExceptionHandling();
    }

    protected function signIn($user = null)
    {
    	$user = $user ?: create('App\User');

    	$this->actingAs($user);

    	return $this;
    }

    protected function disableExceptionHandling()
    {
        $this->oldExceptionHandler = $this->app->make(ExceptionHandler::class);

        $this->app->instance(ExceptionHandler::class, new DisableExceptionHandler);
    }

    protected function withExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, $this->oldExceptionHandler);

        return $this;
    }
}

