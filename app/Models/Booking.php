<?php

namespace App\Models;

use App\Traits\HasOccupancy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model implements Occupancy
{
    use HasFactory, HasOccupancy;

    protected $visible = [
        'ends_at',
        'id',
        'room',
        'starts_at',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
