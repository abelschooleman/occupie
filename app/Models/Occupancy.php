<?php

namespace App\Models;

use App\Types\Day;
use App\Types\Month;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

interface Occupancy
{
    public function room(): BelongsTo;

    public function scopeOfRooms(Builder $query, Collection $rooms): Builder;

    public function scopeOnDate(Builder $query, Day $day): Builder;

    public function scopeInMonth(Builder $query, Month $month): Builder;
}
