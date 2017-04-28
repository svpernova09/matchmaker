<?php

namespace App;

use App\Photos\InvalidUsersPhotoException;
use App\Uploads\UploadInterface;
use App\Uploads\UserPhotoLimitException;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
	protected $fillable = ['user_id', 'path', 'position'];

    public static function boot()
    {
        parent::boot();

        self::created(function ($photo) {
            $photo->lastPosition();
        });

        self::deleting(function ($photo) {
            Storage::delete([
                'photos/' . $photo->path,
                'thumbnails/' . $photo->path
            ]);
        });

        self::deleted(function ($photo) {
            $photo::renumberPostionsToAccountForNewlyDeletedPhotos();
        });
    }
    
    public function owner()
    {
    	return $this->belongsTo('App\User', 'user');
    }

    public static function upload(UploadInterface $file, $userId)
    {
        if (parent::whereUserId($userId)->count() > 3) {
            throw new UserPhotoLimitException;
        }

    	$file->store();

    	parent::create([
    		'user_id' => $userId,
    		'path' => $file->getFileName()
    	]);
    }

    public function lastPosition()
    {
        $numberOfPhotos = $this->whereUserId(auth()->id())->count();
        
        $this->update(['position' => $numberOfPhotos]);
    }

    public static function renumberPostionsToAccountForNewlyDeletedPhotos()
    {
        $position = 1;
        $photos = parent::whereUserId(auth()->id())->orderBy('position')->orderBy('updated_at')->get();
        foreach ($photos as $photo) {
            $photo->update(['position' => $position]);
            $position++;
        }
    }

    public static function updatePosition(User $user, $photoId, $newPosition)
    {
        // get all the authenticated users photos
        $photos = $user->photos->pluck('id')->toArray();

        // if the photoId they sent isn't in the array, then it isn't their photo
        $currentPosition = array_search($photoId, $photos);
        if (! $currentPosition) {
            throw new InvalidUsersPhotoException;
        }

        // remove the photo from the array
        unset($photos[$currentPosition]);

        // loop over the remaning photos and update their positions starting from one.
        $x = 1;
        foreach ($photos as $photo) {
            // if the current iteration is at the requested new position, updated it by one to create a gap.
            if ($x == $newPosition) $x++;
            parent::whereId($photo)->update(['position'=>$x]);
            $x++;
        }

        // Update the photo to the desired position.
        parent::whereId($photoId)->update(['position' => $newPosition]);
    }
}
