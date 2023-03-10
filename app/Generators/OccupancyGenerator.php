<?php

namespace App\Generators;

use App\Exceptions\CouldNotStoreOccupancy;
use App\Models\Block;
use App\Models\Booking;
use App\Models\Occupancy;
use App\Models\Room;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class OccupancyGenerator
{
    private Occupancy $occupancy;

    public function __construct(Occupancy | string $model) {
        is_string($model) ?
            $this->occupancy = new $model() :
            $this->occupancy = $model;
    }

    public static function for(Occupancy | string $model): OccupancyGenerator
    {
        return new self($model);
    }

    public function from(Carbon $date): OccupancyGenerator
    {
        return $this->apply('starts_at', $date->toDateString());
    }

    public function get(): Occupancy
    {
        return $this->occupancy;
    }

    public function room(Room $room): OccupancyGenerator
    {
        return $this->apply('room', $room);
    }

    /**
     * @throws CouldNotStoreOccupancy
     */
    public function save(): Occupancy
    {
        if (!$this->occupancy->save()) {
            throw CouldNotStoreOccupancy::forUnexpectedReasons($this->occupancy);
        }

        return $this->get();
    }

    public function to(Carbon $date): OccupancyGenerator
    {
        return $this->apply('ends_at', $date->toDateString());
    }

    private function apply(string $property, mixed $value): OccupancyGenerator
    {
        if ($value instanceof Room) {
            $this->occupancy->room()->associate($value);
        } else {
            $this->occupancy->$property = $value;
        }

        return $this;
    }
}
