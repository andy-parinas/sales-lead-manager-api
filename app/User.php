<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    const HEAD_OFFICE = 'head_office';
    const FRANCHISE_ADMIN = 'franchise_admin';
    const STAFF_USER = 'staff_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'username', 'name', 'email', 'password', 'user_type'
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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function franchises()
    {
        return $this->belongsToMany(Franchise::class);
            // ->where('franchises.parent_id', '<>', null);
    }

    public function isHeadOffice()
    {
        return $this->user_type === User::HEAD_OFFICE;
    }

    public function isFranchiseAdmin()
    {
        return $this->user_type === User::FRANCHISE_ADMIN;
    }

    public function isStaffUser()
    {
        return $this->user_type === User::STAFF_USER;
    }


}
