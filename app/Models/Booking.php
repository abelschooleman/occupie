<?php

namespace App\Models;

use App\Traits\HasStartsAtEndsAt;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory, HasStartsAtEndsAt;

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
