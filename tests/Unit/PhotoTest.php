<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhotoTest extends TestCase
{
	protected $user;

	public function setUp()
	{
		parent::setUp();

        Storage::fake('local');
        $this->user = create('App\User');
        $this->signIn($this->user);
	}

	/** @test */
	function deleting_a_photo_removes_the_image_and_thumbnail_from_storage_and_the_record_from_the_database()
	{
		$this->uploadPhoto();
        $this->assertCount(1, $this->user->photos);
        $photoName = $this->user->photos[0]->path;
        Storage::assertExists('photos/' . $photoName);
        Storage::assertExists('thumbnails/' . $photoName);

        $this->user->photos->each->delete();

        $this->assertCount(0, $this->user->fresh()->photos);
        Storage::assertMissing('photos/' . $photoName);
        Storage::assertMissing('thumbnails/' . $photoName);
	}

	private function uploadPhoto()
	{
		$this->json('POST', 'photos', ['photo' => UploadedFile::fake()->image('avatar.png')]);
	}
}
