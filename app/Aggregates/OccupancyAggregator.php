<?php

namespace App\Aggregates;

use App\Models\Block;
use App\Models\Booking;
use App\Models\Occupancy;
use App\Types\Day;
use App\Types\Month;
use App\Types\Period;
use DivisionByZeroError;
use Illuminate\Support\Collection;

class OccupancyAggregator
{
    private int $capacity;

    public function __construct(
        private readonly Collection $rooms,
        private readonly Period $period,
    ) {
        $capacity = $this->rooms->sum('capacity');

        if ($period instanceof Month) {
            $capacity = $capacity * $this->period->days();
        }

        $this->capacity = $capacity;
    }

    public function aggregate(): array
    {
        $bookings = $this->fetchOccupancy(new Booking());
        $blocks = $this->fetchOccupancy(new Block());

        return [
            'bookings' => $bookings,
            'blocks' => $blocks,
            'capacity' => $this->capacity,
            'occupancy_rate' => (float) number_format($this->rate($bookings, $blocks), 2),
            'period' => $this->period->toString(),
            'rooms' => $this->rooms->toArray(),
        ];
    }

    private function fetchOccupancy(Occupancy $model): int
    {
        return $model::ofRooms($this->rooms)
            ->when(
                $this->period instanceof Day,
                fn ($q) => $q->onDate($this->period),
                fn ($q) => $q->inMonth($this->period)
            )->count();
    }

    private function rate(int $bookings, int $blocks): float
    {
        try {
            return $bookings / ( $this->capacity - $blocks );
        } catch (DivisionByZeroError) {
            // If capacity - block equals 0, it means there is no capacity left so occupancy must be equal to bookings / capacity === 1.0
            return 1.0;
        }
    }
}
