<?php

namespace App\Uploads;

use Illuminate\Http\UploadedFile;

interface UploadInterface
{
	public function __construct(UploadedFile $file);
	public function getFileName();
	public function store();
}