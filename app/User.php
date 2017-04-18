<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'gender', 'tagline', 'profile'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * A url to the user's profile.
     * 
     * @return string
     */
    public function getProfilePathAttribute()
    {
        return '/@' . $this->username;
    }

    /**
     * Preserves line-breaks in user profile while sanitizing with htmlentities.
     * 
     * @return string
     */
    public function getFormattedProfileAttribute()
    {
        return nl2br(e($this->profile));
    }
}
