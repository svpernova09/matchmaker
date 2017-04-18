<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfilesController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

    public function show($username)
    {
    	$user = User::whereUsername($username)->firstOrFail();

    	return view('profile.view', compact('user'));
    }

    public function edit()
    {
        $user = User::whereId(auth()->id())->first();

        return view('profile.edit', compact('user'));
    }

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
