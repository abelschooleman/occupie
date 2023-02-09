<?php

namespace App\Types;

use Illuminate\Support\Carbon;

class Month implements Period
{
    public function __construct(
        public readonly int $year,
        public readonly int $month
    ) {}

    public function days(): int
    {
        return $this->init()->daysInMonth;
    }

    public function end(): Carbon
    {
        return $this->init()->endOfMonth();
    }

    public function start(): Carbon
    {
        return $this->init()->startOfMonth();
    }

    public function toString(): string
    {
        return $this->year . '-' . $this->month;
    }

    private function init(): Carbon
    {
        return Carbon::create($this->year, $this->month);
    }
}
