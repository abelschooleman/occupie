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
        private readonly Collection $forRooms,
        private readonly Period $inPeriod,
    ) {
        $capacity = $this->forRooms->sum('capacity');

        if ($this->inPeriod instanceof Month) {
            $capacity = $capacity * $this->inPeriod->days();
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
            'period' => $this->inPeriod->toString(),
            'rooms' => $this->forRooms->toArray(),
        ];
    }

    private function fetchOccupancy(Occupancy $model): int
    {
        return $model::ofRooms($this->forRooms)
            ->when(
                $this->inPeriod instanceof Day,
                fn ($q) => $q->onDate($this->inPeriod),
                fn ($q) => $q->inMonth($this->inPeriod)
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
