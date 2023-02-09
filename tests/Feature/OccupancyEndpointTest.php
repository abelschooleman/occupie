<?php

namespace Tests\Feature;

use Generator;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class OccupancyEndpointTest extends TestCase
{
    /**
     * @dataProvider roomProvider
     * */
    public function test_daily_occupancy_rates_endpoint_returns_occupancy_rates_per_day(array $roomIds)
    {
        $date = Carbon::now()->toDateString();

        $query = http_build_query($roomIds);

        $response = $this->getJson("api/daily-occupancy-rates/$date?$query")
            ->assertStatus(200);

        $this->assertSame($date, $response->json('period'));
    }

    /**
     * @dataProvider roomProvider
     * */
    public function test_monthly_occupancy_rates_endpoint_returns_occupancy_rates_per_month(array $roomIds)
    {
        $date = Carbon::now();
        $month = "{$date->year}-{$date->month}";

        $query = http_build_query($roomIds);

        $response = $this->getJson("api/monthly-occupancy-rates/$month?$query")
            ->assertStatus(200);

        $this->assertSame($month, $response->json('period'));
    }

    public function roomProvider(): Generator
    {
        yield from [
            'all rooms' => [
                'room_ids' => [],
            ],
            'some rooms' => [
                'room_ids' => [1, 2, 4],
            ],
        ];
    }
}
