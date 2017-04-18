<?php

namespace App\Http\Controllers;

use App\Photo;
use App\Uploads\UserPhoto;
use Illuminate\Http\Request;

class PhotosController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

    public function store(Request $request)
    {
    	$this->validate($request, [
    		'photo' => 'required|mimes:jpg,jpeg,png,bmp,gif'
    	]);

    	Photo::upload(
    		new UserPhoto($request->file('photo')),
            auth()->user()->id
    	);
    }
}
