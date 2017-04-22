<?php

namespace App\Uploads;

use App\Uploads\UploadInterface;
use Faker\Provider\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class UserPhoto implements UploadInterface
{
	protected $file;

	protected $fileName;

	public function __construct(UploadedFile $file)
	{
		$this->file = $file;

		$this->fileName = time() . '-' . rand(100,9999) . '.' . $this->file->getClientOriginalExtension();
	}

	public function getFileName()
	{
		return $this->fileName;
	}

	public function store()
	{
    	$photo = Storage::putFileAs(
			'photos', $this->file, $this->fileName
		);

    	$this->makeThumbnail($photo);
	}

	protected function makeThumbnail($photo)
	{
	 	$photo = Storage::get($photo);

		if (! $photo) {
			return;
		}

		$thumbnail = (string) Image::make($photo)
			->fit(175, 175)
			->stream();

		Storage::put(
			'thumbnails/' . $this->fileName, $thumbnail, 'public'
		);
	}
}