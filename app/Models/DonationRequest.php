<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonationRequest extends Model
{
    protected $fillable = [
        'donation_id', 'charity_id', 'status',
        'requested_at', 'approved_at', 'collected_at'
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at'  => 'datetime',
        'collected_at' => 'datetime',
    ];

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    public function charity()
    {
        return $this->belongsTo(User::class, 'charity_id');
    }
}