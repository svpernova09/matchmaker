<?php

namespace App\Http\Controllers;

use App\Photo;
use App\Photos\InvalidUsersPhotoException;
use App\Uploads\UserPhoto;
use App\Uploads\UserPhotoLimitException;
use Illuminate\Http\Request;

class PhotosController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
	public function __construct()
	{
		$this->middleware('auth');
	}

    /**
     * Allows the authenticated user to manage their photos. Upload/Delete/Rearrange
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $photos = auth()->user()->photos;

        return view('profile.photos', compact('photos'));
    }

    /**
     * POST request for the authenticated user to upload a new photo.
     * 
     * @param  Illuminate\Http\Request $request
     * 
     * @return redirect
     */
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

    /**
     * POST request for the authenticated user to re-order a photos position.
     *
     * @param integer  $id  The photos id.
     * @param  Illuminate\Http\Request $request
     * 
     * @return redirect
     */
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

    /**
     * DELETE request for the authenticated user to remove an existing photo.
     * 
     * @param integer  $id  The photos id.
     * 
     * @return redirect
     */
    public function destroy($id)
    {
        if ($photo = Photo::whereId($id)->whereUserId(auth()->id())->first()) {
            $photo->delete();
        }

        return redirect()->back();
    }
}
