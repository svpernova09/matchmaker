<?php

namespace App;

use App\Uploads\UploadInterface;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
	protected $fillable = ['user_id', 'path'];

    public function owner()
    {
    	return $this->belongsTo('App\User', 'user');
    }

    public static function upload(UploadInterface $file, $userId)
    {
    	$file->store();

    	parent::create([
    		'user_id' => $userId,
    		'path' => $file->getFileName()
    	]);
    }
}
