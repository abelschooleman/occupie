<?php

namespace Tests\Feature;

use App\Generators\OccupancyGenerator;
use App\Models\Booking;
use App\Models\Room;
use App\Types\Day;
use App\Types\Month;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class HasStartsAtEndsAtTraitTest extends TestCase
{
    use RefreshDatabase;

    public function test_result_only_includes_selected_rooms()
    {
        $rooms = Room::factory(5)
            ->has(Booking::factory()->state(['starts_at' => Carbon::now(), 'ends_at' => Carbon::now()->addDay()]))
            ->create();

        $included = $rooms->take(3);

        $bookings = Booking::ofRooms($included)
            ->onDate(new Day(Carbon::now()))
            ->get();

        $this->assertEquals($included->pluck('id'), $bookings->pluck('room_id'));
    }

    /**
     * @dataProvider dailyBookingProvider
     */
    public function test_on_day_query_returns_any_records_which_start_and_end_dates_include_selected_date(array $dates, bool $inResult, Day $onDay)
    {
        $booking = (new OccupancyGenerator(Booking::class))
            ->room(Room::factory()->create())
            ->from($dates['from'])
            ->to($dates['to'])
            ->save();

        $result = Booking::onDate($onDay)
            ->get()
            ->pluck('id');

        $this->assertTrue($result->contains($booking->id) === $inResult);
    }

    /**
     * @dataProvider monthlyBookingProvider
     */
    public function test_in_month_query_returns_any_records_which_duration_at_least_partly_covers_the_selected_month(array $dates, bool $inResult, Month $inMonth)
    {
        $booking = (new OccupancyGenerator(Booking::class))
            ->room(Room::factory()->create())
            ->from($dates['from'])
            ->to($dates['to'])
            ->save();

        $result = Booking::inMonth($inMonth)
            ->get()
            ->pluck('id');

        $this->assertTrue($result->contains($booking->id) === $inResult);
    }

    public function monthlyBookingProvider(): Generator
    {
        $date = Carbon::now();
        $month = new Month($date->year, $date->month);

        yield from [
            'start and end date are within month' => [
                'dates' => [
                    'from' => $date,
                    'to' => $date,
                ],
                'included_in_result' => true,
                'in_month' => $month,
            ],
            'start date is before month but end date is in month' => [
                'dates' => [
                    'from' => $date->copy()->subMonth(),
                    'to' => $date,
                ],
                'included_in_result' => true,
                'in_month' => $month,
            ],
            'end date is after month but start date is in month' => [
                'dates' => [
                    'from' => $date,
                    'to' => $date->copy()->addMonth(),
                ],
                'included_in_result' => true,
                'in_month' => $month,
            ],
            'start date is before month and end date is after month' => [
                'dates' => [
                    'from' => $date->copy()->subMonth(),
                    'to' => $date->copy()->addMonth(),
                ],
                'included_in_result' => true,
                'in_month' => $month,
            ],
            'start and end date are before month' => [
                'dates' => [
                    'from' => $date->copy()->subMonths(2),
                    'to' => $date->copy()->subMonth(),
                ],
                'included_in_result' => false,
                'in_month' => $month,
            ],
            'start and end date are after month' => [
                'dates' => [
                    'from' => $date->copy()->addMonth(),
                    'to' => $date->copy()->addMonths(2),
                ],
                'included_in_result' => false,
                'in_month' => $month,
            ],
        ];
    }

    public function dailyBookingProvider(): Generator
    {
        $date = Carbon::now();
        $day = new Day($date);

        yield from [
            'on selected date' => [
                'dates' => [
                    'from' => $date,
                    'to' => $date,
                ],
                'included_in_result' => true,
                'on_day' => $day,
            ],
            'starts before selected date' => [
                'dates' => [
                    'from' => $date->copy()->subDay(),
                    'to' => $date,
                ],
                'included_in_result' => true,
                'on_day' => $day,
            ],
            'ends after selected date' => [
                'dates' => [
                    'from' => $date,
                    'to' => $date->copy()->addDay(),
                ],
                'included_in_result' => true,
                'on_day' => $day,
            ],
            'starts before and ends after selected date' => [
                'dates' => [
                    'from' => $date->copy()->subDay(),
                    'to' => $date->copy()->addDay(),
                ],
                'included_in_result' => true,
                'on_day' => $day,
            ],
            'starts and ends before selected date' => [
                'dates' => [
                    'from' => $date->copy()->subDays(2),
                    'to' => $date->copy()->subDay(),
                ],
                'included_in_result' => false,
                'on_day' => $day,
            ],
            'starts and ends after selected date' => [
                'dates' => [
                    'from' => $date->copy()->addDay(),
                    'to' => $date->copy()->addDays(2),
                ],
                'included_in_result' => false,
                'on_day' => $day,
            ],
        ];
    }
}
