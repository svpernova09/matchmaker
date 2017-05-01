<?php

namespace App\Uploads;

/**
 * The user has tried to upload more photos then they are allowed to.
 */
class UserPhotoLimitException extends \RuntimeException {}