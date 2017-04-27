<?php

namespace App;

use App\Uploads\UploadInterface;
use App\Uploads\UserPhotoLimitException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
	protected $fillable = ['user_id', 'path'];

    public static function boot()
    {
        parent::boot();

        self::deleting(function ($photo) {
            Storage::delete([
                'photos/' . $photo->path,
                'thumbnails/' . $photo->path
            ]);
        });
    }
    public function owner()
    {
    	return $this->belongsTo('App\User', 'user');
    }

    public static function upload(UploadInterface $file, $userId)
    {
        if (parent::whereUserId($userId)->count() > 3) throw new UserPhotoLimitException;

    	$file->store();

    	parent::create([
    		'user_id' => $userId,
    		'path' => $file->getFileName()
    	]);
    }
}
