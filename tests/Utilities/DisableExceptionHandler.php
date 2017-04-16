<?php

namespace Tests\Utilties;

use App\Exceptions\Handler;

class DisableExceptionHandler extends Handler 
{
    public function __construct() {}
    public function report(\Exception $e) {}
    public function render($request, \Exception $e) {
        throw $e;
    }
}
