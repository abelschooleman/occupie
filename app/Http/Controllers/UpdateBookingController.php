<?php

namespace App\Http\Controllers;

use App\Actions\ChangeBooking;
use App\Exceptions\CouldNotStoreOccupancy;
use App\Http\Requests\SaveBookingRequest;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class UpdateBookingController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param SaveBookingRequest $request
     * @param Booking $booking
     * @return Response
     */
    public function __invoke(SaveBookingRequest $request, Booking $booking): Response
    {
        $room = Room::findOrFail($request->roomId);
        $from = Carbon::parse($request->from);
        $to = Carbon::parse($request->to);

        try {
            (new ChangeBooking($booking, $from, $room, $to))->submit();

            return response()->noContent();
        } catch (CouldNotStoreOccupancy $e) {
            report($e);

            return response(null, 409);
        }
    }
}
