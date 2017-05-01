<?php

namespace App\Uploads;

use App\Uploads\UploadInterface;
use Faker\Provider\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class UserPhoto implements UploadInterface
{
	/**
	 * The uploaded file.
	 * 
	 * @var object Illuminate\Http\UploadedFile
	 */
	protected $file;

	/**
	 * Unique filename to associate with the file.
	 * @var string
	 */
	protected $fileName;

	/**
	 * Sets the file and fileName properties.
	 * 
	 * @param Illuminate\Http\UploadedFile $file
	 */
	public function __construct(UploadedFile $file)
	{
		$this->file = $file;

		$this->fileName = time() . '-' . rand(100,9999) . '.' . $this->file->getClientOriginalExtension();
	}

	/**
	 * Getter method to return the fileName property.
	 * 
	 * @return string
	 */
	public function getFileName()
	{
		return $this->fileName;
	}

	/**
	 * The process to save the file to the server and create a thumbnail.
	 * 
	 * @return void
	 */
	public function store()
	{
    	$photo = Storage::putFileAs(
			'photos', $this->file, $this->fileName
		);

    	$this->makeThumbnail($photo);
    	
    	$this->resizeImage($photo);
	}

	/**
	 * Resizes the orriginal image and replaces it.
	 * 
	 * @param  string $photo 	path to photo
	 * @return void
	 */
	protected function resizeImage($photo)
	{
	 	$photo = Storage::get($photo);

		if (! $photo) {
			return;
		}

		$newPhoto = (string) Image::make($photo)
			->resize(750, null, function ($constraint) {
			    $constraint->aspectRatio();
			    $constraint->upsize();
			})
			->stream();


		Storage::put(
			'photos/' . $this->fileName, $newPhoto, 'public'
		);
	}

	/**
	 * Creates a thumbnail of the photo and stores it.
	 * 
	 * @param  string $photo 	path to photo
	 * @return void
	 */
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