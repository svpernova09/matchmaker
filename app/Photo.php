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
    /**
     * Attributes may be set through mass assignment.
     * 
     * @var array
     */
	protected $fillable = ['user_id', 'path', 'position'];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
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
    
    /**
     * A photo belongs to a user.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
    	return $this->belongsTo('App\User', 'user');
    }

    /**
     * Moves the file to the server and persists to the database.
     * 
     * @param  App\Uploads\UploadInterface $file
     * @param  int $userId The authenticated users id.
     * 
     * @return void
     */
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

    /**
     * Reorders a given photo to the last position available.
     * 
     * @return void
     */
    public function lastPosition()
    {
        $numberOfPhotos = $this->whereUserId(auth()->id())->count();
        
        $this->update(['position' => $numberOfPhotos]);
    }

    /**
     * Renumbers the position of all photos so that there are no gaps when a photo gets deleted.
     * 
     * @return void
     */
    public static function renumberPostionsToAccountForNewlyDeletedPhotos()
    {
        $position = 1;
        $photos = parent::whereUserId(auth()->id())->orderBy('position')->orderBy('updated_at')->get();
        foreach ($photos as $photo) {
            $photo->update(['position' => $position]);
            $position++;
        }
    }

    /**
     * Reorders all photo positions based on the new position of a given photo.
     * 
     * @param  App\User   $user
     * @param  integer $photoId
     * @param  integer $newPosition
     * @return void
     */
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
