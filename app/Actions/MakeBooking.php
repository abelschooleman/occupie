<?php

namespace App\Actions;

use App\Generators\OccupancyGenerator;
use App\Exceptions\CouldNotStoreOccupancy;
use App\Models\Booking;
use App\Models\Occupancy;
use App\Models\Room;
use Illuminate\Support\Carbon;

class MakeBooking
{
    public function __construct(
        private readonly Carbon $from,
        private readonly Room $room,
        private readonly Carbon $to
    ) {}

    /**
     * @throws CouldNotStoreOccupancy
     */
    public function submit(): Occupancy
    {
        return OccupancyGenerator::for(Booking::class)
            ->room($this->room)
            ->from($this->from)
            ->to($this->to)
            ->save();
    }
}
