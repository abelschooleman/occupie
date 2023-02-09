<?php

namespace App\Types;

use Illuminate\Support\Carbon;

class Day implements Period
{
    public Carbon $date;

    public function __construct(
        public readonly int $year,
        public readonly int $month,
        public readonly int $day,
    ) {
        $this->date = Carbon::create($this->year, $this->month, $this->day);
    }

    public function toString(): string
    {
        return $this->date->toDateString();
    }

    public static function fromDate(Carbon $date): Day
    {
        return new self($date->year, $date->month, $date->day);
    }
}
