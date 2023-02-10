<?php

namespace Tests\Feature;

use App\Http\Middleware\LogRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LogRequestMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Route::post('api/log-request-middleware-test', function () {
            return 'Testing log request middleware';
        })->middleware(LogRequest::class);
    }

    function test_request_data_is_written_to_log_table()
    {
        $data = [
            '::key1::' => '::value1::',
            '::key2::' => '::value2::',
        ];

        $response = $this->postJson('api/log-request-middleware-test', $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('request_logs', ['body' => http_build_query($data)]);
    }

    function test_routes_have_middleware()
    {
        $this->assertRouteUsesMiddleware('booking.create', [LogRequest::class]);
        $this->assertRouteUsesMiddleware('booking.update', [LogRequest::class]);
    }
}
