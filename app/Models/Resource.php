<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resource extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'type',
        'capacity',
        'features',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'capacity' => 'integer',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
