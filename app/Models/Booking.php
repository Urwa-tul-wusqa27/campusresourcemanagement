<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'resource_id',
        'event_name',
        'participants',
        'purpose',
        'start_time',
        'end_time',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'participants' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }
}
