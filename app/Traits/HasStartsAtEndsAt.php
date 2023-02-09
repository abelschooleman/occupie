<?php

namespace App\Traits;

use App\Types\Day;
use App\Types\Month;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

trait HasStartsAtEndsAt
{
    public function scopeOfRooms(Builder $query, Collection $rooms): Builder
    {
        return $query->whereIn('room_id', $rooms->pluck('id'));
    }

    public function scopeOnDate(Builder $query, Day $day): Builder
    {
        return $query->whereDate('starts_at', '<=', $day->date)
            ->whereDate('ends_at', '>=', $day->date);
    }

    public function scopeInMonth(Builder $query, Month $month): Builder
    {
        return $query
            ->where(
                fn ($q) => $q->whereMonth('starts_at', $month->month)
                    ->orWhereMonth('ends_at', $month->month)
            )->orWhere(
                fn ($q) => $q->whereDate('starts_at', '<', $month->start())
                    ->whereDate('ends_at', '>', $month->end())
            );
    }
}
