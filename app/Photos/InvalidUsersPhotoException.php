<?php

namespace App\Photos;

/**
 * The user has tried to update a photo that does not belong to them.
 */
class InvalidUsersPhotoException extends \RuntimeException {}