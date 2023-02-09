<?php

namespace App\Models;

use App\Traits\HasStartsAtEndsAt;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Block extends Model
{
    use HasFactory, HasStartsAtEndsAt;

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
