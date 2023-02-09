<?php

namespace Tests\Feature;

use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class OccupancyEndpointTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rooms = Room::factory(4)->create();

        $this->date = Carbon::now()->toDateString();
    }

    public function test_daily_occupancy_rates_endpoint_returns_occupancy_rates_of_all_rooms_per_day()
    {
        $response = $this->getJson("api/daily-occupancy-rates/$this->date")
            ->assertStatus(200);

        $this->assertSame($this->date, $response->json('period'));
        $this->assertSame($this->rooms->pluck('id')->toArray(), array_column($response->json('rooms'), 'id'));
    }

    public function test_daily_occupancy_rates_endpoint_returns_occupancy_rates_of_some_rooms_per_day()
    {
        $roomIds = [$this->rooms[0]->id, $this->rooms[2]->id];

        $query = http_build_query(['room_ids' => $roomIds]);

        $response = $this->getJson("api/daily-occupancy-rates/$this->date?$query")
            ->assertStatus(200);

        $this->assertSame($this->date, $response->json('period'));
        $this->assertSame($roomIds, array_column($response->json('rooms'), 'id'));
    }

    public function test_monthly_occupancy_rates_endpoint_of_some_rooms_per_month()
    {
        $date = Carbon::now();
        $month = "{$date->year}-{$date->month}";

        $roomIds = [$this->rooms[0]->id, $this->rooms[2]->id];

        $query = http_build_query(['room_ids' => $roomIds]);

        $response = $this->getJson("api/monthly-occupancy-rates/$month?$query")
            ->assertStatus(200);

        $this->assertSame($month, $response->json('period'));
        $this->assertSame($roomIds, array_column($response->json('rooms'), 'id'));
    }

    public function test_endpoint_returns_404_when_room_id_does_not_exist()
    {
        $roomIds = [97967, 85943];

        $query = http_build_query(['room_ids' => $roomIds]);

        $this->getJson("api/daily-occupancy-rates/$this->date?$query")
            ->assertStatus(404);
    }
}
