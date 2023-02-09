<?php

namespace App\Exceptions;

use App\Models\Block;
use App\Models\Booking;
use Exception;

class CouldNotStoreOccupancy extends Exception
{
    public static function becauseTheRoomCouldNotBeFound(string $uuid): self
    {
        return new self("A room with uuid [$uuid] does not exist");
    }

    public static function forUnexpectedReasons(Booking | Block $occupancy): self
    {
        return new self("The payload was valid but failed when persisting to the database. [" . http_build_query($occupancy->toArray()) . "]");
    }
}
