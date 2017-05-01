<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfilesController extends Controller
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
     * Display a users profile.
     * 
     * @param  string $username The users unique username.
     * 
     * @return \Illuminate\Http\Response
     */
    public function show($username)
    {
    	$user = User::whereUsername($username)->firstOrFail();

    	return view('profile.view', compact('user'));
    }

    /**
     * Display the authenticated users edit profile page.
     * 
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $user = User::whereId(auth()->id())->first();

        return view('profile.edit', compact('user'));
    }

    /**
     * POST request for the authenticated user to update their profile.
     *
     * @param  Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
    	$this->validate($request, [
    		'gender' => ['required', Rule::in(['male', 'female'])]
    	]);

    	$user = User::whereId(auth()->id())->first();
        $user->update([
            'gender' => request('gender'),
            'tagline' => request('tagline'),
            'profile' => request('profile')
        ]);

    	return redirect($user->profilePath);
    }
}
