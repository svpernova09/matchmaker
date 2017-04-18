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

    public function getAvatarAttribute()
    {
        $avatar = $this->profileImage;

        if ($avatar) {
            return "images/users/{$avatar->path}";
        }

        return "images/default_user_{$this->gender}.png";
    }

    public function profileImage()
    {
        return $this->hasOne('App\Photo')->latest();
    }

    public function photos()
    {
        return $this->hasMany('App\Photo')->latest();
    }
}
