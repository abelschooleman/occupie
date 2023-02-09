<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Room;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BookingEndpointTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->room = Room::factory()->create();
        $this->from = Carbon::now()->toDateString();
        $this->to = Carbon::now()->addDays(rand(1, 50))->toDateString();
    }

    public function test_submitting_a_create_booking_request_creates_the_provided_booking() {
        $response = $this->postJson('/api/booking', [
            'from' => $this->from,
            'roomId' => $this->room->id,
            'to' => $this->to,
        ]);

        $response->assertStatus(201)
            ->assertExactJson([
                'data' => [
                    'ends_at' => $this->to,
                    'id' => $response->json('data.id'),
                    'room' => [
                        'capacity' => $this->room->capacity,
                        'id' => $this->room->id,
                        'name' => $this->room->name,
                    ],
                    'starts_at' => $this->from,
                ],
            ]);
    }

    /**
     * @dataProvider changeProvider
     * */
    public function test_submitting_a_booking_change_request_alters_the_given_booking(string $from, string $to, bool $changeRoom): void
    {
        $booking = Booking::factory()
            ->for(Room::factory())
            ->create(['starts_at' => $this->from, 'ends_at' => $this->to]);

        $payload = [
            'roomId' => $changeRoom ? Room::factory()->create()->id : $booking->room->id,
            'from' => $from,
            'to' => $to,
        ];

        $this->putJson("api/booking/$booking->id", $payload)
            ->assertStatus(204);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'room_id' => $payload['roomId'],
            'starts_at' => $payload['from'],
            'ends_at' => $payload['to'],
        ]);
    }

    public function changeProvider(): Generator
    {
        yield from [
            'changed room' => [
                'from' => Carbon::now()->toDateString(),
                'to' => Carbon::now()->addDay()->toDateString(),
                'change_room' => true,
            ],
            'changed from' => [
                'from' => Carbon::now()->subDay()->toDateString(),
                'to' => Carbon::now()->addDay()->toDateString(),
                'change_room' => true,
            ],
            'changed to' => [
                'from' => Carbon::now()->toDateString(),
                'to' => Carbon::now()->addDays(3)->toDateString(),
                'change_room' => true,
            ],
        ];
    }
}
