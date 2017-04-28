<?php

namespace App\Http\Controllers;

use App\Photo;
use App\Photos\InvalidUsersPhotoException;
use App\Uploads\UserPhoto;
use App\Uploads\UserPhotoLimitException;
use Illuminate\Http\Request;

class PhotosController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

    public function index()
    {
        $photos = auth()->user()->photos;

        return view('profile.photos', compact('photos'));
    }

    public function store(Request $request)
    {
    	$this->validate($request, [
    		'photo' => 'required|mimes:jpg,jpeg,png,bmp,gif'
    	]);

        try {
        	Photo::upload(
        		new UserPhoto($request->file('photo')),
                auth()->user()->id
        	);            
        }

        catch (UserPhotoLimitException $e) {
            return redirect()->back()->withErrors(['photo' => 'You can only have 4 photos.']);
        }

        return redirect()->back();
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'position' => 'required|integer'
        ]);

        try {
            Photo::updatePosition(auth()->user(), $id, $request->position);
        } 

        catch (InvalidUsersPhotoException $e) {
            return redirect()->back()->withErrors(['position' => 'You can only reposition your photos.']);
        }

        return redirect()->back();
    }

    public function destroy($id)
    {
        if ($photo = Photo::whereId($id)->whereUserId(auth()->id())->first()) {
            $photo->delete();
        }

        return redirect()->back();
    }
}
