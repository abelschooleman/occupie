<?php

namespace App\Types;

use Illuminate\Support\Carbon;

class Month implements Period
{
    private Carbon $date;

    public function __construct(
        public readonly int $year,
        public readonly int $month
    ) {
        $this->date = Carbon::create($this->year, $this->month);
    }

    public function days(): int
    {
        return $this->date->daysInMonth;
    }

    public function end(): Carbon
    {
        return $this->date->endOfMonth();
    }

    public function start(): Carbon
    {
        return $this->date->startOfMonth();
    }

    public function toString(): string
    {
        return $this->year . '-' . $this->month;
    }

    public static function fromString(string $date): Month
    {
        return new self(...explode('-', $date));
    }
}
