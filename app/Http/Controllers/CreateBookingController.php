<?php

namespace App\Http\Controllers;

use App\Actions\MakeBooking;
use App\Exceptions\CouldNotStoreOccupancy;
use App\Http\Requests\SaveBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class CreateBookingController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param SaveBookingRequest $request
     * @return BookingResource|JsonResponse
     */
    public function __invoke(SaveBookingRequest $request): BookingResource|JsonResponse
    {
        $room = Room::findOrFail($request->roomId);
        $from = Carbon::parse($request->from);
        $to = Carbon::parse($request->to);

        try {
            $booking = (new MakeBooking($from, $room, $to))->submit();

            return new BookingResource($booking);
        } catch (CouldNotStoreOccupancy $e) {
            report($e);

            return response()->json(null, 409);
        }
    }
}
