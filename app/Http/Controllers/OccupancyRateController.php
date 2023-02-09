<?php

namespace App\Http\Controllers;

use App\Aggregates\OccupancyAggregator;
use App\Exceptions\CouldNotFindOneOrMoreRequestedRooms;
use App\Models\Room;
use App\Repositories\RoomRepository;
use App\Types\Day;
use App\Types\Month;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class OccupancyRateController extends Controller
{
    /**
     * @param Request $request
     * @param RoomRepository $repository
     * @return JsonResponse|Response
     */
    public function daily(Request $request, RoomRepository $repository): JsonResponse|Response
    {
        try {
            $day = new Day(...explode('-', $request->date));

            $rooms = $repository->get($request->query('room_ids', []));

            return response()->json((new OccupancyAggregator($rooms, $day))->aggregate());
        } catch (CouldNotFindOneOrMoreRequestedRooms $e) {
            report($e);

            return response($e, 404);
        } catch (\Throwable $e) {
            report($e);

            return response('Could not instantiate date or aggregator from input', 409);
        }
    }

    /**
     * @param Request $request
     * @param RoomRepository $repository
     * @return JsonResponse|Response
     */
    public function monthly(Request $request, RoomRepository $repository): JsonResponse|Response
    {
        try {
            $month = new Month(...explode('-', $request->month));

            $rooms = $repository->get($request->query('room_ids', []));

            return response()->json((new OccupancyAggregator($rooms, $month))->aggregate());
        } catch (\Throwable $e) {
            report($e);

            return response('Could not instantiate month or aggregator from input', 409);
        }
    }
}
