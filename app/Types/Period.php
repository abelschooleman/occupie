<?php

namespace App\Types;

interface Period
{
    public static function fromString(string $date): Period;

    public function toString(): string;
}
