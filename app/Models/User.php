<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
        'role', 'city', 'phone', 'address', 'status'
    ];

    protected $hidden = [
        'password', 'remember_token'
    ];

    public function donations()
    {
        return $this->hasMany(Donation::class, 'restaurant_id');
    }

    public function donationRequests()
    {
        return $this->hasMany(DonationRequest::class, 'charity_id');
    }
}