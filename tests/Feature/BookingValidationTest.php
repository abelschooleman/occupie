<?php

namespace Tests\Feature;

use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BookingValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @dataProvider bookingProvider
     */
    public function test_booking_validation(array $payload, string $key): void
    {
        $response = $this->postJson('api/booking', $payload);

        $response->assertStatus(422)
            ->assertSeeText($key);
    }

    public function test_returns_404_when_room_does_not_exist(): void
    {
        $this->postJson('api/bookings', [
            'roomId' => 98,
            'from' => Carbon::now()->toDateString(),
            'to' => Carbon::now()->addDays(rand(1, 50))->toDateString(),
        ])
            ->assertStatus(404);
    }

    public function bookingProvider(): Generator
    {
        $payload = [
            'roomId' => 1,
            'from' => Carbon::now()->toDateString(),
            'to' => Carbon::now()->addDays(rand(1, 50))->toDateString(),
        ];

        yield from [
            'missing from' => [
                'payload' => Arr::except($payload, 'from'),
                'key' => 'from',
            ],
            'missing to' => [
                'payload' => Arr::except($payload, 'to'),
                'key' => 'to',
            ],
            'missing room id' => [
                'payload' => Arr::except($payload, 'roomId'),
                'key' => 'roomId',
            ],
            'invalid from' => [
                'payload' => [...$payload, ...['from' => 'Not a date']],
                'key' => 'from',
            ],
            'invalid to' => [
                'payload' => [...$payload, ...['to' => 'Not a date']],
                'key' => 'to',
            ],
            'invalid room id' => [
                'payload' => [...$payload, ...['roomId' => 'Not an id']],
                'key' => 'room',
            ],
            'end date before start date' => [
                'payload' => [...$payload, ...['from' => '2023-02-10', 'to' => '2023-02-05']],
                'key' => 'to',
            ],
        ];
    }
}
