<?php

namespace App\Repositories;

use App\Exceptions\CouldNotFindOneOrMoreRequestedRooms;
use App\Models\Room;
use Illuminate\Support\Collection;

class RoomRepository
{
    /**
     * @param array $ids
     * @return Collection
     * @throws CouldNotFindOneOrMoreRequestedRooms
     */
    public function get(array $ids): Collection
    {
        if (empty($ids)) {
            return Room::all();
        }

        $rooms = Room::whereIn('id',$ids)->get();

        if ($rooms->count() !== count($ids)) {
            throw CouldNotFindOneOrMoreRequestedRooms::becauseTheRoomCouldNotBeFound($ids);
        }

        return $rooms;
    }
}
