<?php

namespace App\Exceptions;

use App\Models\Occupancy;
use Exception;

class CouldNotStoreOccupancy extends Exception
{
    public static function forUnexpectedReasons(Occupancy $occupancy): self
    {
        return new self("The payload was valid but failed when persisting to the database. [" . http_build_query($occupancy->toArray()) . "]");
    }
}
