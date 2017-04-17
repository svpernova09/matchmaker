<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class ProfilesController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

    public function show($username)
    {
    	$user = User::whereUsername($username)->firstOrFail();

    	return view('profile', compact('user'));
    }
}
