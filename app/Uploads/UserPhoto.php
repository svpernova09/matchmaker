<?php

namespace App\Uploads;

use App\Uploads\UploadInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UserPhoto implements UploadInterface
{
	protected $file;

	protected $fileName;

	public function __construct(UploadedFile $file)
	{
		$this->file = $file;

		$this->fileName = time() . rand(100,9999) . '-' . $this->file->getClientOriginalName();
	}

	public function getFileName()
	{
		return $this->fileName;
	}

	public function store()
	{
    	Storage::putFileAs(
			'photos', $this->file, $this->fileName
		);
	}
}