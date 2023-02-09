<?php

namespace App\Actions;

use App\Generators\OccupancyGenerator;
use App\Exceptions\CouldNotStoreOccupancy;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Support\Carbon;

class ChangeBooking
{
    public function __construct(
        private readonly Booking $booking,
        private readonly Carbon  $from,
        private readonly Room    $room,
        private readonly Carbon  $to
    ) {}

    /**
     * @throws CouldNotStoreOccupancy
     */
    public function submit(): Booking
    {
        return (new OccupancyGenerator($this->booking))
            ->room($this->room)
            ->from($this->from)
            ->to($this->to)
            ->save();
    }
}
