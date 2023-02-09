<?php

namespace Tests\Feature;

use App\Aggregates\OccupancyAggregator;
use App\Generators\OccupancyGenerator;
use App\Models\Block;
use App\Models\Booking;
use App\Models\Room;
use App\Types\Day;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class OccupancyAggregateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @dataProvider roomProvider
     */
    public function test_aggregator_is_initialised_with_given_rooms(int $included, int $excluded)
    {
        $rooms = Room::factory($included + $excluded)->create();

        $expected = $rooms->take($included);

        $aggregate = (new OccupancyAggregator($expected, Day::fromDate(Carbon::now())))->aggregate();

        $this->assertEquals(
            $expected->pluck('id')->toArray(),
            array_column($aggregate['rooms'], 'id'),
        );
    }

    /**
     * @dataProvider roomProvider
     */
    public function test_aggregator_capacity_equals_sum_of_given_rooms_capacity(int $included, int $excluded)
    {
        $rooms = Room::factory($included + $excluded)->create();

        $expected = $rooms->take($included);

        $aggregate = (new OccupancyAggregator($expected, Day::fromDate(Carbon::now())))->aggregate();

        $this->assertEquals(
            $expected->sum('capacity'),
            $aggregate['capacity'],
        );
    }

    /**
     * @dataProvider occupancyProvider
     * */
    public function test_aggregate_shows_expected_number_of_bookings_blocks_and_rate(int $capacity, int $bookings, int $blocks, float $occupancyRate) {
        $observedDay = Carbon::now();

        $observedRoom = Room::factory()->create(['capacity' => $capacity]);

        for ($i = 0; $i < $bookings; $i++) {
            (new OccupancyGenerator(Booking::class))
                ->room($observedRoom)
                ->from($observedDay)
                ->to($observedDay->copy()->addDay())
                ->get()
                ->save();
        }

        for ($i = 0; $i < $blocks; $i++) {
            (new OccupancyGenerator(Block::class))
                ->room($observedRoom)
                ->from($observedDay)
                ->to($observedDay->copy()->addDay())
                ->get()
                ->save();
        }

        $aggregate = (new OccupancyAggregator(collect([$observedRoom]), Day::fromDate($observedDay)))->aggregate();

        $this->assertSame(
            [
                'bookings' => $bookings,
                'blocks' => $blocks,
                'occupancy_rate' => $occupancyRate
            ],
            [
                'bookings' => $aggregate['bookings'],
                'blocks' => $aggregate['blocks'],
                'occupancy_rate' => $aggregate['occupancy_rate'
                ]
            ],
        );
    }

    public function roomProvider(): Generator
    {
        yield from [
            'all rooms included' => [
                'included' => 3,
                'excluded' => 0,
            ],
            'some rooms included' => [
                'included' => 3,
                'excluded' => 2,
            ],
            'no rooms included' => [
                'included' => 0,
                'excluded' => 3,
            ],
        ];
    }

    public function occupancyProvider(): Generator
    {
        yield from [
            'full capacity available' => [
                'capacity' => 2,
                'bookings' => 0,
                'blocks' => 0,
                'occupancy_rate' => 0.0,
            ],
            'some bookings and block but capacity left' => [
                'capacity' => 6,
                'bookings' => 2,
                'blocks' => 1,
                'occupancy_rate' => 0.4,
            ],
            'sum of bookings and blocks equals capacity' => [
                'capacity' => 4,
                'bookings' => 3,
                'blocks' => 1,
                'occupancy_rate' => 1.0,
            ],
            'no capacity left due to blocks' => [
                'capacity' => 2,
                'bookings' => 0,
                'blocks' => 2,
                'occupancy_rate' => 1.0,
            ],
        ];
    }
}
