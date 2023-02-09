<?php

namespace Tests\Feature;

use App\Generators\OccupancyGenerator;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class OccupancyGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->model = Booking::class;

        $this->builder = new OccupancyGenerator($this->model);
    }

    function test_instantiating_builder_initiates_instance_of_given_model () {
        $this->assertTrue($this->builder->get() instanceof $this->model);
    }

    function test_setting_from_date_updates_instance_with_given_start_date () {
        $fromDate = Carbon::now();

        $this->builder->from($fromDate);

        $this->assertSame($fromDate->toDateString(), $this->builder->get()->starts_at);
    }

    function test_setting_to_date_updates_instance_with_given_end_date () {
        $fromDate = Carbon::now();

        $this->builder->to($fromDate);

        $this->assertSame($fromDate->toDateString(), $this->builder->get()->ends_at);
    }

    function test_setting_room_attaches_given_room_to_instance () {
        $room = Room::factory()->make();

        $this->builder->room($room);

        $this->assertSame($room, $this->builder->get()->room);
    }
}
