<?php

namespace Tests\Unit;

use App\Location;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LocationTest extends TestCase
{
	/** @test */
	function returns_only_zip_codes_within_a_given_radius()
	{
		Location::create([
			'zip' => '33611', 
			'city' => 'Tampa', 
			'state' => 'Florida', 
			'lat' => '27.8914', 
			'lng' => '-82.5067'
		]);
		
		Location::create([
			'zip' => '33616', 
			'city' => 'Tampa', 
			'state' => 'Florida', 
			'lat' => '27.8742', 
			'lng' => '-82.5203'
		]);

		Location::create([
			'zip' => '99521', 
			'city' => 'Anchorage', 
			'state' => 'Alaska', 
			'lat' => '61.2181', 
			'lng' => '-149.9003'
		]);

		$returnedZips = Location::zipWithin(33611, 5);

		$this->assertCount(2, $returnedZips);
		$this->assertContains('33611', $returnedZips);
		$this->assertContains('33616', $returnedZips);
		$this->assertNotContains('99521', $returnedZips);
	}
}
