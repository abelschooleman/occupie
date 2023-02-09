<?php

namespace App\Exceptions;

use Exception;

class CouldNotFindOneOrMoreRequestedRooms extends Exception
{
    public static function becauseTheRoomCouldNotBeFound(array $ids): self
    {
        return new self('One or more of the rooms in [' . implode(',', $ids) .'] does not exist');
    }
}
