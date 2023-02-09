<?php

namespace App\Models;

use App\Traits\HasOccupancy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Block extends Model implements Occupancy
{
    use HasFactory, HasOccupancy;

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
