<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = [
        'restaurant_id', 'title', 'description',
        'quantity', 'quantity_unit', 'expiry_time',
        'image', 'city', 'status'
    ];

    protected $casts = [
        'expiry_time' => 'datetime',
    ];

    public function restaurant()
    {
        return $this->belongsTo(User::class, 'restaurant_id');
    }

    public function requests()
    {
        return $this->hasMany(DonationRequest::class);
    }
}