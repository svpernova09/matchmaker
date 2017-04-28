<?php

namespace Tests\Unit;

use App\Photo;
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

	/** @test */
	function users_cant_have_more_than_4_photos()
	{
		$this->uploadPhoto();
		$this->uploadPhoto();
		$this->uploadPhoto();
		$this->uploadPhoto();
		
		$this->uploadPhoto()
			->assertSessionHasErrors('photo');
	}

	/** @test */
	function users_can_arrange_their_photos_by_position()
	{
		$this->signIn($user = create('App\User'));
		$photo1 = Photo::create(['user_id' => $user->id,'path' => 'uploaded_photo.png']);
		$photo2 = Photo::create(['user_id' => $user->id,'path' => 'uploaded_photo1.png']);
		$photo3 = Photo::create(['user_id' => $user->id,'path' => 'uploaded_photo2.png']);
		$this->assertEquals($photo1->id, $user->photos[0]->id);
		$this->assertEquals($photo2->id, $user->photos[1]->id);
		$this->assertEquals($photo3->id, $user->photos[2]->id);

		$photo1->update(['position'=>2]);
		$photo2->update(['position'=>3]);
		$photo3->update(['position'=>1]);

		$this->assertEquals($photo3->id, $user->fresh()->photos[0]->id);
		$this->assertEquals($photo1->id, $user->fresh()->photos[1]->id);
		$this->assertEquals($photo2->id, $user->fresh()->photos[2]->id);
	}

	/** @test */
	function a_new_photos_position_is_equal_to_the_number_of_photos_for_that_user()
	{
		$this->signIn($user = create('App\User'));
		
		$photo1 = Photo::create(['user_id' => $user->id,'path' => 'uploaded_photo.png']);
		$photo2 = Photo::create(['user_id' => $user->id,'path' => 'uploaded_photo.png']);
		$photo3 = Photo::create(['user_id' => $user->id,'path' => 'uploaded_photo.png']);

		$this->assertEquals(1, $photo1->fresh()->position);
		$this->assertEquals(2, $photo2->fresh()->position);
		$this->assertEquals(3, $photo3->fresh()->position);
	}

	/** @test */
	function photo_positions_are_reordered_on_delete()
	{
		$this->signIn($user = create('App\User'));
		
		$photo1 = Photo::create(['user_id' => $user->id,'path' => 'uploaded_photo.png']);
		$photo2 = Photo::create(['user_id' => $user->id,'path' => 'uploaded_photo.png']);
		$photo3 = Photo::create(['user_id' => $user->id,'path' => 'uploaded_photo.png']);
		$this->assertEquals(1, $photo1->fresh()->position);
		$this->assertEquals(2, $photo2->fresh()->position);
		$this->assertEquals(3, $photo3->fresh()->position);

		$photo2->delete();

		$this->assertEquals(1, $photo1->fresh()->position);
		$this->assertEquals(2, $photo3->fresh()->position);
	}

	private function uploadPhoto()
	{
		return $this->json('POST', 'photos', ['photo' => UploadedFile::fake()->image('avatar.png')]);
	}
}
