<?php

namespace App\Types;

use Illuminate\Support\Carbon;

class Day implements Period
{
    public function __construct(
        public Carbon $date
    ) {}

    public static function fromString(string $date): Day
    {
        return new self(Carbon::createFromFormat('Y-m-d', $date));
    }

    public function toString(): string
    {
        return $this->date->toDateString();
    }
}
