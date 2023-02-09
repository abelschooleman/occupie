<?php

namespace App\Http\Controllers;

use App\Aggregates\OccupancyAggregator;
use App\Models\Room;
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
     * @return JsonResponse|Response
     */
    public function daily(Request $request): JsonResponse|Response
    {
        try {
            $day = new Day(...explode('-', $request->date));

            $ids = $request->query('room_ids', []);

            return response()->json((new OccupancyAggregator($this->rooms($ids), $day))->aggregate());
        } catch (\Throwable $e) {
            report($e);

            return response('Could not instantiate date or aggregator from input', 409);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function monthly(Request $request): JsonResponse|Response
    {
        try {
            $month = new Month(...explode('-', $request->month));

            $ids = $request->query('room_ids', []);

            return response()->json((new OccupancyAggregator($this->rooms($ids), $month))->aggregate());
        } catch (\Throwable $e) {
            report($e);

            return response('Could not instantiate month or aggregator from input', 409);
        }
    }

    /**
     * @param array $ids
     * @return Collection
     */
    private function rooms(array $ids): Collection
    {
        return !empty($ids) ?
            Room::whereIn('id',$ids)->get() :
            Room::all();
    }
}
