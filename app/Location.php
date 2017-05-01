<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Jackpopp\GeoDistance\GeoDistanceTrait;

class Location extends Model {

    use GeoDistanceTrait;

    /**
     * Turn off mass assignment protection.
     * @var array
     */
    protected $guarded = [];

    /**
     * Turn off timestamps for this model.
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Return an array of zip codes within a radius.
     * 
     * @param  int $zip
     * @param  int $miles
     * @return array
     */
    public static function zipWithin($zip, $miles)
    {
    	if (! $zip = parent::whereZip($zip)->first(['lat', 'lng']) ) {
    		return [];
    	}

		parent::within($miles, 'miles', $zip->lat, $zip->lng)
            ->get()->each(function ($location) use (&$matchingZipCodes) {
				$matchingZipCodes[] = $location->zip;
			});

		return $matchingZipCodes;
    }
}